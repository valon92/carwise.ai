<?php

namespace App\Services;

use App\Models\DiagnosisSession;
use Illuminate\Support\Facades\Http;

class ApiCreditReport
{
    public function __construct(private CustomerServicePricing $pricing) {}

    public function report(): array
    {
        $users = $this->spendByUser();
        $providers = array_values(array_filter([
            $this->openAi(),
            $this->gemini(),
            $this->cohere(),
        ]));

        foreach ($providers as &$provider) {
            $provider['measured_spend'] = '0.000000';
            $provider['billed_calls'] = 0;
            $provider['unbilled_calls'] = 0;

            foreach ($users as $row) {
                if ($row['provider'] !== $provider['name']) {
                    continue;
                }

                $provider['measured_spend'] = $this->add($provider['measured_spend'], $row['spent']);
                $provider['billed_calls'] += $row['billed_calls'];
                $provider['unbilled_calls'] += $row['unbilled_calls'];
            }
        }

        return [
            'providers' => $providers,
            'users' => $users,
        ];
    }

    public function topUp(string $provider, float $euros): array
    {
        $pages = [
            'OpenAI' => 'https://platform.openai.com/settings/organization/billing/overview',
            'Gemini' => 'https://aistudio.google.com/apikey',
            'Cohere' => 'https://dashboard.cohere.com/billing',
        ];

        if (! isset($pages[$provider]) || ! $this->configured($provider)) {
            abort(422, 'That API is not connected.');
        }

        $rate = $this->euroToUsd();
        $dollars = $rate ? round($euros * $rate['rate'], 2) : null;

        if ($provider === 'OpenAI' && $dollars !== null && $dollars < 5) {
            abort(422, 'OpenAI accepts at least 5 USD.');
        }

        return [
            'provider' => $provider,
            'amount_eur' => number_format($euros, 2, '.', ''),
            'charge_amount' => $dollars === null ? number_format($euros, 2, '.', '') : number_format($dollars, 2, '.', ''),
            'charge_currency' => $dollars === null ? 'EUR' : 'USD',
            'rate' => $rate['rate'] ?? null,
            'rate_date' => $rate['date'] ?? null,
            'billing_url' => $pages[$provider],
        ];
    }

    private function openAi(): ?array
    {
        $key = (string) config('services.openai.api_key');

        if ($key === '') {
            return null;
        }

        $base = rtrim((string) config('services.openai.api_url', 'https://api.openai.com/v1'), '/');
        $grants = Http::withToken($key)->timeout(12)->acceptJson()->get('https://api.openai.com/dashboard/billing/credit_grants');

        if ($grants->successful() && isset($grants->json()['total_available'])) {
            return $this->provider(
                'OpenAI',
                number_format((float) $grants->json()['total_available'], 2, '.', ''),
                true,
                'Remaining balance reported by OpenAI.'
            );
        }

        $probe = Http::withToken($key)->timeout(20)->acceptJson()->post($base.'/chat/completions', [
            'model' => config('services.openai.model', 'gpt-4o'),
            'messages' => [['role' => 'user', 'content' => 'ok']],
            'max_tokens' => 1,
        ]);
        $error = $probe->json('error') ?? [];
        $code = (string) ($error['code'] ?? '');
        $message = (string) ($error['message'] ?? '');

        if ($probe->status() === 429 && ($code === 'credit_balance_exhausted' || str_contains(strtolower($message), 'no credits'))) {
            return $this->provider(
                'OpenAI',
                '0.00',
                true,
                'A live call was rejected: no credits remaining.'
            );
        }

        if ($probe->successful()) {
            return $this->provider(
                'OpenAI',
                null,
                false,
                'Calls are accepted. This key cannot read the remaining dollar balance.'
            );
        }

        return $this->provider(
            'OpenAI',
            null,
            false,
            $this->safeStatus($message, 'OpenAI did not return a credit balance.')
        );
    }

    private function gemini(): ?array
    {
        $key = (string) config('services.gemini.api_key');

        if ($key === '') {
            return null;
        }

        $base = rtrim((string) config('services.gemini.api_url', 'https://generativelanguage.googleapis.com/v1beta'), '/');
        $response = Http::timeout(12)->acceptJson()->get($base.'/models', ['key' => $key]);

        if ($response->successful()) {
            return $this->provider(
                'Gemini',
                null,
                false,
                'The key is accepted. Gemini does not publish a remaining credit balance.'
            );
        }

        return $this->provider(
            'Gemini',
            null,
            false,
            'The key was rejected. No credit balance was returned.'
        );
    }

    private function cohere(): ?array
    {
        $key = (string) config('services.cohere.api_key');

        if ($key === '') {
            return null;
        }

        $base = rtrim((string) config('services.cohere.api_url', 'https://api.cohere.ai/v1'), '/');
        $response = Http::withToken($key)->timeout(12)->acceptJson()->post($base.'/check-api-key');
        $body = $response->json() ?? [];

        foreach (['balance', 'credits', 'remaining_credits', 'available'] as $field) {
            if (isset($body[$field]) && is_numeric($body[$field])) {
                return $this->provider(
                    'Cohere',
                    number_format((float) $body[$field], 2, '.', ''),
                    true,
                    'Remaining balance reported by Cohere.'
                );
            }
        }

        if ($response->successful()) {
            return $this->provider(
                'Cohere',
                null,
                false,
                'The key is accepted. Cohere does not publish a remaining credit balance.'
            );
        }

        return $this->provider(
            'Cohere',
            null,
            false,
            'The key was rejected. No credit balance was returned.'
        );
    }

    private function spendByUser(): array
    {
        $grouped = [];

        DiagnosisSession::query()->with('user:id,name,email')->orderBy('user_id')->each(function (DiagnosisSession $session) use (&$grouped) {
            $usage = is_array($session->ai_response) ? ($session->ai_response['usage'] ?? null) : null;
            $provider = $this->providerFromUsage($usage);
            $userId = $session->user_id;
            $key = ($userId ?? 'guest').'|'.$provider;
            $input = is_array($usage) ? (int) ($usage['input_tokens'] ?? 0) : 0;
            $output = is_array($usage) ? (int) ($usage['output_tokens'] ?? 0) : 0;
            $billed = is_array($usage) && ($input + $output > 0 || (float) ($usage['whisper_seconds'] ?? 0) > 0);

            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'user_id' => $userId,
                    'name' => $session->user->name ?? 'Guest',
                    'email' => $session->user->email ?? '',
                    'provider' => $provider,
                    'billed_calls' => 0,
                    'unbilled_calls' => 0,
                    'input_tokens' => 0,
                    'output_tokens' => 0,
                    'spent' => '0.000000',
                ];
            }

            $grouped[$key]['input_tokens'] += $input;
            $grouped[$key]['output_tokens'] += $output;

            if ($billed) {
                $grouped[$key]['billed_calls']++;
                $grouped[$key]['spent'] = $this->add($grouped[$key]['spent'], $this->pricing->costFromUsage($usage));
            } else {
                $grouped[$key]['unbilled_calls']++;
            }
        });

        $rows = array_values($grouped);
        usort($rows, function (array $left, array $right) {
            return [$left['user_id'] ?? PHP_INT_MAX, $left['provider']] <=> [$right['user_id'] ?? PHP_INT_MAX, $right['provider']];
        });

        return $rows;
    }

    private function providerFromUsage(mixed $usage): string
    {
        $model = strtolower(is_array($usage) ? (string) ($usage['model'] ?? '') : '');

        if (str_contains($model, 'gemini')) {
            return 'Gemini';
        }

        if (str_contains($model, 'command') || str_contains($model, 'cohere')) {
            return 'Cohere';
        }

        return 'OpenAI';
    }

    private function provider(string $name, ?string $remaining, bool $known, string $status): array
    {
        return [
            'name' => $name,
            'remaining' => $remaining,
            'remaining_known' => $known,
            'currency' => 'USD',
            'status' => $status,
        ];
    }

    private function safeStatus(string $message, string $fallback): string
    {
        $message = trim(preg_replace('/\s+/', ' ', $message) ?? '');

        if ($message === '' || str_contains(strtolower($message), 'sk-')) {
            return $fallback;
        }

        return mb_substr($message, 0, 180);
    }

    private function configured(string $provider): bool
    {
        $key = match ($provider) {
            'OpenAI' => config('services.openai.api_key'),
            'Gemini' => config('services.gemini.api_key'),
            'Cohere' => config('services.cohere.api_key'),
            default => '',
        };

        return trim((string) $key) !== '';
    }

    private function euroToUsd(): ?array
    {
        $response = Http::timeout(8)->acceptJson()->get('https://api.frankfurter.app/latest', [
            'from' => 'EUR',
            'to' => 'USD',
        ]);
        $rate = $response->json('rates.USD');

        if (! $response->successful() || ! is_numeric($rate)) {
            return null;
        }

        return [
            'rate' => (float) $rate,
            'date' => (string) $response->json('date'),
        ];
    }

    private function add(string $left, string $right): string
    {
        return number_format((float) $left + (float) $right, 6, '.', '');
    }
}
