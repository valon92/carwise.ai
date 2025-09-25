<?php

namespace App\Services;

use App\Contracts\AIProviderInterface;
use App\Services\AIProviders\OpenAIProvider;
use App\Services\AIProviders\ClaudeProvider;
use App\Services\AIProviders\GeminiProvider;
use App\Services\AIProviders\CohereProvider;
use App\Services\AIProviders\MistralProvider;
use Illuminate\Support\Facades\Log;

class AIProviderManager
{
    private array $providers = [];
    private string $defaultProvider;

    public function __construct()
    {
        $this->defaultProvider = config('app.default_ai_provider', 'openai');
        $this->initializeProviders();
    }

    private function initializeProviders(): void
    {
        $this->providers = [
            'mistral' => new MistralProvider(),    // Very cheap: ~$0.015 per diagnosis
            'gemini' => new GeminiProvider(),      // Free (if available in your region)
            'openai' => new OpenAIProvider(),      // Professional: ~$0.08 per diagnosis
            'claude' => new ClaudeProvider(),      // Professional: ~$0.06 per diagnosis
            'cohere' => new CohereProvider(),      // Free tier: 1000 requests/month (deprecated models)
        ];
    }

    public function getAvailableProviders(): array
    {
        $available = [];
        foreach ($this->providers as $name => $provider) {
            if ($provider->isAvailable()) {
                $available[$name] = [
                    'name' => $provider->getProviderName(),
                    'available' => true
                ];
            }
        }
        return $available;
    }

    public function getBestProvider(): AIProviderInterface
    {
        // Try providers in order of preference (cheap first, then free, then professional)
        $preferredOrder = ['mistral', 'gemini', 'openai', 'claude', 'cohere'];
        
        foreach ($preferredOrder as $providerName) {
            if (isset($this->providers[$providerName]) && $this->providers[$providerName]->isAvailable()) {
                Log::info("Using AI provider: {$providerName}");
                return $this->providers[$providerName];
            }
        }
        
        throw new \Exception('No AI providers are available');
    }

    public function getProvider(string $name): ?AIProviderInterface
    {
        return $this->providers[$name] ?? null;
    }

    public function getProviderInfo(): array
    {
        $available = $this->getAvailableProviders();
        $bestProvider = null;
        
        try {
            $bestProvider = $this->getBestProvider();
        } catch (\Exception $e) {
            // No providers available
        }
        
        return [
            'available_providers' => $available,
            'best_provider' => $bestProvider ? $bestProvider->getProviderName() : null,
            'default_provider' => $this->defaultProvider,
            'fallback_enabled' => config('app.ai_fallback_enabled', true)
        ];
    }
}
