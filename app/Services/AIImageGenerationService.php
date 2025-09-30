<?php

namespace App\Services;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIImageGenerationService
{
    private array $providers = [
        'openai' => [
            'name' => 'OpenAI DALL-E',
            'endpoint' => 'https://api.openai.com/v1/images/generations',
            'model' => 'dall-e-3',
            'enabled' => false
        ],
        'gemini' => [
            'name' => 'Google Gemini',
            'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/imagen-3.0-generate-001:generateImage',
            'model' => 'imagen-3.0-generate-001',
            'enabled' => false
        ],
        'claude' => [
            'name' => 'Anthropic Claude',
            'endpoint' => 'https://api.anthropic.com/v1/messages',
            'model' => 'claude-3-5-sonnet-20241022',
            'enabled' => false
        ]
    ];

    public function __construct()
    {
        $this->initializeProviders();
    }

    /**
     * Initialize AI providers based on environment configuration
     */
    private function initializeProviders(): void
    {
        // OpenAI Configuration
        if (config('services.openai.api_key')) {
            $this->providers['openai']['enabled'] = true;
        }

        // Gemini Configuration
        if (config('services.gemini.api_key')) {
            $this->providers['gemini']['enabled'] = true;
        }

        // Claude Configuration
        if (config('services.claude.api_key')) {
            $this->providers['claude']['enabled'] = true;
        }
    }

    /**
     * Generate car image using AI based on car description
     */
    public function generateCarImage(Car $car, string $provider = 'openai'): ?array
    {
        if (!$this->providers[$provider]['enabled']) {
            Log::warning("AI provider {$provider} is not enabled or configured");
            return null;
        }

        $prompt = $this->generateCarPrompt($car);
        
        try {
            switch ($provider) {
                case 'openai':
                    return $this->generateWithOpenAI($prompt, $car);
                case 'gemini':
                    return $this->generateWithGemini($prompt, $car);
                case 'claude':
                    return $this->generateWithClaude($prompt, $car);
                default:
                    throw new \InvalidArgumentException("Unsupported AI provider: {$provider}");
            }
        } catch (\Exception $e) {
            Log::error("AI image generation failed for car {$car->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate detailed prompt for car image generation
     */
    private function generateCarPrompt(Car $car): string
    {
        $year = $car->year ?? 'modern';
        $color = $car->color ?? 'professional color';
        $bodyType = $this->getBodyTypeFromModel($car->model);
        
        $prompt = "A high-quality, professional photograph of a {$year} {$car->brand} {$car->model} ";
        $prompt .= "in {$color} color. ";
        
        if ($bodyType) {
            $prompt .= "The car is a {$bodyType} body style. ";
        }
        
        $prompt .= "The image should be: ";
        $prompt .= "- Shot from a 3/4 front angle showing the car's best features ";
        $prompt .= "- High resolution and professional quality ";
        $prompt .= "- Clean background or studio setting ";
        $prompt .= "- Well-lit with good contrast ";
        $prompt .= "- Realistic and detailed ";
        $prompt .= "- Suitable for automotive marketing or catalog use ";
        
        // Add specific details based on car brand
        $prompt .= $this->getBrandSpecificDetails($car->brand);
        
        return $prompt;
    }

    /**
     * Get body type from car model
     */
    private function getBodyTypeFromModel(string $model): ?string
    {
        $bodyTypes = [
            'sedan' => ['sedan', 'saloon', '3 series', '5 series', '7 series', 'a4', 'a6', 'a8', 'c-class', 'e-class', 's-class', 'camry', 'accord', 'civic'],
            'hatchback' => ['hatchback', 'golf', 'focus', 'polo', 'fiesta', 'civic hatchback'],
            'suv' => ['suv', 'x3', 'x5', 'x7', 'q5', 'q7', 'q8', 'glc', 'gle', 'gls', 'rav4', 'cr-v', 'pilot'],
            'coupe' => ['coupe', '911', 'cayman', 'boxster', 'z4', 'm4', 'a5', 'tt'],
            'convertible' => ['convertible', 'cabriolet', 'roadster'],
            'wagon' => ['wagon', 'estate', 'touring', 'avant'],
            'pickup' => ['pickup', 'truck', 'f-150', 'silverado', 'ram']
        ];

        $modelLower = strtolower($model);
        
        foreach ($bodyTypes as $type => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($modelLower, $keyword) !== false) {
                    return $type;
                }
            }
        }
        
        return null;
    }

    /**
     * Get brand-specific details for prompt
     */
    private function getBrandSpecificDetails(string $brand): string
    {
        $brandDetails = [
            'BMW' => 'Showcasing BMW\'s signature kidney grille and sporty design language.',
            'Mercedes-Benz' => 'Highlighting Mercedes-Benz\'s elegant three-pointed star and luxury styling.',
            'Audi' => 'Featuring Audi\'s distinctive single-frame grille and quattro design elements.',
            'Porsche' => 'Emphasizing Porsche\'s iconic design heritage and performance aesthetics.',
            'Volkswagen' => 'Showcasing Volkswagen\'s clean, functional German design philosophy.',
            'Toyota' => 'Highlighting Toyota\'s reliable and practical design approach.',
            'Honda' => 'Featuring Honda\'s innovative and efficient design language.',
            'Ford' => 'Showcasing Ford\'s bold American design and blue oval branding.',
            'Tesla' => 'Highlighting Tesla\'s futuristic, minimalist electric vehicle design.',
            'Nissan' => 'Featuring Nissan\'s dynamic and modern design philosophy.'
        ];

        return $brandDetails[$brand] ?? 'Showcasing the brand\'s distinctive design elements.';
    }

    /**
     * Generate image using OpenAI DALL-E
     */
    private function generateWithOpenAI(string $prompt, Car $car): ?array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post($this->providers['openai']['endpoint'], [
            'model' => $this->providers['openai']['model'],
            'prompt' => $prompt,
            'n' => 1,
            'size' => '1024x1024',
            'quality' => 'hd',
            'style' => 'natural'
        ]);

        if (!$response->successful()) {
            throw new \Exception("OpenAI API error: " . $response->body());
        }

        $data = $response->json();
        $imageUrl = $data['data'][0]['url'] ?? null;

        if (!$imageUrl) {
            throw new \Exception("No image URL returned from OpenAI");
        }

        return $this->saveGeneratedImage($imageUrl, $car, 'openai', $prompt);
    }

    /**
     * Generate image using Google Gemini
     */
    private function generateWithGemini(string $prompt, Car $car): ?array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->providers['gemini']['endpoint'] . '?key=' . config('services.gemini.api_key'), [
            'prompt' => $prompt,
            'number_of_images' => 1,
            'aspect_ratio' => 'ASPECT_RATIO_1_1',
            'safety_filter_level' => 'BLOCK_SOME',
            'person_generation' => 'DONT_ALLOW'
        ]);

        if (!$response->successful()) {
            throw new \Exception("Gemini API error: " . $response->body());
        }

        $data = $response->json();
        $imageUrl = $data['generatedImages'][0]['imageUri'] ?? null;

        if (!$imageUrl) {
            throw new \Exception("No image URL returned from Gemini");
        }

        return $this->saveGeneratedImage($imageUrl, $car, 'gemini', $prompt);
    }

    /**
     * Generate image using Anthropic Claude
     */
    private function generateWithClaude(string $prompt, Car $car): ?array
    {
        // Note: Claude doesn't directly generate images, but we can use it for prompt enhancement
        // For now, we'll use it as a fallback or for prompt optimization
        throw new \Exception("Claude image generation not yet implemented");
    }

    /**
     * Save generated image and create CarImage record
     */
    private function saveGeneratedImage(string $imageUrl, Car $car, string $provider, string $prompt): array
    {
        // Download and save the image
        $imageContent = Http::get($imageUrl)->body();
        $filename = 'ai-generated/' . $car->id . '_' . time() . '_' . $provider . '.jpg';
        
        Storage::disk('public')->put($filename, $imageContent);
        $localUrl = Storage::url($filename);

        // Create CarImage record
        $carImage = CarImage::create([
            'car_brand_id' => null, // Will be set based on brand lookup
            'car_model_id' => null, // Will be set based on model lookup
            'year' => $car->year,
            'color' => $car->color,
            'image_url' => $localUrl,
            'thumbnail_url' => $localUrl, // For now, use same URL
            'is_primary' => true,
            'is_3d_model' => false,
            'source' => 'ai_generated',
            'metadata' => [
                'provider' => $provider,
                'prompt' => $prompt,
                'generated_at' => now()->toISOString(),
                'car_id' => $car->id
            ]
        ]);

        return [
            'success' => true,
            'image' => $carImage,
            'provider' => $provider,
            'prompt' => $prompt
        ];
    }

    /**
     * Get available AI providers
     */
    public function getAvailableProviders(): array
    {
        return array_filter($this->providers, function($provider) {
            return $provider['enabled'];
        });
    }

    /**
     * Check if any AI providers are available
     */
    public function hasAvailableProviders(): bool
    {
        return count($this->getAvailableProviders()) > 0;
    }

    /**
     * Generate image for car if no image exists
     */
    public function generateImageIfNeeded(Car $car, string $provider = 'openai'): ?array
    {
        // Check if car already has an image
        $existingImage = $car->getPrimaryImage();
        if ($existingImage && $existingImage->source !== 'ai_generated') {
            return null; // Car already has a real image
        }

        return $this->generateCarImage($car, $provider);
    }
}



