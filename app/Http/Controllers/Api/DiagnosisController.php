<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisSession;
use App\Models\DiagnosisMedia;
use App\Models\DiagnosisResult;
use App\Services\AIDiagnosisService;
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
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240' // 10MB max
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
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $file) {
                    $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('diagnosis-media', $fileName, 'public');

                    DiagnosisMedia::create([
                        'diagnosis_session_id' => $session->id,
                        'file_name' => $file->getClientOriginalName(),
                        'file_path' => $filePath,
                        'file_type' => 'image',
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'metadata' => [
                            'original_name' => $file->getClientOriginalName(),
                            'uploaded_at' => now()->toISOString()
                        ]
                    ]);
                }
            }

            // Process with AI (async)
            $this->processWithAI($session);

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

            return response()->json([
                'success' => true,
                'data' => [
                    'session' => $session,
                    'result' => $session->result,
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
                'media_files' => $session->media->pluck('file_path')->toArray()
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
}