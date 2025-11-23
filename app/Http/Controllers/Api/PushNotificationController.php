<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    private $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Register push notification token
     */
    public function registerToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'platform' => 'required|string|in:web,android,ios,windows,mac,linux',
            'user_agent' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Store token in user's notification preferences
            $notificationPreferences = $user->notification_preferences ?? [];
            $notificationPreferences['push_token'] = $request->token;
            $notificationPreferences['push_platform'] = $request->platform;
            $notificationPreferences['push_enabled'] = true;
            $notificationPreferences['push_registered_at'] = now()->toISOString();

            $user->update([
                'notification_preferences' => $notificationPreferences
            ]);

            Log::info('Push notification token registered', [
                'user_id' => $user->id,
                'platform' => $request->platform,
                'token_preview' => substr($request->token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push notification token registered successfully',
                'data' => [
                    'platform' => $request->platform,
                    'registered_at' => $notificationPreferences['push_registered_at']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to register push notification token', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register push notification token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unregister push notification token
     */
    public function unregisterToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Remove token from user's notification preferences
            $notificationPreferences = $user->notification_preferences ?? [];
            unset($notificationPreferences['push_token']);
            unset($notificationPreferences['push_platform']);
            $notificationPreferences['push_enabled'] = false;
            $notificationPreferences['push_unregistered_at'] = now()->toISOString();

            $user->update([
                'notification_preferences' => $notificationPreferences
            ]);

            Log::info('Push notification token unregistered', [
                'user_id' => $user->id,
                'token_preview' => substr($request->token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push notification token unregistered successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to unregister push notification token', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister push notification token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test push notification
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'title' => 'nullable|string|max:100',
            'body' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $title = $request->title ?? 'Test Notification - CarWise.ai';
            $body = $request->body ?? 'This is a test notification to verify push notifications are working correctly.';

            $result = $this->pushNotificationService->sendCustomNotification(
                $request->token,
                $title,
                $body,
                [
                    'type' => 'test',
                    'timestamp' => now()->toISOString(),
                    'url' => config('app.url')
                ]
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test notification'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send test push notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get push notification status
     */
    public function getStatus(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notificationPreferences = $user->notification_preferences ?? [];
            
            $status = $this->pushNotificationService->getStatus();
            $status['user_token_registered'] = !empty($notificationPreferences['push_token']);
            $status['user_push_enabled'] = $notificationPreferences['push_enabled'] ?? false;
            $status['user_platform'] = $notificationPreferences['push_platform'] ?? null;
            $status['user_registered_at'] = $notificationPreferences['push_registered_at'] ?? null;

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get push notification status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get push notification status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'push_enabled' => 'boolean',
            'diagnosis_notifications' => 'boolean',
            'maintenance_reminders' => 'boolean',
            'part_availability' => 'boolean',
            'price_alerts' => 'boolean',
            'appointment_reminders' => 'boolean',
            'marketing_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $notificationPreferences = $user->notification_preferences ?? [];

            // Update preferences
            foreach ($request->all() as $key => $value) {
                if (in_array($key, [
                    'push_enabled',
                    'diagnosis_notifications',
                    'maintenance_reminders',
                    'part_availability',
                    'price_alerts',
                    'appointment_reminders',
                    'marketing_notifications'
                ])) {
                    $notificationPreferences[$key] = $value;
                }
            }

            $notificationPreferences['preferences_updated_at'] = now()->toISOString();

            $user->update([
                'notification_preferences' => $notificationPreferences
            ]);

            Log::info('Push notification preferences updated', [
                'user_id' => $user->id,
                'preferences' => $request->all()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully',
                'data' => $notificationPreferences
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update push notification preferences', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification preferences',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send diagnosis complete notification
     */
    public function sendDiagnosisNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'diagnosis_data' => 'required|array',
            'diagnosis_data.session_id' => 'required|string',
            'diagnosis_data.car_brand' => 'required|string',
            'diagnosis_data.car_model' => 'required|string',
            'diagnosis_data.severity' => 'required|string',
            'diagnosis_data.confidence_score' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushNotificationService->sendDiagnosisCompleteNotification(
                $request->token,
                $request->diagnosis_data
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Diagnosis notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send diagnosis notification'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send diagnosis push notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send diagnosis notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send maintenance reminder notification
     */
    public function sendMaintenanceNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'car_data' => 'required|array',
            'car_data.id' => 'required|integer',
            'car_data.brand' => 'required|string',
            'car_data.model' => 'required|string',
            'car_data.maintenance_type' => 'nullable|string',
            'car_data.estimated_cost' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushNotificationService->sendMaintenanceReminderNotification(
                $request->token,
                $request->car_data
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance reminder sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send maintenance reminder'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send maintenance push notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send maintenance reminder',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends Controller
{
    private $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Register push notification token
     */
    public function registerToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'platform' => 'required|string|in:web,android,ios,windows,mac,linux',
            'user_agent' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Store token in user's notification preferences
            $notificationPreferences = $user->notification_preferences ?? [];
            $notificationPreferences['push_token'] = $request->token;
            $notificationPreferences['push_platform'] = $request->platform;
            $notificationPreferences['push_enabled'] = true;
            $notificationPreferences['push_registered_at'] = now()->toISOString();

            $user->update([
                'notification_preferences' => $notificationPreferences
            ]);

            Log::info('Push notification token registered', [
                'user_id' => $user->id,
                'platform' => $request->platform,
                'token_preview' => substr($request->token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push notification token registered successfully',
                'data' => [
                    'platform' => $request->platform,
                    'registered_at' => $notificationPreferences['push_registered_at']
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to register push notification token', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to register push notification token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unregister push notification token
     */
    public function unregisterToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Remove token from user's notification preferences
            $notificationPreferences = $user->notification_preferences ?? [];
            unset($notificationPreferences['push_token']);
            unset($notificationPreferences['push_platform']);
            $notificationPreferences['push_enabled'] = false;
            $notificationPreferences['push_unregistered_at'] = now()->toISOString();

            $user->update([
                'notification_preferences' => $notificationPreferences
            ]);

            Log::info('Push notification token unregistered', [
                'user_id' => $user->id,
                'token_preview' => substr($request->token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Push notification token unregistered successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to unregister push notification token', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to unregister push notification token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send test push notification
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'title' => 'nullable|string|max:100',
            'body' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $title = $request->title ?? 'Test Notification - CarWise.ai';
            $body = $request->body ?? 'This is a test notification to verify push notifications are working correctly.';

            $result = $this->pushNotificationService->sendCustomNotification(
                $request->token,
                $title,
                $body,
                [
                    'type' => 'test',
                    'timestamp' => now()->toISOString(),
                    'url' => config('app.url')
                ]
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test notification'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send test push notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send test notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get push notification status
     */
    public function getStatus(): JsonResponse
    {
        try {
            $user = Auth::user();
            $notificationPreferences = $user->notification_preferences ?? [];
            
            $status = $this->pushNotificationService->getStatus();
            $status['user_token_registered'] = !empty($notificationPreferences['push_token']);
            $status['user_push_enabled'] = $notificationPreferences['push_enabled'] ?? false;
            $status['user_platform'] = $notificationPreferences['push_platform'] ?? null;
            $status['user_registered_at'] = $notificationPreferences['push_registered_at'] ?? null;

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get push notification status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get push notification status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'push_enabled' => 'boolean',
            'diagnosis_notifications' => 'boolean',
            'maintenance_reminders' => 'boolean',
            'part_availability' => 'boolean',
            'price_alerts' => 'boolean',
            'appointment_reminders' => 'boolean',
            'marketing_notifications' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $notificationPreferences = $user->notification_preferences ?? [];

            // Update preferences
            foreach ($request->all() as $key => $value) {
                if (in_array($key, [
                    'push_enabled',
                    'diagnosis_notifications',
                    'maintenance_reminders',
                    'part_availability',
                    'price_alerts',
                    'appointment_reminders',
                    'marketing_notifications'
                ])) {
                    $notificationPreferences[$key] = $value;
                }
            }

            $notificationPreferences['preferences_updated_at'] = now()->toISOString();

            $user->update([
                'notification_preferences' => $notificationPreferences
            ]);

            Log::info('Push notification preferences updated', [
                'user_id' => $user->id,
                'preferences' => $request->all()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notification preferences updated successfully',
                'data' => $notificationPreferences
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update push notification preferences', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update notification preferences',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send diagnosis complete notification
     */
    public function sendDiagnosisNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'diagnosis_data' => 'required|array',
            'diagnosis_data.session_id' => 'required|string',
            'diagnosis_data.car_brand' => 'required|string',
            'diagnosis_data.car_model' => 'required|string',
            'diagnosis_data.severity' => 'required|string',
            'diagnosis_data.confidence_score' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushNotificationService->sendDiagnosisCompleteNotification(
                $request->token,
                $request->diagnosis_data
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Diagnosis notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send diagnosis notification'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send diagnosis push notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send diagnosis notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send maintenance reminder notification
     */
    public function sendMaintenanceNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|max:500',
            'car_data' => 'required|array',
            'car_data.id' => 'required|integer',
            'car_data.brand' => 'required|string',
            'car_data.model' => 'required|string',
            'car_data.maintenance_type' => 'nullable|string',
            'car_data.estimated_cost' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->pushNotificationService->sendMaintenanceReminderNotification(
                $request->token,
                $request->car_data
            );

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Maintenance reminder sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send maintenance reminder'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send maintenance push notification', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send maintenance reminder',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}














