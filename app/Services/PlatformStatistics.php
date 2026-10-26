<?php

namespace App\Services;

use App\Models\AffiliateCommission;
use App\Models\BillingHistory;
use App\Models\DiagnosisSession;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class PlatformStatistics
{
    public function __construct(private CustomerServicePricing $pricing) {}

    public function report(?Carbon $from = null, ?Carbon $to = null): array
    {
        $accounts = User::query()->get(['id', 'name', 'email'])->keyBy('id');
        $requests = DiagnosisSession::query()->orderByDesc('id')->get()
            ->filter(fn (DiagnosisSession $session) => $this->inPeriod($session->created_at, $from, $to))
            ->values();
        $payments = Schema::hasTable('payments')
            ? Payment::query()->where('status', 'completed')->get()
                ->filter(fn (Payment $payment) => $this->inPeriod($payment->paid_at ?? $payment->created_at, $from, $to))
                ->values()
            : collect();
        $orders = Schema::hasTable('orders')
            ? Order::query()->get()
                ->filter(fn (Order $order) => $this->paidOrder($order) && $this->inPeriod($order->created_at, $from, $to))
                ->values()
            : collect();
        $invoices = Schema::hasTable('billing_history')
            ? BillingHistory::query()->where('status', 'paid')->get()
                ->filter(fn (BillingHistory $invoice) => $this->inPeriod($invoice->paid_at ?? $invoice->created_at, $from, $to))
                ->values()
            : collect();
        $commissions = Schema::hasTable('affiliate_commissions')
            ? AffiliateCommission::query()->where('status', 'paid')->get()
                ->filter(fn (AffiliateCommission $row) => $this->inPeriod($row->payment_date ?? $row->created_at, $from, $to))
                ->values()
            : collect();

        $apiUsd = 0.0;
        $billed = 0;
        $paidResults = 0;
        $services = [
            'Diagnosis' => $this->blankService('Diagnosis'),
            'Subscriptions' => $this->blankService('Subscriptions'),
            'Orders' => $this->blankService('Orders'),
            'Other services' => $this->blankService('Other services'),
        ];
        $rows = [];

        foreach ($requests as $session) {
            $usage = is_array($session->ai_response) ? ($session->ai_response['usage'] ?? null) : null;
            $cost = $this->pricing->usageCost(is_array($usage) ? $usage : null);
            $charged = $this->billed($usage);
            if ($charged) {
                $billed++;
                $apiUsd += $cost;
            }

            $services['Diagnosis']['count']++;
            $payment = $this->diagnosisPayment($session, $payments);
            $paid = 0.0;
            if ($payment && $this->currency($payment->currency) !== 'EUR') {
                $services['Diagnosis']['other'] += $this->net($payment);
                $services['Diagnosis']['other_currency'] = $this->currency($payment->currency);
                $paidResults++;
            } else {
                $paid = $this->collectedFor($session, $payments);
                if ($paid > 0) {
                    $paidResults++;
                    $services['Diagnosis']['eur'] += $paid;
                }
            }

            $row = &$this->account($rows, $accounts, $session->user_id);
            $row['requests']++;
            $row['api_usd'] += $cost;
            $row['eur'] += $paid;
            if ($payment && $this->currency($payment->currency) !== 'EUR') {
                $row['other'] += $this->net($payment);
                $row['other_currency'] = $this->currency($payment->currency);
            }
            unset($row);
        }

        foreach ($payments as $payment) {
            if ($this->linkedDiagnosis($payment, $requests)) {
                continue;
            }

            $net = $this->net($payment);
            $service = $this->serviceName((string) $payment->type, (string) $payment->related_type);
            $currency = $this->currency($payment->currency);
            $services[$service]['count']++;
            $services[$service][$currency === 'EUR' ? 'eur' : 'other'] += $currency === 'EUR' ? $net : 0;
            if ($currency !== 'EUR') {
                $services[$service]['other_currency'] = $currency;
                $services[$service]['other'] += $net;
            }

            $row = &$this->account($rows, $accounts, $payment->user_id);
            if ($currency === 'EUR') {
                $row['eur'] += $net;
            } else {
                $row['other'] += $net;
                $row['other_currency'] = $currency;
            }
            unset($row);
        }

        foreach ($orders as $order) {
            if ($payments->contains(fn (Payment $payment) => $payment->related_type === 'order' && (int) $payment->related_id === (int) $order->id)) {
                continue;
            }

            $amount = (float) $order->total_amount;
            $currency = $this->currency($order->currency);
            $services['Orders']['count']++;
            if ($currency === 'EUR') {
                $services['Orders']['eur'] += $amount;
            } else {
                $services['Orders']['other'] += $amount;
                $services['Orders']['other_currency'] = $currency;
            }

            $row = &$this->account($rows, $accounts, $order->user_id);
            if ($currency === 'EUR') {
                $row['eur'] += $amount;
            } else {
                $row['other'] += $amount;
                $row['other_currency'] = $currency;
            }
            unset($row);
        }

        foreach ($invoices as $invoice) {
            if ($payments->contains(fn (Payment $payment) => $payment->related_type === 'subscription' && (int) $payment->related_id === (int) $invoice->subscription_id)) {
                continue;
            }

            $amount = (float) $invoice->amount;
            $currency = $this->currency($invoice->currency);
            $services['Subscriptions']['count']++;
            if ($currency === 'EUR') {
                $services['Subscriptions']['eur'] += $amount;
            } else {
                $services['Subscriptions']['other'] += $amount;
                $services['Subscriptions']['other_currency'] = $currency;
            }

            $subscriptionUser = Schema::hasColumn('billing_history', 'user_id') ? $invoice->user_id : optional($invoice->subscription)->user_id;
            $row = &$this->account($rows, $accounts, $subscriptionUser);
            if ($currency === 'EUR') {
                $row['eur'] += $amount;
            } else {
                $row['other'] += $amount;
                $row['other_currency'] = $currency;
            }
            unset($row);
        }

        $commissionEur = 0.0;
        $commissionOther = 0.0;
        foreach ($commissions as $commission) {
            if ($this->currency($commission->currency) === 'EUR') {
                $commissionEur += (float) $commission->commission_amount;
            } else {
                $commissionOther += (float) $commission->commission_amount;
            }
        }

        $userEur = array_sum(array_column($services, 'eur')) + $commissionEur;
        $otherIncome = array_sum(array_column($services, 'other')) + $commissionOther;
        $rate = ($apiUsd > 0 || $otherIncome > 0) ? $this->usdPerEur() : null;
        $apiEur = $apiUsd > 0 && $rate ? $apiUsd / $rate['rate'] : ($apiUsd > 0 ? null : 0.0);
        $otherEur = $otherIncome > 0 && $rate ? $otherIncome / $rate['rate'] : ($otherIncome > 0 ? null : 0.0);
        $known = $apiEur !== null && $otherEur !== null;

        $accountRows = array_values(array_map(function (array $row) use ($rate) {
            $apiEur = $row['api_usd'] > 0 && $rate ? $row['api_usd'] / $rate['rate'] : ($row['api_usd'] > 0 ? null : 0.0);
            $paidEur = $row['eur'] + ($row['other'] > 0 && $rate ? $row['other'] / $rate['rate'] : ($row['other'] > 0 ? null : 0.0));

            return [
                'user_id' => $row['user_id'],
                'name' => $row['name'],
                'requests' => $row['requests'],
                'api_usd' => $this->precise($row['api_usd']),
                'paid' => $this->money($row['eur']),
                'left' => $apiEur === null || ! is_float($paidEur) ? null : $this->money($paidEur - $apiEur),
            ];
        }, $rows));

        usort($accountRows, fn (array $left, array $right) => ((float) $right['paid']) <=> ((float) $left['paid']));

        return [
            'period' => [
                'from' => $from?->toDateString(),
                'to' => $to?->toDateString(),
                'label' => $this->periodLabel($from, $to),
            ],
            'platform' => [
                'api_usd' => $this->precise($apiUsd),
                'api_eur' => $apiEur === null ? null : $this->money($apiEur),
                'billed_calls' => $billed,
                'unbilled_calls' => $requests->count() - $billed,
                'partner_eur' => $this->money($commissionEur),
            ],
            'users' => [
                'eur' => $this->money($userEur - $commissionEur),
                'payments' => $payments->count() + $orders->count() + $invoices->count(),
                'paid_results' => $paidResults,
                'unpaid_requests' => $requests->count() - $paidResults,
            ],
            'left' => [
                'eur' => $known ? $this->money(($userEur + ($otherEur ?? 0)) - $apiEur) : null,
                'known' => $known,
                'rate' => $rate['rate'] ?? null,
                'rate_date' => $rate['date'] ?? null,
            ],
            'services' => array_values(array_map(fn (array $service) => [
                'name' => $service['name'],
                'count' => $service['count'],
                'from_users' => $this->money($service['eur']),
                'other' => $service['other'] > 0 ? $this->money($service['other']).' '.$service['other_currency'] : null,
            ], $services)),
            'accounts' => $accountRows,
        ];
    }

    private function blankService(string $name): array
    {
        return [
            'name' => $name,
            'count' => 0,
            'eur' => 0.0,
            'other' => 0.0,
            'other_currency' => null,
        ];
    }

    private function &account(array &$rows, $accounts, $userId): array
    {
        $key = $userId ?: 'guest';
        if (! isset($rows[$key])) {
            $account = $userId ? $accounts->get($userId) : null;
            $rows[$key] = [
                'user_id' => $userId,
                'name' => $account->name ?? 'Guest',
                'requests' => 0,
                'api_usd' => 0.0,
                'eur' => 0.0,
                'other' => 0.0,
                'other_currency' => null,
            ];
        }

        return $rows[$key];
    }

    private function serviceName(string $type, string $related): string
    {
        $value = strtolower($type.' '.$related);

        if (str_contains($value, 'diagnos') || str_contains($value, 'report') || str_contains($value, 'predict') || str_contains($value, 'expert')) {
            return 'Diagnosis';
        }

        if (str_contains($value, 'subscription')) {
            return 'Subscriptions';
        }

        if (str_contains($value, 'order')) {
            return 'Orders';
        }

        return 'Other services';
    }

    private function diagnosisPayment(DiagnosisSession $session, $payments): ?Payment
    {
        return $payments->first(fn (Payment $row) => (int) $row->related_id === (int) $session->id
            && in_array($row->related_type, [null, '', 'diagnosis_session', 'diagnosis'], true));
    }

    private function collectedFor(DiagnosisSession $session, $payments): float
    {
        $payment = $this->diagnosisPayment($session, $payments);

        if ($payment && $this->currency($payment->currency) === 'EUR') {
            return $this->net($payment);
        }

        $response = $session->ai_response;

        if (! is_array($response) || empty($response['unlocked'])) {
            return 0.0;
        }

        return (float) $this->pricing->quoteFromUsage(is_array($response) ? ($response['usage'] ?? null) : null);
    }

    private function linkedDiagnosis(Payment $payment, $requests): bool
    {
        if (! in_array($payment->related_type, [null, '', 'diagnosis_session', 'diagnosis'], true)) {
            return false;
        }

        return $requests->contains(fn (DiagnosisSession $session) => (int) $session->id === (int) $payment->related_id);
    }

    private function paidOrder(Order $order): bool
    {
        return in_array(strtolower((string) $order->payment_status), ['paid', 'completed', 'succeeded'], true);
    }

    private function billed(?array $usage): bool
    {
        if (! is_array($usage)) {
            return false;
        }

        return ((int) ($usage['input_tokens'] ?? 0) + (int) ($usage['output_tokens'] ?? 0)) > 0
            || (float) ($usage['whisper_seconds'] ?? 0) > 0;
    }

    private function net(Payment $payment): float
    {
        return max(0, (float) $payment->amount - (float) $payment->refunded_amount);
    }

    private function currency(?string $currency): string
    {
        $code = strtoupper(trim((string) $currency));

        return $code !== '' ? $code : 'EUR';
    }

    private function usdPerEur(): ?array
    {
        $response = Http::timeout(8)->acceptJson()->get('https://api.frankfurter.app/latest', [
            'from' => 'EUR',
            'to' => 'USD',
        ]);
        $rate = $response->json('rates.USD');

        if (! $response->successful() || ! is_numeric($rate) || (float) $rate <= 0) {
            return null;
        }

        return [
            'rate' => (float) $rate,
            'date' => (string) $response->json('date'),
        ];
    }

    private function inPeriod($moment, ?Carbon $from, ?Carbon $to): bool
    {
        if (! $moment) {
            return $from === null && $to === null;
        }

        if ($from && $moment->lt($from)) {
            return false;
        }

        return ! $to || $moment->lte($to);
    }

    private function periodLabel(?Carbon $from, ?Carbon $to): string
    {
        if (! $from && ! $to) {
            return 'All time';
        }

        $start = $from?->timezone('Europe/Belgrade')->format('j M Y');
        $end = $to?->timezone('Europe/Belgrade')->format('j M Y');

        return $start === $end ? $start : trim($start.' – '.$end, ' –');
    }

    private function money(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    private function precise(float $amount): string
    {
        return number_format($amount, 6, '.', '');
    }
}
