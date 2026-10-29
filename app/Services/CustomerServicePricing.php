<?php

namespace App\Services;

use App\Models\DiagnosisSession;

class CustomerServicePricing
{
    /**
     * Public catalog totals only (already include platform margin).
     * Never expose raw API costs or the margin rate to clients.
     */
    public function publicPrices(): array
    {
        $costs = config('customer-services.costs');
        $margin = $this->margin();

        return [
            'quick_check' => '0.00',
            'diagnosis' => $this->customerPrice((float) $costs['diagnosis'], $margin),
            'report' => $this->customerPrice((float) $costs['report'], $margin),
            'predictive' => $this->customerPrice((float) $costs['predictive'], $margin),
            'expert' => $this->customerPrice((float) $costs['expert'], $margin),
            'complete' => $this->customerPrice(
                (float) $costs['diagnosis'] + (float) $costs['report'] + (float) $costs['predictive'],
                $margin
            ),
        ];
    }

    /**
     * Fallback quote from uploaded media counts (still margin-applied).
     */
    public function quoteDiagnosis(int $photos, int $videos, bool $audio): string
    {
        $coins = config('customer-services.api_coins');
        $spent = (float) $coins['diagnosis'];
        $spent += max(0, $photos) * (float) $coins['vision'];
        $spent += max(0, $videos) * (float) $coins['video'];

        if ($audio) {
            $spent += (float) $coins['whisper'];
        }

        return $this->customerPrice($spent, $this->margin());
    }

    /**
     * Customer total from measured token/media usage: API spend × (1 + margin).
     */
    public function quoteFromUsage(?array $usage): string
    {
        return $this->customerPrice($this->usageCost($usage), $this->margin());
    }

    /**
     * Preferred quote for a diagnosis session.
     * Uses real usage when present; otherwise media-based coins; never returns 0.00.
     * Returns only the customer-facing total (margin already included).
     */
    public function quoteForSession(DiagnosisSession $session): array
    {
        $meta = is_array($session->ai_response) ? $session->ai_response : [];
        $usage = is_array($meta['usage'] ?? null) ? $meta['usage'] : null;
        $apiCost = $this->usageCost($usage);

        if ($apiCost > 0) {
            $amount = $this->customerPrice($apiCost, $this->margin());
            $source = 'usage';
        } else {
            $session->loadMissing('media');
            $photos = $session->media?->filter(fn ($m) => $m->isImage())->count() ?? 0;
            $videos = $session->media?->filter(fn ($m) => $m->isVideo())->count() ?? 0;
            $audio = ($session->media?->filter(fn ($m) => $m->isAudio())->count() ?? 0) > 0;
            $amount = $this->quoteDiagnosis((int) $photos, (int) $videos, (bool) $audio);
            $apiCost = $this->apiCostFromCustomerAmount($amount);
            $source = 'media_estimate';
        }

        // Absolute floor so unlock is never free by accident
        $min = (float) config('customer-services.min_customer_amount', 0.99);
        if ((float) $amount < $min) {
            $amount = number_format($min, 2, '.', '');
            $apiCost = $this->apiCostFromCustomerAmount($amount);
            $source = 'minimum';
        }

        return [
            'amount' => $amount,
            'currency' => strtoupper((string) config('customer-services.currency', 'eur')),
            // Internal breakdown — store server-side only, never return to the browser.
            '_internal' => [
                'api_cost' => number_format($apiCost, 6, '.', ''),
                'margin' => $this->margin(),
                'source' => $source,
            ],
        ];
    }

    public function costFromUsage(?array $usage): string
    {
        return number_format($this->usageCost($usage), 6, '.', '');
    }

    public function usageCost(?array $usage): float
    {
        if (! is_array($usage)) {
            return 0.0;
        }

        $rate = $this->tokenRate((string) ($usage['model'] ?? 'default'));
        $spent = ((int) ($usage['input_tokens'] ?? 0) / 1000000) * (float) $rate['input'];
        $spent += ((int) ($usage['output_tokens'] ?? 0) / 1000000) * (float) $rate['output'];
        $spent += ((float) ($usage['whisper_seconds'] ?? 0) / 60) * (float) config('customer-services.whisper_per_minute');

        return $spent;
    }

    public function margin(): float
    {
        return (float) config('customer-services.margin', 0.30);
    }

    private function apiCostFromCustomerAmount(string $customerAmount): float
    {
        $margin = $this->margin();
        if ($margin <= -1) {
            return (float) $customerAmount;
        }

        return round(((float) $customerAmount) / (1 + $margin), 6);
    }

    private function tokenRate(string $model): array
    {
        $rates = config('customer-services.token_rates');

        if (isset($rates[$model])) {
            return $rates[$model];
        }

        foreach ($rates as $name => $rate) {
            if ($name !== 'default' && str_starts_with($model, $name)) {
                return $rate;
            }
        }

        return $rates['default'];
    }

    private function customerPrice(float $cost, float $margin): string
    {
        return number_format(round($cost * (1 + $margin), 2), 2, '.', '');
    }
}
