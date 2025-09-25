<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisSession;
use App\Models\DiagnosisMedia;
use App\Models\DiagnosisResult;
use App\Services\AIDiagnosisService;
use App\Jobs\ProcessAIDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DiagnosisController extends Controller
{
    protected $aiService;

    public function __construct(AIDiagnosisService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Start a new diagnosis session.
     */
    public function startDiagnosis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'mileage' => 'nullable|integer|min:0',
            'engine_type' => 'nullable|string|max:255',
            'engine_size' => 'nullable|string|max:255',
            'description' => 'required|string|max:2000',
            'symptoms' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create diagnosis session
            $session = DiagnosisSession::create([
                'session_id' => Str::uuid(),
                'user_id' => auth()->id(),
                'make' => $request->make,
                'model' => $request->model,
                'year' => $request->year,
                'mileage' => $request->mileage,
                'engine_type' => $request->engine_type,
                'engine_size' => $request->engine_size,
                'description' => $request->description,
                'symptoms' => $request->symptoms ?? [],
                'status' => 'processing',
                'created_at' => now()
            ]);

            // Process AI diagnosis synchronously for immediate results
            $this->processWithAI($session);

            return response()->json([
                'success' => true,
                'message' => 'Diagnosis completed successfully',
                'data' => [
                    'session_id' => $session->session_id,
                    'status' => 'completed'
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Diagnosis start failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to start diagnosis'
            ], 500);
        }
    }

    /**
     * Submit a new diagnosis request.
     */
    public function submitDiagnosis(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'mileage' => 'nullable|integer|min:0',
            'engine_type' => 'nullable|string|max:255',
            'engine_size' => 'nullable|string|max:255',
            'description' => 'required|string|max:2000',
            'symptoms' => 'nullable|array',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240', // 10MB max
            'videos' => 'nullable|array|max:2',
            'videos.*' => 'mimes:mp4,avi,mov,wmv,flv,webm|max:51200', // 50MB max
            'audio' => 'nullable|array|max:3',
            'audio.*' => 'mimes:mp3,wav,ogg,m4a,aac|max:20480' // 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create diagnosis session
            $session = DiagnosisSession::create([
                'user_id' => auth()->id(),
                'session_id' => DiagnosisSession::generateSessionId(),
                'make' => $request->make,
                'model' => $request->model,
                'year' => $request->year,
                'mileage' => $request->mileage,
                'engine_type' => $request->engine_type,
                'engine_size' => $request->engine_size,
                'description' => $request->description,
                'symptoms' => is_array($request->symptoms) ? $request->symptoms : ($request->symptoms ? json_decode($request->symptoms, true) : []),
                'status' => 'processing'
            ]);

            // Handle file uploads
            $mediaTypes = ['photos' => 'image', 'videos' => 'video', 'audio' => 'audio'];
            
            foreach ($mediaTypes as $inputName => $fileType) {
                if ($request->hasFile($inputName)) {
                    foreach ($request->file($inputName) as $index => $file) {
                        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                        $filePath = $file->storeAs('diagnosis-media', $fileName, 'public');

                        DiagnosisMedia::create([
                            'diagnosis_session_id' => $session->id,
                            'file_name' => $file->getClientOriginalName(),
                            'file_path' => $filePath,
                            'file_type' => $fileType,
                            'file_size' => $file->getSize(),
                            'mime_type' => $file->getMimeType(),
                            'metadata' => [
                                'original_name' => $file->getClientOriginalName(),
                                'uploaded_at' => now()->toISOString()
                            ]
                        ]);
                    }
                }
            }

            // Process with AI (sync for now)
            try {
                $this->processWithAI($session);
            } catch (\Exception $e) {
                \Log::error('AI processing failed: ' . $e->getMessage());
                // Continue with response even if AI fails
            }

            return response()->json([
                'success' => true,
                'message' => 'Diagnosis submitted successfully',
                'data' => [
                    'session_id' => $session->session_id,
                    'status' => $session->status,
                    'estimated_processing_time' => '2-5 minutes'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit diagnosis',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get diagnosis result by session ID.
     */
    public function getResult(string $sessionId): JsonResponse
    {
        try {
            $session = DiagnosisSession::where('session_id', $sessionId)
                ->with(['result', 'media'])
                ->first();

            if (!$session) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnosis session not found'
                ], 404);
            }

            if ($session->status === 'processing') {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'status' => 'processing',
                        'message' => 'AI analysis in progress...'
                    ]
                ]);
            }

            if ($session->status === 'failed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnosis analysis failed'
                ], 500);
            }

            if (!$session->result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diagnosis result not available'
                ], 404);
            }

            // Check if result needs translation to user's language
            $result = $session->result;
            $userLanguage = auth()->user()?->language ?? 'en';
            
            // If user language is not English and result might be in English, translate it
            if ($userLanguage !== 'en' && $this->needsTranslation($result)) {
                $result = $this->translateExistingResult($result, $userLanguage);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => $session,
                    'result' => $result,
                    'media' => $session->media->map(function ($media) {
                        return [
                            'id' => $media->id,
                            'file_name' => $media->file_name,
                            'url' => $media->url,
                            'type' => $media->file_type,
                            'size' => $media->formatted_size
                        ];
                    })
                ]
            ])->withHeaders([
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve diagnosis result',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's diagnosis history.
     */
    public function getHistory(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $perPage = $request->get('per_page', 10);

            $sessions = DiagnosisSession::where('user_id', $user->id)
                ->with(['result'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $sessions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve diagnosis history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transcribe audio to text.
     */
    public function transcribeAudio(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,aac|max:20480' // 20MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid audio file',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // For now, return a placeholder response
            // In a real implementation, you would integrate with a speech-to-text service
            // like Google Speech-to-Text, Azure Speech Services, or OpenAI Whisper
            
            return response()->json([
                'success' => true,
                'text' => 'Audio transcription feature is coming soon. Please type your description instead.',
                'message' => 'Transcription completed'
            ]);

        } catch (\Exception $e) {
            \Log::error('Audio transcription failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to transcribe audio'
            ], 500);
        }
    }

    /**
     * Process diagnosis with AI service.
     */
    private function processWithAI(DiagnosisSession $session): void
    {
        try {
            // Prepare data for AI analysis
            $analysisData = [
                'vehicle_info' => [
                    'make' => $session->make,
                    'model' => $session->model,
                    'year' => $session->year,
                    'mileage' => $session->mileage,
                    'engine_type' => $session->engine_type,
                    'engine_size' => $session->engine_size
                ],
                'symptoms' => $session->symptoms,
                'description' => $session->description,
                'media_files' => $session->media ? $session->media->pluck('file_path')->toArray() : [],
                'user_currency_id' => auth()->user()?->preferred_currency_id ?? 1,
                'user_language' => auth()->user()?->language ?? 'en'
            ];

            // Call AI service
            $aiResult = $this->aiService->analyzeDiagnosis($analysisData);

            // Create diagnosis result
            DiagnosisResult::create([
                'diagnosis_session_id' => $session->id,
                'problem_title' => $aiResult['problem_title'],
                'problem_description' => $aiResult['problem_description'],
                'severity' => $aiResult['severity'],
                'confidence_score' => $aiResult['confidence_score'],
                'likely_causes' => $aiResult['likely_causes'],
                'recommended_actions' => $aiResult['recommended_actions'],
                'estimated_costs' => $aiResult['estimated_costs'] ?? null,
                'ai_insights' => $aiResult['ai_insights'] ?? null,
                'related_issues' => $aiResult['related_issues'] ?? null,
                'requires_immediate_attention' => $aiResult['requires_immediate_attention'] ?? false,
                'ai_model_version' => $aiResult['model_version'] ?? '1.0',
                'analysis_completed_at' => now()
            ]);

            // Update session status
            $session->update([
                'status' => 'completed',
                'confidence_score' => $aiResult['confidence_score'],
                'severity' => $aiResult['severity'],
                'processed_at' => now()
            ]);

        } catch (\Exception $e) {
            // Update session status to failed
            $session->update([
                'status' => 'failed',
                'ai_response' => ['error' => $e->getMessage()]
            ]);

            \Log::error('AI Diagnosis failed for session: ' . $session->session_id, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Check if a result needs translation (contains English text)
     */
    private function needsTranslation($result): bool
    {
        if (!is_array($result)) {
            return false;
        }

        // Check common English phrases that indicate the result is in English
        $englishPhrases = [
            'Vehicle Diagnostic Required',
            'Based on the symptoms described',
            'Multiple System Analysis',
            'Comprehensive Diagnostic',
            'Preventive Maintenance'
        ];

        $resultJson = json_encode($result);
        foreach ($englishPhrases as $phrase) {
            if (str_contains($resultJson, $phrase)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Translate existing result to user's language
     */
    private function translateExistingResult($result, string $language)
    {
        if (!is_array($result) || $language === 'en') {
            return $result;
        }

        // Use the same translation service as AIDiagnosisService
        $aiService = app(\App\Services\AIDiagnosisService::class);
        
        // Access the private method via reflection (not ideal but works for now)
        $reflection = new \ReflectionClass($aiService);
        $method = $reflection->getMethod('translateResponse');
        $method->setAccessible(true);
        
        return $method->invoke($aiService, $result, $language);
    }
}