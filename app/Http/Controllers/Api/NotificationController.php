<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get user's notifications.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = $user->notifications();

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->has('unread_only') && $request->unread_only) {
            $query->whereNull('read_at');
        }

        // Pagination
        $perPage = $request->input('per_page', 20);
        $notifications = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'pagination' => [
                'current_page' => $notifications->currentPage(),
                'total_pages' => $notifications->lastPage(),
                'total_count' => $notifications->total(),
                'per_page' => $notifications->perPage()
            ]
        ]);
    }

    /**
     * Mark notification as read.
     *
     * @param string $notificationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(string $notificationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->where('id', $notificationId)->first();
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user->unreadNotifications->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification.
     *
     * @param string $notificationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(string $notificationId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $notification = $user->notifications()->where('id', $notificationId)->first();
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Clear all notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearAll()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $user->notifications()->delete();

        return response()->json([
            'success' => true,
            'message' => 'All notifications cleared'
        ]);
    }

    /**
     * Get notification statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $totalNotifications = $user->notifications()->count();
        $unreadNotifications = $user->unreadNotifications()->count();
        $todayNotifications = $user->notifications()
            ->whereDate('created_at', today())
            ->count();

        // Notifications by type
        $notificationsByType = $user->notifications()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'total_notifications' => $totalNotifications,
                'unread_notifications' => $unreadNotifications,
                'today_notifications' => $todayNotifications,
                'notifications_by_type' => $notificationsByType
            ]
        ]);
    }

    /**
     * Update notification preferences.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'price_alerts' => 'boolean',
            'stock_alerts' => 'boolean',
            'order_updates' => 'boolean',
            'promotions' => 'boolean',
            'system_updates' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update user preferences (assuming you have a user_preferences table)
        $preferences = $user->preferences ?? [];
        $preferences['notifications'] = array_merge(
            $preferences['notifications'] ?? [],
            $request->only([
                'email_notifications',
                'push_notifications', 
                'sms_notifications',
                'price_alerts',
                'stock_alerts',
                'order_updates',
                'promotions',
                'system_updates'
            ])
        );

        $user->update(['preferences' => $preferences]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated',
            'data' => $preferences['notifications']
        ]);
    }

    /**
     * Send a test notification.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTestNotification(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:price_alert,stock_alert,order_update,promotion,system_update',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Create test notification
        $user->notify(new \App\Notifications\TestNotification(
            $request->type,
            $request->title,
            $request->message
        ));

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent'
        ]);
    }

    /**
     * Get notification templates.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplates()
    {
        $templates = [
            'price_alert' => [
                'icon' => '💰',
                'color' => 'yellow',
                'priority' => 'medium',
                'templates' => [
                    'price_drop' => 'Price dropped by {percentage}% for {part_name}! Now ${new_price} (was ${old_price})',
                    'target_price' => 'Target price reached for {part_name}! Now ${current_price} (target: ${target_price})',
                    'significant_change' => 'Significant price change for {part_name}! Price {direction} by {percentage}%'
                ]
            ],
            'stock_alert' => [
                'icon' => '📦',
                'color' => 'purple',
                'priority' => 'medium',
                'templates' => [
                    'low_stock' => 'Low stock alert: Only {quantity} left for {part_name}',
                    'critical_stock' => 'Critical stock alert: Only {quantity} left for {part_name}!',
                    'out_of_stock' => '{part_name} is now out of stock',
                    'restocked' => '{part_name} has been restocked! {quantity} available'
                ]
            ],
            'order_update' => [
                'icon' => '🛒',
                'color' => 'indigo',
                'priority' => 'high',
                'templates' => [
                    'order_confirmed' => 'Order #{order_id} confirmed! Expected delivery: {delivery_date}',
                    'order_shipped' => 'Order #{order_id} has been shipped! Tracking: {tracking_number}',
                    'order_delivered' => 'Order #{order_id} has been delivered!',
                    'order_cancelled' => 'Order #{order_id} has been cancelled'
                ]
            ],
            'promotion' => [
                'icon' => '🏷️',
                'color' => 'pink',
                'priority' => 'low',
                'templates' => [
                    'flash_sale' => 'Flash Sale: {discount}% off {category} parts! Limited time only',
                    'seasonal_discount' => 'Seasonal Sale: Save up to {discount}% on {category} parts',
                    'bulk_discount' => 'Bulk Discount: Buy {min_quantity}+ and save {discount}%'
                ]
            ],
            'system_update' => [
                'icon' => '🔧',
                'color' => 'gray',
                'priority' => 'low',
                'templates' => [
                    'maintenance' => 'Scheduled maintenance: {service} will be unavailable from {start_time} to {end_time}',
                    'feature_update' => 'New feature available: {feature_name}. {description}',
                    'security_update' => 'Security update completed. Please review your account settings.'
                ]
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }
}

