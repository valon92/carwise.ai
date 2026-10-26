<?php

namespace App\Services;

use App\Models\Car;
use App\Models\DiagnosisSession;
use App\Models\Payment;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserSession;
use Illuminate\Support\Carbon;

class PlatformVisitorReport
{
    public function __construct(private CustomerServicePricing $pricing) {}

    public function report(?Carbon $from = null, ?Carbon $to = null): array
    {
        $now = now();
        $accounts = User::query()->get(['id', 'name', 'email', 'role', 'created_at', 'last_login_at']);
        $allSignIns = UserSession::query()->get(['user_id', 'created_at', 'last_activity_at', 'is_active', 'expires_at']);
        $signIns = $allSignIns->filter(fn (UserSession $session) => $this->inPeriod($session->created_at, $from, $to))->values();
        $views = UserActivityLog::query()->where('activity_type', 'page_view')->orderByDesc('id')->get()
            ->filter(fn (UserActivityLog $view) => $this->inPeriod($view->created_at, $from, $to))->values();
        $requests = DiagnosisSession::query()->with('user:id,name,email')->orderByDesc('id')->get()
            ->filter(fn (DiagnosisSession $session) => $this->inPeriod($session->created_at, $from, $to))->values();
        $registered = $accounts->filter(fn (User $user) => $this->inPeriod($user->created_at, $from, $to))->values();
        $payments = Payment::query()->where('status', 'completed')->get()
            ->filter(fn (Payment $payment) => $this->inPeriod($payment->paid_at ?? $payment->created_at, $from, $to))->values();

        return [
            'period' => [
                'from' => $from?->toDateString(),
                'to' => $to?->toDateString(),
                'label' => $this->periodLabel($from, $to),
            ],
            'visitors' => $this->visitors($signIns, $views),
            'registrations' => $this->registrations($registered),
            'users' => $this->activeUsers($signIns, $views, $requests),
            'sign_ins' => $this->signIns($signIns, $allSignIns, $now),
            'requests' => $this->requests($requests),
            'ledger' => $this->ledger($requests, $payments),
            'vehicles' => Car::query()
                ->when($from, fn ($query) => $query->where('created_at', '>=', $from))
                ->when($to, fn ($query) => $query->where('created_at', '<=', $to))
                ->count(),
            'pages' => $this->pages($views),
            'activity' => UserActivityLog::query()
                ->selectRaw('activity_type, count(*) as total')
                ->groupBy('activity_type')
                ->orderByDesc('total')
                ->get()
                ->map(fn ($row) => [
                    'type' => (string) $row->activity_type,
                    'total' => (int) $row->total,
                ])
                ->values()
                ->all(),
            'recent_registrations' => $registered->sortByDesc('created_at')->take(12)->map(fn (User $user) => [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'registered_at' => optional($user->created_at)->toIso8601String(),
                'last_login_at' => optional($user->last_login_at)->toIso8601String(),
            ])->values()->all(),
            'recent_requests' => $requests->take(12)->map(fn (DiagnosisSession $session) => [
                'user_id' => $session->user_id,
                'name' => $session->user->name ?? 'Guest',
                'vehicle' => trim($session->make.' '.$session->model.' '.$session->year),
                'request' => $this->excerpt((string) $session->description),
                'status' => $session->status,
                'requested_at' => optional($session->created_at)->toIso8601String(),
                'spent' => $this->pricing->costFromUsage($this->usage($session)),
                'collected' => $this->money($this->collectedFor($session, $payments)),
            ])->values()->all(),
            'recent_visits' => $views->take(12)->map(function (UserActivityLog $view) use ($accounts) {
                $account = $accounts->firstWhere('id', $view->user_id);

                return [
                    'user_id' => $view->user_id,
                    'name' => $account->name ?? 'Guest',
                    'path' => (string) ($view->metadata['page'] ?? $view->url),
                    'visited_at' => optional($view->created_at)->toIso8601String(),
                    'device' => $this->device($view),
                ];
            })->values()->all(),
            'spend_by_user' => $this->spendByUser($requests, $payments),
        ];
    }

    private function visitors($signIns, $views): array
    {
        $known = [];

        foreach ($views as $view) {
            $known[$view->user_id ? 'user:'.$view->user_id : 'visitor:'.$view->session_id] = true;
        }

        foreach ($signIns as $session) {
            if ($session->user_id) {
                $known['user:'.$session->user_id] = true;
            }
        }

        $first = UserActivityLog::query()->where('activity_type', 'page_view')->min('created_at');

        return [
            'known' => count($known),
            'anonymous' => $views->whereNull('user_id')->pluck('session_id')->unique()->count(),
            'page_views' => $views->count(),
            'average_minutes' => $this->averageViewMinutes($views),
            'recording_since' => $first ? Carbon::parse($first)->toIso8601String() : null,
        ];
    }

    private function registrations($accounts): array
    {
        return [
            'total' => $accounts->count(),
            'customers' => $accounts->where('role', 'customer')->count(),
            'mechanics' => $accounts->where('role', 'mechanic')->count(),
            'admins' => $accounts->where('role', 'admin')->count(),
        ];
    }

    private function activeUsers($signIns, $views, $requests): array
    {
        $ids = [];

        foreach ($signIns as $session) {
            if ($session->user_id) {
                $ids[$session->user_id] = true;
            }
        }

        foreach ($views as $view) {
            if ($view->user_id) {
                $ids[$view->user_id] = true;
            }
        }

        foreach ($requests as $session) {
            if ($session->user_id) {
                $ids[$session->user_id] = true;
            }
        }

        return ['total' => count($ids)];
    }

    private function signIns($signIns, $allSignIns, Carbon $now): array
    {
        $minutes = $signIns->map(fn (UserSession $session) => $this->minutes($session->created_at, $session->last_activity_at));

        return [
            'total' => $signIns->count(),
            'people' => $signIns->pluck('user_id')->filter()->unique()->count(),
            'average_minutes' => $minutes->isEmpty() ? null : round($minutes->avg(), 1),
            'active_now' => $allSignIns->filter(function (UserSession $session) use ($now) {
                return $session->is_active
                    && (! $session->expires_at || $session->expires_at->isFuture())
                    && $session->last_activity_at
                    && $session->last_activity_at->greaterThan($now->copy()->subMinutes(30));
            })->count(),
        ];
    }

    private function requests($requests): array
    {
        return [
            'total' => $requests->count(),
            'completed' => $requests->where('status', 'completed')->count(),
            'failed' => $requests->where('status', 'failed')->count(),
            'open' => $requests->whereIn('status', ['pending', 'processing'])->count(),
        ];
    }

    private function ledger($requests, $payments): array
    {
        $expense = 0.0;
        $collected = 0.0;
        $billed = 0;
        $paidResults = 0;

        foreach ($requests as $session) {
            $usage = $this->usage($session);
            if ($this->billed($usage)) {
                $billed++;
                $expense += $this->pricing->usageCost($usage);
            }

            $amount = $this->collectedFor($session, $payments);
            if ($amount > 0) {
                $paidResults++;
                $collected += $amount;
            }
        }

        foreach ($payments as $payment) {
            if ($this->linkedToRequest($payment, $requests)) {
                continue;
            }

            $collected += (float) $payment->amount;
        }

        return [
            'expense' => $this->add('0', number_format($expense, 6, '.', '')),
            'collected' => $this->money($collected),
            'left' => $this->money($collected - $expense),
            'billed_calls' => $billed,
            'unbilled_calls' => $requests->count() - $billed,
            'paid_results' => $paidResults,
        ];
    }

    private function spendByUser($requests, $payments): array
    {
        $rows = [];

        foreach ($requests as $session) {
            $key = $session->user_id ?? 'guest';
            if (! isset($rows[$key])) {
                $rows[$key] = [
                    'user_id' => $session->user_id,
                    'name' => $session->user->name ?? 'Guest',
                    'requests' => 0,
                    'expense' => 0.0,
                    'collected' => 0.0,
                ];
            }

            $rows[$key]['requests']++;
            $rows[$key]['expense'] += $this->pricing->usageCost($this->usage($session));
            $rows[$key]['collected'] += $this->collectedFor($session, $payments);
        }

        foreach ($payments as $payment) {
            if ($this->linkedToRequest($payment, $requests)) {
                continue;
            }

            $key = $payment->user_id ?? 'guest';
            if (! isset($rows[$key])) {
                $rows[$key] = [
                    'user_id' => $payment->user_id,
                    'name' => optional($payment->user)->name ?? 'Account',
                    'requests' => 0,
                    'expense' => 0.0,
                    'collected' => 0.0,
                ];
            }

            $rows[$key]['collected'] += (float) $payment->amount;
        }

        return array_values(array_map(function (array $row) {
            $row['expense'] = $this->add('0', number_format($row['expense'], 6, '.', ''));
            $row['left'] = $this->money($row['collected'] - (float) $row['expense']);
            $row['collected'] = $this->money($row['collected']);

            return $row;
        }, $rows));
    }

    private function pages($views): array
    {
        return $views
            ->groupBy(fn (UserActivityLog $view) => (string) ($view->metadata['page'] ?? $view->url))
            ->map(fn ($group, $path) => ['path' => $path, 'views' => $group->count()])
            ->sortByDesc('views')
            ->take(8)
            ->values()
            ->all();
    }

    private function averageViewMinutes($views): ?float
    {
        $spans = $views
            ->groupBy('session_id')
            ->map(function ($group) {
                $times = $group->pluck('created_at')->filter();
                if ($times->count() < 2) {
                    return null;
                }

                return abs($times->min()->diffInSeconds($times->max())) / 60;
            })
            ->filter(fn ($minutes) => $minutes !== null);

        return $spans->isEmpty() ? null : round($spans->avg(), 1);
    }

    private function usage(DiagnosisSession $session): ?array
    {
        $response = $session->ai_response;

        return is_array($response) ? ($response['usage'] ?? null) : null;
    }

    private function billed(?array $usage): bool
    {
        if (! is_array($usage)) {
            return false;
        }

        return ((int) ($usage['input_tokens'] ?? 0) + (int) ($usage['output_tokens'] ?? 0)) > 0
            || (float) ($usage['whisper_seconds'] ?? 0) > 0;
    }

    private function minutes($from, $to): float
    {
        if (! $from || ! $to) {
            return 0.0;
        }

        return round(abs($from->diffInSeconds($to)) / 60, 1);
    }

    private function device(UserActivityLog $view): ?string
    {
        $parts = array_values(array_filter([
            $view->browser && $view->browser !== 'Unknown' ? $view->browser : null,
            $view->operating_system && $view->operating_system !== 'Unknown' ? $view->operating_system : null,
            $view->device_type ? ucfirst($view->device_type) : null,
        ]));

        return $parts === [] ? null : implode(' · ', $parts);
    }

    private function collectedFor(DiagnosisSession $session, $payments): float
    {
        $payment = $payments->first(fn (Payment $row) => (int) $row->related_id === (int) $session->id);

        if ($payment) {
            return (float) $payment->amount;
        }

        $response = $session->ai_response;

        if (! is_array($response) || empty($response['unlocked'])) {
            return 0.0;
        }

        return (float) $this->pricing->quoteFromUsage($this->usage($session));
    }

    private function linkedToRequest(Payment $payment, $requests): bool
    {
        return $requests->contains(fn (DiagnosisSession $session) => (int) $session->id === (int) $payment->related_id);
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

    private function excerpt(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? '');

        return mb_strlen($text) > 90 ? mb_substr($text, 0, 87).'...' : $text;
    }

    private function add(string $left, string $right): string
    {
        return number_format((float) $left + (float) $right, 6, '.', '');
    }
}
