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
            'openai' => new OpenAIProvider(),      // Professional: ~$0.08 per diagnosis - PAID FOR
            // Other providers disabled - only OpenAI is paid for
            // 'mistral' => new MistralProvider(),    // Very cheap: ~$0.015 per diagnosis
            // 'gemini' => new GeminiProvider(),      // Free (if available in your region)
            // 'claude' => new ClaudeProvider(),      // Professional: ~$0.06 per diagnosis
            // 'cohere' => new CohereProvider(),      // Free tier: 1000 requests/month (deprecated models)
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
        // Use only OpenAI since it's the only paid provider
        $preferredOrder = ['openai'];
        
        foreach ($preferredOrder as $providerName) {
            if (isset($this->providers[$providerName]) && $this->providers[$providerName]->isAvailable()) {
                Log::info("Using AI provider: {$providerName}");
                return $this->providers[$providerName];
            }
        }
        
        throw new \Exception('No AI providers are available');
    }

    /**
     * Get provider with fallback support
     */
    public function getProviderWithFallback(?string $preferredProvider = null): AIProviderInterface
    {
        // Try preferred provider first
        if ($preferredProvider && isset($this->providers[$preferredProvider])) {
            try {
                if ($this->providers[$preferredProvider]->isAvailable()) {
                    Log::info("Using preferred AI provider: {$preferredProvider}");
                    return $this->providers[$preferredProvider];
                }
            } catch (\Exception $e) {
                Log::warning("Preferred provider {$preferredProvider} failed, falling back", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback to best available provider
        return $this->getBestProvider();
    }

    /**
     * Analyze diagnosis with intelligent provider selection
     */
    public function analyzeDiagnosisIntelligent(array $data, array $options = []): array
    {
        $preferredProvider = $options['preferred_provider'] ?? null;
        $maxRetries = $options['max_retries'] ?? 3;
        $retryCount = 0;
        
        $providerOrder = $this->getIntelligentProviderOrder($data);
        
        foreach ($providerOrder as $providerName) {
            if (!isset($this->providers[$providerName])) {
                continue;
            }
            
            try {
                $provider = $this->providers[$providerName];
                if (!$provider->isAvailable()) {
                    continue;
                }

                Log::info("Attempting diagnosis with provider: {$providerName}");
                $result = $provider->analyzeDiagnosis($data);
                
                // Add provider metadata
                $result['provider_used'] = $providerName;
                $result['retry_count'] = $retryCount;
                
                return $result;
                
            } catch (\Exception $e) {
                $retryCount++;
                Log::warning("Provider {$providerName} failed", [
                    'error' => $e->getMessage(),
                    'retry_count' => $retryCount
                ]);
                
                if ($retryCount >= $maxRetries) {
                    throw new \Exception("All AI providers failed after {$maxRetries} attempts");
                }
                
                continue;
            }
        }
        
        throw new \Exception('No AI providers are available for diagnosis');
    }

    /**
     * Get intelligent provider order based on data characteristics
     */
    private function getIntelligentProviderOrder(array $data): array
    {
        $language = $data['user_language'] ?? 'en';
        $description = $data['description'] ?? $data['problem_description'] ?? '';
        $symptoms = $data['symptoms'] ?? [];
        
        // Start with base preference
        $providerOrder = ['gemini', 'mistral', 'openai', 'claude', 'cohere'];
        
        // Adjust based on language - some providers work better with certain languages
        if ($language === 'sq') {
            // OpenAI and Claude generally better with Albanian
            $providerOrder = ['openai', 'claude', 'gemini', 'mistral', 'cohere'];
        } elseif (in_array($language, ['de', 'fr', 'es', 'pt', 'it'])) {
            // Mistral excels with European languages
            $providerOrder = ['mistral', 'claude', 'gemini', 'openai', 'cohere'];
        }
        
        // Adjust based on complexity
        $isComplex = strlen($description) > 200 || count($symptoms) > 3;
        if ($isComplex) {
            // More powerful models for complex issues
            $providerOrder = ['claude', 'openai', 'gemini', 'mistral', 'cohere'];
        }
        
        // Filter only available providers
        return array_filter($providerOrder, function($provider) {
            return isset($this->providers[$provider]);
        });
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
