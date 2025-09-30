<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DiagnosisSession;
use App\Models\DiagnosisMedia;
use App\Models\DiagnosisResult;
use App\Services\AIDiagnosisService;
use App\Services\DiagnosisEnhancementService;
use App\Jobs\ProcessAIDiagnosis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DiagnosisController extends Controller
{
    protected $aiService;
    protected $enhancementService;

    public function __construct(AIDiagnosisService $aiService, DiagnosisEnhancementService $enhancementService)
    {
        $this->aiService = $aiService;
        $this->enhancementService = $enhancementService;
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
            'car_id' => 'nullable|integer|exists:cars,id',
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
                'car_id' => $request->car_id,
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

            // Enhance result with additional data if not already enhanced
            if (!isset($result->suggested_parts_for_purchase) && !isset($result->repair_videos)) {
                $enhancedResult = $this->enhancementService->enhanceDiagnosisResults(
                    $result->toArray(), 
                    $session->make, 
                    $session->model, 
                    $session->year
                );
                $result = (object) array_merge($result->toArray(), $enhancedResult);
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
            $carId = $request->get('car_id');

            $query = DiagnosisSession::where('user_id', $user->id)
                ->with(['result']);

            // Filter by car ID if provided
            if ($carId) {
                $query->where('car_id', $carId);
            }

            $sessions = $query->orderBy('created_at', 'desc')
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
     * Get diagnosis history for a specific car.
     */
    public function getCarDiagnosisHistory(Request $request, $carId): JsonResponse
    {
        try {
            $user = auth()->user();
            $perPage = $request->get('per_page', 10);

            // Verify the car belongs to the user
            $car = \App\Models\Car::where('id', $carId)
                ->where('user_id', $user->id)
                ->first();

            if (!$car) {
                return response()->json([
                    'success' => false,
                    'message' => 'Car not found or unauthorized'
                ], 404);
            }

            $sessions = DiagnosisSession::where('user_id', $user->id)
                ->where('car_id', $carId)
                ->with(['result'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'car' => $car,
                'diagnosis_history' => $sessions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve car diagnosis history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transcribe audio to text using OpenAI Whisper API.
     */
    public function transcribeAudio(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'audio' => 'required|file|mimes:mp3,wav,ogg,m4a,aac,webm|max:25000' // 25MB max (OpenAI limit)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid audio file',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $audioFile = $request->file('audio');
            $user = auth()->user();
            
            // Log the transcription attempt
            \Log::info('Audio transcription started', [
                'user_id' => $user->id,
                'file_size' => $audioFile->getSize(),
                'file_type' => $audioFile->getMimeType()
            ]);

            // Check if OpenAI API key is available
            $openaiApiKey = config('services.openai.api_key');
            if (!$openaiApiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audio transcription service is not configured. Please type your description instead.',
                    'text' => 'Audio transcription service is not available. Please type your description manually.'
                ]);
            }

            // Use OpenAI Whisper API for transcription
            $transcription = $this->transcribeWithOpenAI($audioFile);
            
            if ($transcription) {
                \Log::info('Audio transcription completed', [
                    'user_id' => $user->id,
                    'transcription_length' => strlen($transcription)
                ]);

                return response()->json([
                    'success' => true,
                    'text' => $transcription,
                    'message' => 'Transcription completed successfully'
                ]);
            } else {
                throw new \Exception('Transcription returned empty result');
            }

        } catch (\Exception $e) {
            \Log::error('Audio transcription failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to transcribe audio. Please try again or type your description manually.',
                'text' => 'Transcription failed. Please type your description manually.'
            ], 500);
        }
    }

    /**
     * Transcribe audio using OpenAI Whisper API.
     */
    private function transcribeWithOpenAI($audioFile): ?string
    {
        try {
            $openaiApiKey = config('services.openai.api_key');
            
            // Create a temporary file path
            $tempPath = $audioFile->getRealPath();
            
            // Prepare the cURL request
            $ch = curl_init();
            
            curl_setopt_array($ch, [
                CURLOPT_URL => 'https://api.openai.com/v1/audio/transcriptions',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $openaiApiKey,
                ],
                CURLOPT_POSTFIELDS => [
                    'file' => new \CURLFile($tempPath, $audioFile->getMimeType(), $audioFile->getClientOriginalName()),
                    'model' => 'whisper-1',
                    'language' => 'sq', // Albanian language
                    'response_format' => 'text'
                ],
                CURLOPT_TIMEOUT => 60, // 60 seconds timeout
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                \Log::error('OpenAI Whisper API cURL error', ['error' => $error]);
                return null;
            }
            
            if ($httpCode !== 200) {
                \Log::error('OpenAI Whisper API HTTP error', [
                    'http_code' => $httpCode,
                    'response' => $response
                ]);
                return null;
            }
            
            // Clean up the response
            $transcription = trim($response);
            
            if (empty($transcription)) {
                \Log::warning('OpenAI Whisper API returned empty transcription');
                return null;
            }
            
            return $transcription;
            
        } catch (\Exception $e) {
            \Log::error('OpenAI Whisper transcription error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
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
                'car_brand' => $session->make,
                'car_model' => $session->model,
                'car_year' => $session->year,
                'mileage' => $session->mileage,
                'engine_type' => $session->engine_type,
                'engine_size' => $session->engine_size,
                'symptoms' => $session->symptoms,
                'problem_description' => $session->description,
                'media_files' => $session->media ? $session->media->pluck('file_path')->toArray() : [],
                'user_currency_id' => auth()->user()?->preferred_currency_id ?? 1,
                'user_language' => $this->detectLanguageFromText($session->description, $session->symptoms)
            ];

            // Call AI service
            $aiResult = $this->aiService->analyzeDiagnosis($analysisData);

            // Enhance results with part images, videos, and purchase links
            $aiResult = $this->enhancementService->enhanceDiagnosisResults(
                $aiResult, 
                $session->make, 
                $session->model, 
                $session->year
            );

            // Create diagnosis result
            DiagnosisResult::create([
                'diagnosis_session_id' => $session->id,
                'problem_title' => $aiResult['problem_title'],
                'problem_description' => $aiResult['problem_summary'] ?? $aiResult['primary_diagnosis']['description'] ?? 'No description available',
                'severity' => $aiResult['severity'],
                'confidence_score' => $aiResult['confidence_score'],
                'likely_causes' => $aiResult['secondary_causes'] ?? $aiResult['likely_causes'] ?? [],
                'recommended_actions' => $aiResult['immediate_actions'] ?? $aiResult['recommended_actions'] ?? [],
                'estimated_costs' => $aiResult['repair_estimates'] ?? $aiResult['estimated_costs'] ?? null,
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
     * Detect language from user's text input
     */
    private function detectLanguageFromText(string $description, $symptoms): string
    {
        $text = $description;
        
        // If symptoms is an array, join them
        if (is_array($symptoms)) {
            $text .= ' ' . implode(' ', $symptoms);
        } elseif (is_string($symptoms)) {
            $text .= ' ' . $symptoms;
        }
        
        $text = strtolower(trim($text));
        
        // Global language detection based on common words
        $albanianWords = ['makinë', 'motor', 'problem', 'nuk', 'po', 'është', 'ka', 'me', 'të', 'nga', 'për', 'në', 'me', 'analizo', 'foto', 'dërgo', 'shpjegim', 'shqip', 'shqipe', 'baze', 'fotografis', 'pershkruaj', 'qfare', 'eshte', 'problemi', 'që', 'dhe', 'ose', 'por', 'kur', 'si', 'ku', 'cila', 'cilin', 'cilat', 'cilët'];
        $germanWords = ['auto', 'motor', 'problem', 'nicht', 'ist', 'hat', 'mit', 'von', 'für', 'in', 'das', 'der', 'die', 'und', 'oder', 'wenn', 'wie', 'wo', 'was', 'welche', 'welcher', 'welches'];
        $frenchWords = ['voiture', 'moteur', 'problème', 'ne', 'pas', 'est', 'a', 'avec', 'de', 'pour', 'dans', 'le', 'la', 'et', 'ou', 'quand', 'comment', 'où', 'que', 'quelle', 'quel', 'quels', 'quelles'];
        $spanishWords = ['motor', 'problema', 'ruido', 'potencia', 'coche', 'auto', 'vehículo', 'tiene', 'hace', 'ha', 'perdido', 'mucho', 'el', 'la', 'de', 'en', 'con', 'por', 'para', 'que', 'como', 'cuando', 'donde', 'cual', 'cuales'];
        $italianWords = ['motore', 'problema', 'rumore', 'potenza', 'macchina', 'auto', 'veicolo', 'ha', 'fa', 'perso', 'molto', 'il', 'la', 'di', 'in', 'con', 'per', 'che', 'come', 'quando', 'dove', 'quale', 'quali', 'un', 'una', 'del', 'della', 'dello', 'delle', 'degli', 'delle'];
        $portugueseWords = ['motor', 'problema', 'ruído', 'potência', 'carro', 'auto', 'veículo', 'tem', 'faz', 'perdeu', 'muito', 'o', 'a', 'de', 'em', 'com', 'por', 'para', 'que', 'como', 'quando', 'onde', 'qual', 'quais', 'um', 'uma', 'do', 'da', 'dos', 'das'];
        $russianWords = ['двигатель', 'проблема', 'шум', 'мощность', 'машина', 'авто', 'транспорт', 'имеет', 'делает', 'потерял', 'много', 'и', 'в', 'с', 'для', 'что', 'как', 'когда', 'где', 'какой', 'какие'];
        $chineseWords = ['发动机', '问题', '噪音', '功率', '汽车', '车辆', '有', '做', '失去', '很多', '的', '在', '和', '为', '什么', '怎么', '什么时候', '哪里', '哪个'];
        $japaneseWords = ['エンジン', '問題', '騒音', 'パワー', '車', '自動車', 'ある', 'する', '失った', 'たくさん', 'の', 'で', 'と', 'ために', '何', 'どう', 'いつ', 'どこ', 'どれ'];
        $koreanWords = ['엔진', '문제', '소음', '파워', '자동차', '차량', '있다', '하다', '잃었다', '많이', '의', '에서', '와', '위해', '무엇', '어떻게', '언제', '어디', '어떤'];
        $arabicWords = ['محرك', 'مشكلة', 'ضوضاء', 'قوة', 'سيارة', 'مركبة', 'لديه', 'يفعل', 'فقد', 'كثير', 'من', 'في', 'مع', 'لأجل', 'ماذا', 'كيف', 'متى', 'أين', 'أي'];
        $hindiWords = ['इंजन', 'समस्या', 'शोर', 'शक्ति', 'कार', 'वाहन', 'है', 'करता', 'खोया', 'बहुत', 'का', 'में', 'के', 'के लिए', 'क्या', 'कैसे', 'कब', 'कहाँ', 'कौन'];
        $turkishWords = ['motor', 'sorun', 'gürültü', 'güç', 'araba', 'araç', 'var', 'yapar', 'kaybetti', 'çok', 'nin', 'de', 'ile', 'için', 'ne', 'nasıl', 'ne zaman', 'nerede', 'hangi', 'bir', 've', 'da', 'ya', 'mi', 'mı', 'mu', 'mü'];
        $dutchWords = ['motor', 'probleem', 'geluid', 'vermogen', 'auto', 'voertuig', 'heeft', 'doet', 'verloren', 'veel', 'van', 'in', 'met', 'voor', 'wat', 'hoe', 'wanneer', 'waar', 'welke'];
        $polishWords = ['silnik', 'problem', 'hałas', 'moc', 'samochód', 'pojazd', 'ma', 'robi', 'stracił', 'dużo', 'z', 'w', 'z', 'dla', 'co', 'jak', 'kiedy', 'gdzie', 'który'];
        $swedishWords = ['motor', 'problem', 'ljud', 'kraft', 'bil', 'fordon', 'har', 'gör', 'förlorade', 'mycket', 'av', 'i', 'med', 'för', 'vad', 'hur', 'när', 'var', 'vilken'];
        $norwegianWords = ['motor', 'problem', 'støy', 'kraft', 'bil', 'kjøretøy', 'har', 'gjør', 'mistet', 'mye', 'av', 'i', 'med', 'for', 'hva', 'hvordan', 'når', 'hvor', 'hvilken'];
        $danishWords = ['motor', 'problem', 'støj', 'kraft', 'bil', 'køretøj', 'har', 'gør', 'mistede', 'meget', 'af', 'i', 'med', 'for', 'hvad', 'hvordan', 'hvornår', 'hvor', 'hvilken'];
        $finnishWords = ['moottori', 'ongelma', 'melu', 'teho', 'auto', 'ajoneuvo', 'on', 'tekee', 'menetti', 'paljon', 'ja', 'ssa', 'kanssa', 'varten', 'mitä', 'miten', 'milloin', 'missä', 'mikä'];
        $greekWords = ['κινητήρας', 'πρόβλημα', 'θόρυβος', 'ισχύς', 'αυτοκίνητο', 'όχημα', 'έχει', 'κάνει', 'έχασε', 'πολύ', 'του', 'στο', 'με', 'για', 'τι', 'πώς', 'πότε', 'πού', 'ποιο'];
        $hebrewWords = ['מנוע', 'בעיה', 'רעש', 'כוח', 'מכונית', 'רכב', 'יש', 'עושה', 'איבד', 'הרבה', 'של', 'ב', 'עם', 'עבור', 'מה', 'איך', 'מתי', 'איפה', 'איזה'];
        $thaiWords = ['เครื่องยนต์', 'ปัญหา', 'เสียง', 'กำลัง', 'รถ', 'ยานพาหนะ', 'มี', 'ทำ', 'สูญเสีย', 'มาก', 'ของ', 'ใน', 'กับ', 'สำหรับ', 'อะไร', 'อย่างไร', 'เมื่อไหร่', 'ที่ไหน', 'อันไหน'];
        $vietnameseWords = ['động cơ', 'vấn đề', 'tiếng ồn', 'công suất', 'xe hơi', 'phương tiện', 'có', 'làm', 'mất', 'nhiều', 'của', 'trong', 'với', 'cho', 'gì', 'như thế nào', 'khi nào', 'ở đâu', 'cái nào'];
        
        $albanianCount = 0;
        $germanCount = 0;
        $frenchCount = 0;
        $spanishCount = 0;
        $italianCount = 0;
        $portugueseCount = 0;
        $russianCount = 0;
        $chineseCount = 0;
        $japaneseCount = 0;
        $koreanCount = 0;
        $arabicCount = 0;
        $hindiCount = 0;
        $turkishCount = 0;
        $dutchCount = 0;
        $polishCount = 0;
        $swedishCount = 0;
        $norwegianCount = 0;
        $danishCount = 0;
        $finnishCount = 0;
        $greekCount = 0;
        $hebrewCount = 0;
        $thaiCount = 0;
        $vietnameseCount = 0;
        
        // Count words for each language
        foreach ($albanianWords as $word) {
            if (strpos($text, $word) !== false) {
                $albanianCount++;
            }
        }
        
        foreach ($germanWords as $word) {
            if (strpos($text, $word) !== false) {
                $germanCount++;
            }
        }
        
        foreach ($frenchWords as $word) {
            if (strpos($text, $word) !== false) {
                $frenchCount++;
            }
        }
        
        foreach ($spanishWords as $word) {
            if (strpos($text, $word) !== false) {
                $spanishCount++;
            }
        }
        
        foreach ($italianWords as $word) {
            if (strpos($text, $word) !== false) {
                $italianCount++;
            }
        }
        
        foreach ($portugueseWords as $word) {
            if (strpos($text, $word) !== false) {
                $portugueseCount++;
            }
        }
        
        foreach ($russianWords as $word) {
            if (strpos($text, $word) !== false) {
                $russianCount++;
            }
        }
        
        foreach ($chineseWords as $word) {
            if (strpos($text, $word) !== false) {
                $chineseCount++;
            }
        }
        
        foreach ($japaneseWords as $word) {
            if (strpos($text, $word) !== false) {
                $japaneseCount++;
            }
        }
        
        foreach ($koreanWords as $word) {
            if (strpos($text, $word) !== false) {
                $koreanCount++;
            }
        }
        
        foreach ($arabicWords as $word) {
            if (strpos($text, $word) !== false) {
                $arabicCount++;
            }
        }
        
        foreach ($hindiWords as $word) {
            if (strpos($text, $word) !== false) {
                $hindiCount++;
            }
        }
        
        foreach ($turkishWords as $word) {
            if (strpos($text, $word) !== false) {
                $turkishCount++;
            }
        }
        
        foreach ($dutchWords as $word) {
            if (strpos($text, $word) !== false) {
                $dutchCount++;
            }
        }
        
        foreach ($polishWords as $word) {
            if (strpos($text, $word) !== false) {
                $polishCount++;
            }
        }
        
        foreach ($swedishWords as $word) {
            if (strpos($text, $word) !== false) {
                $swedishCount++;
            }
        }
        
        foreach ($norwegianWords as $word) {
            if (strpos($text, $word) !== false) {
                $norwegianCount++;
            }
        }
        
        foreach ($danishWords as $word) {
            if (strpos($text, $word) !== false) {
                $danishCount++;
            }
        }
        
        foreach ($finnishWords as $word) {
            if (strpos($text, $word) !== false) {
                $finnishCount++;
            }
        }
        
        foreach ($greekWords as $word) {
            if (strpos($text, $word) !== false) {
                $greekCount++;
            }
        }
        
        foreach ($hebrewWords as $word) {
            if (strpos($text, $word) !== false) {
                $hebrewCount++;
            }
        }
        
        foreach ($thaiWords as $word) {
            if (strpos($text, $word) !== false) {
                $thaiCount++;
            }
        }
        
        foreach ($vietnameseWords as $word) {
            if (strpos($text, $word) !== false) {
                $vietnameseCount++;
            }
        }
        
        // Create language mapping array
        $languageCounts = [
            'sq' => $albanianCount,
            'de' => $germanCount,
            'fr' => $frenchCount,
            'es' => $spanishCount,
            'it' => $italianCount,
            'pt' => $portugueseCount,
            'ru' => $russianCount,
            'zh' => $chineseCount,
            'ja' => $japaneseCount,
            'ko' => $koreanCount,
            'ar' => $arabicCount,
            'hi' => $hindiCount,
            'tr' => $turkishCount,
            'nl' => $dutchCount,
            'pl' => $polishCount,
            'sv' => $swedishCount,
            'no' => $norwegianCount,
            'da' => $danishCount,
            'fi' => $finnishCount,
            'el' => $greekCount,
            'he' => $hebrewCount,
            'th' => $thaiCount,
            'vi' => $vietnameseCount
        ];
        
        // Log detection results for debugging
        \Log::info('Language detection', [
            'text' => $text,
            'counts' => $languageCounts
        ]);
        
        // Find the language with the highest count
        $maxCount = max($languageCounts);
        $detectedLanguage = array_search($maxCount, $languageCounts);
        
        if ($maxCount > 0 && $detectedLanguage) {
            $languageNames = [
                'sq' => 'Albanian', 'de' => 'German', 'fr' => 'French', 'es' => 'Spanish',
                'it' => 'Italian', 'pt' => 'Portuguese', 'ru' => 'Russian', 'zh' => 'Chinese',
                'ja' => 'Japanese', 'ko' => 'Korean', 'ar' => 'Arabic', 'hi' => 'Hindi',
                'tr' => 'Turkish', 'nl' => 'Dutch', 'pl' => 'Polish', 'sv' => 'Swedish',
                'no' => 'Norwegian', 'da' => 'Danish', 'fi' => 'Finnish', 'el' => 'Greek',
                'he' => 'Hebrew', 'th' => 'Thai', 'vi' => 'Vietnamese'
            ];
            
            \Log::info('Detected language: ' . ($languageNames[$detectedLanguage] ?? $detectedLanguage));
            return $detectedLanguage;
        }
        
        \Log::info('Detected language: English (default)');
        return 'en';
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