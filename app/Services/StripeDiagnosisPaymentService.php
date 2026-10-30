<?php

namespace App\Services;

use App\Models\DiagnosisSession;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeDiagnosisPaymentService
{
    public function __construct(
        private readonly CustomerServicePricing $pricing
    ) {
    }

    public function isConfigured(): bool
    {
        return filled(config('services.stripe.secret'))
            || (bool) config('services.stripe.mock');
    }

    public function publishableKey(): ?string
    {
        $key = config('services.stripe.key');

        return filled($key) ? (string) $key : null;
    }

    public function isMock(): bool
    {
        return (bool) config('services.stripe.mock')
            || (! filled(config('services.stripe.secret')) && app()->environment('local'));
    }

    /**
     * Create (or reuse) a PaymentIntent for unlocking a diagnosis.
     * Client receives only amount + client_secret — never API cost / margin.
     */
    public function createPaymentIntent(DiagnosisSession $session, User $user): array
    {
        $quote = $this->pricing->quoteForSession($session);

        return $this->createQuotedIntent($session, $user, $quote, 'diagnosis_unlock', 'customer_amount');
    }

    /**
     * PaymentIntent for refining an unlocked diagnosis with a live OBD/VCDS code.
     */
    public function createCodeRefinePaymentIntent(DiagnosisSession $session, User $user): array
    {
        $quote = $this->pricing->quoteCodeRefine();

        return $this->createQuotedIntent($session, $user, $quote, 'diagnosis_code_refine', 'code_refine_amount');
    }

    private function createQuotedIntent(
        DiagnosisSession $session,
        User $user,
        array $quote,
        string $type,
        string $amountMetaKey
    ): array {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Stripe is not configured.');
        }

        $amount = $quote['amount'];
        $currency = strtolower($quote['currency']);
        $internal = $quote['_internal'];

        $meta = is_array($session->ai_response) ? $session->ai_response : [];
        $meta[$amountMetaKey] = $amount;
        $meta[$amountMetaKey.'_pricing'] = $internal;
        if ($type === 'diagnosis_unlock') {
            $meta['customer_amount'] = $amount;
            $meta['pricing'] = $internal;
        }
        $session->update(['ai_response' => $meta]);

        if ($this->isMock()) {
            $mockId = 'pi_mock_'.Str::lower(Str::random(24));
            $payment = $this->upsertPayment($session, $user, $mockId, $amount, $currency, 'pending', [
                'mock' => true,
                'api_cost' => $internal['api_cost'],
                'margin' => $internal['margin'],
                'source' => $internal['source'],
                'type' => $type,
            ]);

            return [
                'mock' => true,
                'payment_intent_id' => $mockId,
                'client_secret' => $mockId.'_secret_mock',
                'publishable_key' => null,
                'amount' => $amount,
                'currency' => strtoupper($currency),
                'payment_id' => $payment->payment_id,
                'product' => $type,
            ];
        }

        $this->bootStripe();

        $piMetaKey = $type === 'diagnosis_code_refine' ? 'stripe_code_refine_payment_intent_id' : 'stripe_payment_intent_id';
        $existingPi = $meta[$piMetaKey] ?? null;
        if (is_string($existingPi) && str_starts_with($existingPi, 'pi_')) {
            try {
                $intent = PaymentIntent::retrieve($existingPi);
                if (in_array($intent->status, ['requires_payment_method', 'requires_confirmation', 'requires_action'], true)
                    && (int) $intent->amount === $this->toCents($amount)
                ) {
                    $this->upsertPayment($session, $user, $intent->id, $amount, $currency, 'pending', [
                        'api_cost' => $internal['api_cost'],
                        'margin' => $internal['margin'],
                        'source' => $internal['source'],
                        'type' => $type,
                    ]);

                    return [
                        'mock' => false,
                        'payment_intent_id' => $intent->id,
                        'client_secret' => $intent->client_secret,
                        'publishable_key' => $this->publishableKey(),
                        'amount' => $amount,
                        'currency' => strtoupper($currency),
                        'product' => $type,
                    ];
                }
            } catch (ApiErrorException $e) {
                Log::warning('Stripe reuse PI failed', ['error' => $e->getMessage(), 'type' => $type]);
            }
        }

        $intent = PaymentIntent::create([
            'amount' => $this->toCents($amount),
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true],
            'receipt_email' => $user->email,
            'metadata' => [
                'type' => $type,
                'session_id' => $session->session_id,
                'user_id' => (string) $user->id,
                'api_cost' => $internal['api_cost'],
                'margin' => (string) $internal['margin'],
                'pricing_source' => $internal['source'],
            ],
            'description' => $type === 'diagnosis_code_refine'
                ? 'CarWise.ai OBD code verification · '.$session->session_id
                : 'CarWise.ai diagnosis unlock · '.$session->session_id,
        ]);

        $meta[$piMetaKey] = $intent->id;
        $session->update(['ai_response' => $meta]);

        $this->upsertPayment($session, $user, $intent->id, $amount, $currency, 'pending', [
            'api_cost' => $internal['api_cost'],
            'margin' => $internal['margin'],
            'source' => $internal['source'],
            'type' => $type,
        ]);

        return [
            'mock' => false,
            'payment_intent_id' => $intent->id,
            'client_secret' => $intent->client_secret,
            'publishable_key' => $this->publishableKey(),
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'product' => $type,
        ];
    }

    /**
     * Verify the PaymentIntent succeeded for this session before unlock.
     */
    public function assertPaid(DiagnosisSession $session, User $user, string $paymentIntentId, string $product = 'diagnosis_unlock'): Payment
    {
        $expected = $product === 'diagnosis_code_refine'
            ? (string) (($session->ai_response['code_refine_amount'] ?? null)
                ?: $this->pricing->quoteCodeRefine()['amount'])
            : (string) (($session->ai_response['customer_amount'] ?? null)
                ?: $this->pricing->quoteForSession($session)['amount']);

        if ($this->isMock() && str_starts_with($paymentIntentId, 'pi_mock_')) {
            $payment = $this->upsertPayment(
                $session,
                $user,
                $paymentIntentId,
                $expected,
                strtolower((string) config('customer-services.currency', 'eur')),
                'completed',
                ['mock' => true]
            );
            $payment->markAsCompleted();

            return $payment->fresh();
        }

        $this->bootStripe();

        try {
            $intent = PaymentIntent::retrieve($paymentIntentId);
        } catch (ApiErrorException $e) {
            throw new \RuntimeException('Payment could not be verified: '.$e->getMessage());
        }

        if (($intent->metadata['session_id'] ?? null) !== $session->session_id) {
            throw new \RuntimeException('Payment does not match this diagnosis.');
        }

        if ((int) ($intent->metadata['user_id'] ?? 0) !== (int) $user->id) {
            throw new \RuntimeException('Payment does not match this account.');
        }

        $intentType = (string) ($intent->metadata['type'] ?? 'diagnosis_unlock');
        if ($intentType !== $product) {
            throw new \RuntimeException('Payment product does not match this action.');
        }

        if ($intent->status !== 'succeeded') {
            throw new \RuntimeException('Payment is not completed yet.');
        }

        if ((int) $intent->amount !== $this->toCents($expected)) {
            throw new \RuntimeException('Payment amount does not match the diagnosis total.');
        }

        $payment = $this->upsertPayment(
            $session,
            $user,
            $intent->id,
            $expected,
            strtolower((string) $intent->currency),
            'completed',
            [
                'stripe_status' => $intent->status,
            ],
            $intent->toArray()
        );
        $payment->markAsCompleted();

        return $payment->fresh();
    }

    public function handleWebhook(string $payload, ?string $signature): void
    {
        $secret = config('services.stripe.webhook_secret');
        if (! filled($secret)) {
            throw new \RuntimeException('Stripe webhook secret is not configured.');
        }

        $event = Webhook::constructEvent($payload, (string) $signature, $secret);

        if ($event->type !== 'payment_intent.succeeded') {
            return;
        }

        /** @var PaymentIntent $intent */
        $intent = $event->data->object;
        $sessionId = $intent->metadata['session_id'] ?? null;
        $userId = $intent->metadata['user_id'] ?? null;

        if (! $sessionId || ! $userId) {
            return;
        }

        $session = DiagnosisSession::where('session_id', $sessionId)->first();
        $user = User::find($userId);
        if (! $session || ! $user) {
            return;
        }

        $amount = number_format(((int) $intent->amount) / 100, 2, '.', '');
        $payment = $this->upsertPayment(
            $session,
            $user,
            $intent->id,
            $amount,
            strtolower((string) $intent->currency),
            'completed',
            ['via' => 'webhook'],
            $intent->toArray()
        );
        $payment->markAsCompleted();

        $meta = is_array($session->ai_response) ? $session->ai_response : [];
        if (! ($meta['unlocked'] ?? false)) {
            $meta['unlocked'] = true;
            $meta['unlocked_at'] = now()->toIso8601String();
            $meta['stripe_payment_intent_id'] = $intent->id;
            $meta['customer_amount'] = $amount;
            $session->update(['ai_response' => $meta]);
        }
    }

    private function upsertPayment(
        DiagnosisSession $session,
        User $user,
        string $providerPaymentId,
        string $amount,
        string $currency,
        string $status,
        array $metadata = [],
        ?array $providerData = null
    ): Payment {
        $payment = Payment::query()
            ->where('provider', 'stripe')
            ->where('payment_id', $providerPaymentId)
            ->first();

        $payload = [
            'user_id' => $user->id,
            'payment_id' => $providerPaymentId,
            'provider' => 'stripe',
            'type' => 'diagnosis',
            'status' => $status,
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'description' => 'Diagnosis unlock '.$session->session_id,
            'metadata' => array_merge([
                'session_id' => $session->session_id,
            ], $metadata),
            'related_type' => DiagnosisSession::class,
            'related_id' => $session->id,
            'payment_method' => 'card',
            'provider_transaction_id' => $providerPaymentId,
            'provider_data' => $providerData,
        ];

        if ($payment) {
            $payment->fill($payload);
            $payment->save();

            return $payment;
        }

        return Payment::create($payload);
    }

    private function bootStripe(): void
    {
        $secret = config('services.stripe.secret');
        if (! filled($secret)) {
            throw new \RuntimeException('Stripe secret key is missing.');
        }
        Stripe::setApiKey($secret);
    }

    private function toCents(string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }
}
