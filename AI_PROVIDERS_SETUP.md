# 🤖 AI Providers Setup Guide

## Overview

CarWise.ai now supports **5 different AI providers** with intelligent switching, fallback support, and cost optimization.

## 🎯 Supported AI Providers

| Provider | Model | Cost | Free Tier | Strengths |
|----------|-------|------|-----------|-----------|
| **Google Gemini** | gemini-1.5-flash | Free | ✅ 1500 requests/day | Fast, multilingual, free |
| **Mistral AI** | mistral-large-latest | ~$0.007/1K tokens | ❌ | European languages, affordable |
| **OpenAI** | gpt-4o | ~$0.03/1K tokens | ❌ | High quality, reliable |
| **Anthropic Claude** | claude-3-5-sonnet | ~$0.024/1K tokens | ❌ | Reasoning, safety |
| **Cohere** | command-r-plus | Free/Paid | ✅ 1000 requests/month | Good for retrieval |

## 🚀 Quick Setup

### 1. Environment Configuration

Add to your `.env` file:

```env
# AI Provider Settings
DEFAULT_AI_PROVIDER=gemini
AI_FALLBACK_ENABLED=true
AI_PROVIDER_PRIORITY=gemini,mistral,openai,claude,cohere

# Google Gemini (Recommended - Free)
GEMINI_API_KEY=your-gemini-api-key-here

# Mistral AI (Cost-effective)
MISTRAL_API_KEY=your-mistral-api-key-here

# OpenAI (High Quality)
OPENAI_API_KEY=sk-your-openai-api-key-here

# Anthropic Claude (Reasoning)
CLAUDE_API_KEY=sk-ant-your-claude-api-key-here

# Cohere (Free Tier)
COHERE_API_KEY=your-cohere-api-key-here
```

### 2. Get API Keys

#### 🆓 **Google Gemini** (Recommended - Free)
1. Go to [Google AI Studio](https://aistudio.google.com/)
2. Click "Get API Key" 
3. Create new project or select existing
4. Generate API key
5. Add to `.env`: `GEMINI_API_KEY=your-key`

#### 💰 **Mistral AI** (Most Cost-Effective)
1. Go to [Mistral Console](https://console.mistral.ai/)
2. Sign up and verify email
3. Go to API Keys section
4. Create new API key
5. Add to `.env`: `MISTRAL_API_KEY=your-key`

#### 🎯 **OpenAI** (Highest Quality)
1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Create account and add payment method
3. Go to API Keys section
4. Create new secret key
5. Add to `.env`: `OPENAI_API_KEY=sk-your-key`

#### 🧠 **Anthropic Claude** (Best Reasoning)
1. Go to [Anthropic Console](https://console.anthropic.com/)
2. Sign up and verify email
3. Go to API Keys section
4. Create new API key
5. Add to `.env`: `CLAUDE_API_KEY=sk-ant-your-key`

#### 🔄 **Cohere** (Free Tier Available)
1. Go to [Cohere Dashboard](https://dashboard.cohere.ai/)
2. Sign up for free account
3. Go to API Keys section
4. Copy your API key
5. Add to `.env`: `COHERE_API_KEY=your-key`

## 🔧 Advanced Configuration

### Provider Priority

Set provider order based on your preferences:

```env
# Cost-optimized (free first)
AI_PROVIDER_PRIORITY=gemini,mistral,cohere,claude,openai

# Quality-optimized (best first)
AI_PROVIDER_PRIORITY=claude,openai,gemini,mistral,cohere

# Balanced approach
AI_PROVIDER_PRIORITY=gemini,mistral,openai,claude,cohere
```

### Language-Specific Optimization

```env
# Optimize for Albanian language
OPTIMIZE_FOR_ALBANIAN=true

# Optimize for European languages
OPTIMIZE_FOR_EUROPEAN_LANGUAGES=true
```

### Cost Management

```env
# Monitor costs
MONITOR_AI_PERFORMANCE=true
AI_COST_ALERT_THRESHOLD=10.00

# Prioritize free tiers
PRIORITIZE_FREE_PROVIDERS=true
```

## 🧠 Intelligent Provider Selection

The system automatically selects the best provider based on:

### 📝 **Language Detection**
- **Albanian**: Prefers OpenAI & Claude (better Albanian support)
- **European Languages**: Prefers Mistral (trained on European data)
- **English**: Uses configured priority order

### 🔍 **Complexity Analysis**
- **Simple Issues**: Uses faster, cheaper providers (Gemini, Mistral)
- **Complex Diagnoses**: Uses more powerful models (Claude, OpenAI)

### 💾 **Fallback System**
- Automatic retry with different providers
- Up to 3 attempts before failure
- Logs provider performance for optimization

## 📊 Provider Comparison

### Cost Analysis (per diagnosis)
```
Gemini:  $0.000 (Free tier)
Mistral: $0.015 
Cohere:  $0.000 (Free tier, limited)
Claude:  $0.060
OpenAI:  $0.080
```

### Response Quality
```
Claude:    ★★★★★ (Excellent reasoning)
OpenAI:    ★★★★★ (Consistently high quality)
Gemini:    ★★★★☆ (Very good, fast)
Mistral:   ★★★★☆ (Good, European languages)
Cohere:    ★★★☆☆ (Good for basic tasks)
```

### Language Support
```
Mistral:   ★★★★★ (Excellent European languages)
OpenAI:    ★★★★★ (Excellent global)
Claude:    ★★★★★ (Excellent global)
Gemini:    ★★★★☆ (Very good multilingual)
Cohere:    ★★★☆☆ (Good English, basic others)
```

## 🔍 Testing & Monitoring

### Test Provider Availability

```bash
# Test all providers
php artisan ai:test-providers

# Test specific provider
php artisan ai:test-provider gemini
```

### Monitor Usage

```bash
# Check provider statistics
php artisan ai:provider-stats

# View recent diagnoses
php artisan ai:diagnosis-log --recent
```

### Provider Health Check

```php
// In your application
$manager = new AIProviderManager();
$available = $manager->getAvailableProviders();
$info = $manager->getProviderInfo();
```

## 🛠️ API Usage Examples

### Basic Diagnosis

```php
// Automatic provider selection
$service = new AIDiagnosisService();
$result = $service->analyzeDiagnosis($data);
```

### Advanced Usage

```php
// Intelligent provider selection with options
$manager = new AIProviderManager();
$result = $manager->analyzeDiagnosisIntelligent($data, [
    'preferred_provider' => 'gemini',
    'max_retries' => 3
]);
```

### Specific Provider

```php
// Use specific provider
$provider = $manager->getProvider('claude');
if ($provider && $provider->isAvailable()) {
    $result = $provider->analyzeDiagnosis($data);
}
```

## 🚨 Troubleshooting

### Common Issues

#### "No AI providers available"
1. Check API keys are correctly set
2. Verify internet connectivity
3. Check provider status pages
4. Test with: `php artisan ai:test-providers`

#### "Provider authentication failed"
1. Verify API key format
2. Check key permissions/quotas
3. Ensure billing is set up (paid providers)

#### "Rate limit exceeded"
1. Wait for rate limit reset
2. Enable fallback to other providers
3. Consider upgrading to paid tiers

### Debug Commands

```bash
# Check configuration
php artisan config:show services.gemini

# Test diagnosis
php artisan ai:test-diagnosis "Engine makes noise"

# View logs
tail -f storage/logs/laravel.log | grep "AI"
```

## 💡 Best Practices

### 🎯 **For Development**
- Use Gemini (free tier)
- Enable all providers for testing
- Monitor logs for errors

### 🚀 **For Production**
- Set up multiple providers for reliability
- Use cost-effective priority order
- Monitor usage and costs
- Set up alerts for failures

### 🔒 **Security**
- Store API keys securely
- Rotate keys regularly
- Monitor for unusual usage
- Use environment-specific keys

### 💰 **Cost Optimization**
- Start with free tiers (Gemini, Cohere)
- Monitor usage patterns
- Adjust provider priority based on costs
- Set up cost alerts

## 📈 Scaling Considerations

### High Volume (1000+ diagnoses/day)
- Prioritize: Gemini → Mistral → Others
- Set up rate limiting
- Consider provider quotas
- Implement caching

### Multi-Region Deployment
- Use region-specific API endpoints
- Consider data residency requirements
- Test latency from each region

### Cost Management at Scale
- Implement usage tracking
- Set daily/monthly budgets
- Use cheaper providers for simple cases
- Cache common responses

## 🔮 Future Enhancements

### Planned Features
- [ ] Response quality scoring
- [ ] Automatic provider performance ranking
- [ ] Cost-based smart routing
- [ ] Custom model fine-tuning
- [ ] Multi-modal support (images)
- [ ] Response caching system

### Provider Roadmap
- [ ] Ollama support (local models)
- [ ] Hugging Face integration
- [ ] Azure OpenAI support
- [ ] AWS Bedrock integration

---

**Last Updated**: September 29, 2025  
**Version**: 1.0  
**Next Review**: October 29, 2025

