<?php

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionUsage;
use App\Models\BillingHistory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SubscriptionService
{
    public function getAvailablePlans(): array
    {
        return [
            'basic' => [
                'id' => 'basic',
                'name' => 'Basic',
                'description' => 'Perfect for occasional car owners',
                'price' => 4.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 1,
                'features' => [
                    'basic_diagnosis',
                    'email_support',
                    'basic_reports',
                    'vehicle_management',
                ],
                'limits' => [
                    'vehicles' => 1,
                    'diagnoses_per_month' => 1,
                    'storage' => '100MB',
                    'api_calls_per_day' => 10,
                ],
                'popular' => false,
            ],
            'pro' => [
                'id' => 'pro',
                'name' => 'Pro',
                'description' => 'Ideal for car enthusiasts and regular users',
                'price' => 9.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 3,
                'features' => [
                    'ai_reports',
                    'service_offers',
                    'priority_support',
                    'advanced_analytics',
                    'parts_recommendations',
                    'maintenance_reminders',
                ],
                'limits' => [
                    'vehicles' => 3,
                    'diagnoses_per_month' => 3,
                    'storage' => '1GB',
                    'api_calls_per_day' => 50,
                ],
                'popular' => true,
            ],
            'elite' => [
                'id' => 'elite',
                'name' => 'Elite',
                'description' => 'For professionals and fleet managers',
                'price' => 19.99,
                'currency' => 'EUR',
                'billing_cycle' => 'monthly',
                'diagnoses_per_month' => 'unlimited',
                'features' => [
                    'continuous_monitoring',
                    'ai_advice',
                    'preventive_care',
                    'white_label_reports',
                    'api_access',
                    'custom_integrations',
                    'dedicated_support',
                ],
                'limits' => [
                    'vehicles' => 'unlimited',
                    'diagnoses_per_month' => 'unlimited',
                    'storage' => '10GB',
                    'api_calls_per_day' => 'unlimited',
                ],
                'popular' => false,
            ],
        ];
    }

    public function createSubscription(int $userId, string $planId, array $paymentData = []): array
    {
        try {
            // 1. Validate plan
            $plans = $this->getAvailablePlans();
            if (!isset($plans[$planId])) {
                throw new \InvalidArgumentException("Plan {$planId} not found");
            }

            $plan = $plans[$planId];

            // 2. Check if user already has active subscription
            $existingSubscription = Subscription::where('user_id', $userId)
                ->whereIn('status', ['active', 'trial'])
                ->first();

            if ($existingSubscription) {
                throw new \Exception('User already has an active subscription');
            }

            // 3. Create subscription record
            $subscription = Subscription::create([
                'user_id' => $userId,
                'plan_id' => $planId,
                'status' => 'trial',
                'billing_cycle' => $plan['billing_cycle'],
                'price' => $plan['price'],
                'currency' => $plan['currency'],
                'next_billing_date' => $this->calculateNextBillingDate($plan['billing_cycle']),
                'trial_ends_at' => $this->getTrialEndDate(),
            ]);

            // 4. Update user subscription info
            $this->updateUserSubscriptionInfo($userId, $plan);

            // 5. Track analytics
            $this->trackSubscriptionEvent('subscription_created', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'price' => $plan['price'],
                'billing_cycle' => $plan['billing_cycle'],
            ]);

            Log::info('Subscription created successfully', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'subscription_id' => $subscription->id,
            ]);

            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'plan' => $plan,
                'status' => 'trial',
                'next_billing_date' => $subscription->next_billing_date,
                'trial_ends_at' => $subscription->trial_ends_at,
            ];

        } catch (\Exception $e) {
            Log::error('Subscription creation failed', [
                'user_id' => $userId,
                'plan_id' => $planId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function cancelSubscription(int $userId, string $reason = null): array
    {
        try {
            $subscription = Subscription::where('user_id', $userId)
                ->whereIn('status', ['active', 'trial'])
                ->first();

            if (!$subscription) {
                throw new \Exception('No active subscription found');
            }

            // Update subscription status
            $subscription->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason,
            ]);

            // Reset user to basic features
            $this->resetUserToBasicFeatures($userId);

            // Track analytics
            $this->trackSubscriptionEvent('subscription_cancelled', [
                'user_id' => $userId,
                'plan_id' => $subscription->plan_id,
                'reason' => $reason,
                'duration' => $subscription->created_at->diffInDays(now()),
            ]);

            return [
                'success' => true,
                'subscription_id' => $subscription->id,
                'status' => 'cancelled',
                'cancelled_at' => $subscription->cancelled_at,
            ];

        } catch (\Exception $e) {
            Log::error('Subscription cancellation failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function changePlan(int $userId, string $newPlanId): array
    {
        try {
            $currentSubscription = Subscription::where('user_id', $userId)
                ->whereIn('status', ['active', 'trial'])
                ->first();

            if (!$currentSubscription) {
                throw new \Exception('No active subscription found');
            }

            $plans = $this->getAvailablePlans();
            $newPlan = $plans[$newPlanId];
            $currentPlan = $plans[$currentSubscription->plan_id];

            // Update subscription
            $currentSubscription->update([
                'plan_id' => $newPlanId,
                'price' => $newPlan['price'],
            ]);

            // Update user features
            $this->updateUserSubscriptionInfo($userId, $newPlan);

            // Track analytics
            $this->trackSubscriptionEvent('subscription_plan_changed', [
                'user_id' => $userId,
                'old_plan' => $currentSubscription->plan_id,
                'new_plan' => $newPlanId,
            ]);

            return [
                'success' => true,
                'subscription_id' => $currentSubscription->id,
                'new_plan' => $newPlan,
            ];

        } catch (\Exception $e) {
            Log::error('Plan change failed', [
                'user_id' => $userId,
                'new_plan' => $newPlanId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function checkUsageLimits(int $userId, string $action): array
    {
        $subscription = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$subscription) {
            return [
                'allowed' => false,
                'reason' => 'No active subscription',
                'upgrade_required' => true,
            ];
        }

        $plans = $this->getAvailablePlans();
        $plan = $plans[$subscription->plan_id];

        switch ($action) {
            case 'diagnosis':
                if ($plan['diagnoses_per_month'] === 'unlimited') {
                    return ['allowed' => true];
                }

                $usage = $this->getMonthlyUsage($userId, 'diagnosis');
                $remaining = $plan['diagnoses_per_month'] - $usage;

                return [
                    'allowed' => $remaining > 0,
                    'usage' => $usage,
                    'limit' => $plan['diagnoses_per_month'],
                    'remaining' => $remaining,
                    'upgrade_required' => $remaining <= 0,
                ];

            case 'vehicle_add':
                if ($plan['limits']['vehicles'] === 'unlimited') {
                    return ['allowed' => true];
                }

                $vehicleCount = \App\Models\Vehicle::where('user_id', $userId)->count();
                $remaining = $plan['limits']['vehicles'] - $vehicleCount;

                return [
                    'allowed' => $remaining > 0,
                    'usage' => $vehicleCount,
                    'limit' => $plan['limits']['vehicles'],
                    'remaining' => $remaining,
                    'upgrade_required' => $remaining <= 0,
                ];

            default:
                return ['allowed' => true];
        }
    }

    public function getSubscriptionStatus(int $userId): array
    {
        $subscription = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$subscription) {
            return [
                'has_subscription' => false,
                'plan' => null,
                'usage' => null,
            ];
        }

        $plans = $this->getAvailablePlans();
        $plan = $plans[$subscription->plan_id];

        $usage = [
            'diagnoses' => $this->getMonthlyUsage($userId, 'diagnosis'),
            'vehicles' => \App\Models\Vehicle::where('user_id', $userId)->count(),
            'api_calls' => $this->getDailyUsage($userId, 'api_call'),
        ];

        return [
            'has_subscription' => true,
            'subscription_id' => $subscription->id,
            'plan' => $plan,
            'status' => $subscription->status,
            'next_billing_date' => $subscription->next_billing_date,
            'trial_ends_at' => $subscription->trial_ends_at,
            'usage' => $usage,
            'limits' => $plan['limits'],
        ];
    }

    public function recordUsage(int $userId, string $actionType, int $count = 1, array $metadata = null): void
    {
        $subscription = Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if ($subscription) {
            $subscription->recordUsage($actionType, $count, $metadata);
        }
    }

    private function calculateNextBillingDate(string $billingCycle): \DateTime
    {
        $now = now();

        switch ($billingCycle) {
            case 'monthly':
                return $now->addMonth();
            case 'yearly':
                return $now->addYear();
            default:
                return $now->addMonth();
        }
    }

    private function getTrialEndDate(): \DateTime
    {
        return now()->addDays(7);
    }

    private function updateUserSubscriptionInfo(int $userId, array $plan): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->update([
                'subscription_plan' => $plan['id'],
                'subscription_status' => 'active',
            ]);
        }
    }

    private function resetUserToBasicFeatures(int $userId): void
    {
        $user = User::find($userId);
        if ($user) {
            $user->update([
                'subscription_plan' => 'basic',
                'subscription_status' => 'cancelled',
            ]);
        }
    }

    private function getMonthlyUsage(int $userId, string $action): int
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return SubscriptionUsage::where('user_id', $userId)
            ->where('action_type', $action)
            ->whereBetween('usage_date', [$startOfMonth, $endOfMonth])
            ->sum('usage_count');
    }

    private function getDailyUsage(int $userId, string $action): int
    {
        $today = now()->startOfDay();

        return SubscriptionUsage::where('user_id', $userId)
            ->where('action_type', $action)
            ->where('usage_date', $today)
            ->sum('usage_count');
    }

    private function trackSubscriptionEvent(string $event, array $data): void
    {
        // Track with analytics service if available
        if (class_exists('App\Services\AnalyticsService')) {
            app('App\Services\AnalyticsService')->trackEvent($event, $data);
        }

        Log::info("Subscription event: {$event}", $data);
    }
}

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionUsage;
use App\Models\BillingHistory;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;







