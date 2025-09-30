<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Services\AIImageGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AIImageController extends Controller
{
    private AIImageGenerationService $aiImageService;

    public function __construct(AIImageGenerationService $aiImageService)
    {
        $this->aiImageService = $aiImageService;
    }

    /**
     * Generate AI image for a specific car
     */
    public function generateCarImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'provider' => 'sometimes|string|in:openai,gemini,claude',
            'force_regenerate' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $car = Car::where('user_id', Auth::id())
                     ->findOrFail($request->car_id);

            $provider = $request->input('provider', 'openai');
            $forceRegenerate = $request->input('force_regenerate', false);

            // Check if car already has an AI-generated image
            if (!$forceRegenerate) {
                $existingImage = $car->getPrimaryImage();
                if ($existingImage && $existingImage->source === 'ai_generated') {
                    return response()->json([
                        'success' => true,
                        'message' => 'Car already has an AI-generated image',
                        'image' => $existingImage,
                        'regenerated' => false
                    ]);
                }
            }

            // Generate new image
            $result = $this->aiImageService->generateCarImage($car, $provider);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate AI image. Please check your API keys and try again.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'AI image generated successfully',
                'image' => $result['image'],
                'provider' => $result['provider'],
                'prompt' => $result['prompt'],
                'regenerated' => $forceRegenerate
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating AI image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available AI providers
     */
    public function getAvailableProviders(): JsonResponse
    {
        $providers = $this->aiImageService->getAvailableProviders();
        
        return response()->json([
            'success' => true,
            'providers' => $providers,
            'has_providers' => $this->aiImageService->hasAvailableProviders()
        ]);
    }

    /**
     * Generate image for car if needed
     */
    public function generateImageIfNeeded(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id',
            'provider' => 'sometimes|string|in:openai,gemini,claude'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $car = Car::where('user_id', Auth::id())
                     ->findOrFail($request->car_id);

            $provider = $request->input('provider', 'openai');

            $result = $this->aiImageService->generateImageIfNeeded($car, $provider);

            if (!$result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Car already has an image, no generation needed',
                    'image' => null,
                    'generated' => false
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'AI image generated successfully',
                'image' => $result['image'],
                'provider' => $result['provider'],
                'prompt' => $result['prompt'],
                'generated' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating AI image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate images for all user's cars that don't have images
     */
    public function generateImagesForAllCars(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider' => 'sometimes|string|in:openai,gemini,claude',
            'limit' => 'sometimes|integer|min:1|max:10'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $provider = $request->input('provider', 'openai');
            $limit = $request->input('limit', 5);

            $cars = Car::where('user_id', Auth::id())
                      ->whereDoesntHave('images', function($query) {
                          $query->where('source', 'ai_generated');
                      })
                      ->limit($limit)
                      ->get();

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($cars as $car) {
                try {
                    $result = $this->aiImageService->generateImageIfNeeded($car, $provider);
                    if ($result) {
                        $results[] = [
                            'car_id' => $car->id,
                            'car_name' => "{$car->brand} {$car->model}",
                            'success' => true,
                            'image' => $result['image']
                        ];
                        $successCount++;
                    }
                } catch (\Exception $e) {
                    $results[] = [
                        'car_id' => $car->id,
                        'car_name' => "{$car->brand} {$car->model}",
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                    $errorCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Generated images for {$successCount} cars, {$errorCount} errors",
                'results' => $results,
                'summary' => [
                    'total_processed' => count($cars),
                    'successful' => $successCount,
                    'errors' => $errorCount
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating images: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI generation status for a car
     */
    public function getGenerationStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|exists:cars,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $car = Car::where('user_id', Auth::id())
                     ->findOrFail($request->car_id);

            $primaryImage = $car->getPrimaryImage();
            $hasAIImage = $primaryImage && $primaryImage->source === 'ai_generated';
            $hasRealImage = $primaryImage && $primaryImage->source !== 'ai_generated';

            return response()->json([
                'success' => true,
                'car_id' => $car->id,
                'car_name' => "{$car->brand} {$car->model}",
                'has_image' => (bool) $primaryImage,
                'has_ai_image' => $hasAIImage,
                'has_real_image' => $hasRealImage,
                'image_source' => $primaryImage ? $primaryImage->source : null,
                'can_generate' => !$hasRealImage,
                'available_providers' => $this->aiImageService->getAvailableProviders()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting generation status: ' . $e->getMessage()
            ], 500);
        }
    }
}