<?php

namespace App\Services;

class CustomerServicePricing
{
    public function publicPrices(): array
    {
        $costs = config('customer-services.costs');
        $margin = (float) config('customer-services.margin');

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

    public function quoteDiagnosis(int $photos, int $videos, bool $audio): string
    {
        $coins = config('customer-services.api_coins');
        $spent = (float) $coins['diagnosis'];
        $spent += max(0, $photos) * (float) $coins['vision'];
        $spent += max(0, $videos) * (float) $coins['video'];

        if ($audio) {
            $spent += (float) $coins['whisper'];
        }

        return $this->customerPrice($spent, (float) config('customer-services.margin'));
    }

    public function quoteFromUsage(?array $usage): string
    {
        $spent = 0.0;

        if (is_array($usage)) {
            $rate = $this->tokenRate((string) ($usage['model'] ?? 'default'));
            $spent += ((int) ($usage['input_tokens'] ?? 0) / 1000000) * (float) $rate['input'];
            $spent += ((int) ($usage['output_tokens'] ?? 0) / 1000000) * (float) $rate['output'];
            $spent += ((float) ($usage['whisper_seconds'] ?? 0) / 60) * (float) config('customer-services.whisper_per_minute');
        }

        return $this->customerPrice($spent, (float) config('customer-services.margin'));
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
