<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubscriptionController extends Controller
{
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Get available subscription plans
     */
    public function getPlans(): JsonResponse
    {
        try {
            $plans = $this->subscriptionService->getAvailablePlans();

            return response()->json([
                'success' => true,
                'plans' => $plans,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's subscription status
     */
    public function getStatus(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $status = $this->subscriptionService->getSubscriptionStatus($userId);

            return response()->json([
                'success' => true,
                'subscription' => $status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new subscription
     */
    public function subscribe(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'plan_id' => 'required|string|in:basic,pro,elite',
            ]);

            $userId = $request->user()->id;
            $result = $this->subscriptionService->createSubscription(
                $userId,
                $request->plan_id,
                $request->all()
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'subscription' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change subscription plan
     */
    public function changePlan(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'new_plan_id' => 'required|string|in:basic,pro,elite',
            ]);

            $userId = $request->user()->id;
            $result = $this->subscriptionService->changePlan($userId, $request->new_plan_id);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'subscription' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'nullable|string|max:500',
            ]);

            $userId = $request->user()->id;
            $result = $this->subscriptionService->cancelSubscription($userId, $request->reason);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'subscription' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error'],
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get usage limits for current subscription
     */
    public function getUsageLimits(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $action = $request->query('action', 'diagnosis');

            $limits = $this->subscriptionService->checkUsageLimits($userId, $action);

            return response()->json([
                'success' => true,
                'limits' => $limits,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Record usage for a specific action
     */
    public function recordUsage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'action_type' => 'required|string|in:diagnosis,api_call,storage_upload,vehicle_add',
                'count' => 'integer|min:1|max:100',
                'metadata' => 'nullable|array',
            ]);

            $userId = $request->user()->id;
            $this->subscriptionService->recordUsage(
                $userId,
                $request->action_type,
                $request->count ?? 1,
                $request->metadata
            );

            return response()->json([
                'success' => true,
                'message' => 'Usage recorded successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get billing history
     */
    public function getBillingHistory(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $subscription = \App\Models\Subscription::where('user_id', $userId)
                ->whereIn('status', ['active', 'trial'])
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => false,
                    'error' => 'No active subscription found',
                ], 404);
            }

            $billingHistory = \App\Models\BillingHistory::where('subscription_id', $subscription->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'billing_history' => $billingHistory,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check if user has access to a specific feature
     */
    public function checkFeatureAccess(Request $request, string $feature): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $subscription = \App\Models\Subscription::where('user_id', $userId)
                ->whereIn('status', ['active', 'trial'])
                ->first();

            if (!$subscription) {
                return response()->json([
                    'success' => true,
                    'feature' => $feature,
                    'has_access' => false,
                    'reason' => 'No active subscription',
                ]);
            }

            $plans = $this->subscriptionService->getAvailablePlans();
            $plan = $plans[$subscription->plan_id];
            $hasAccess = in_array($feature, $plan['features'] ?? []);

            return response()->json([
                'success' => true,
                'feature' => $feature,
                'has_access' => $hasAccess,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;







