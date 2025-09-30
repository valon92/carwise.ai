<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send a diagnosis completion notification.
     */
    public function sendDiagnosisCompleteNotification(int $userId, array $diagnosisData): void
    {
        $this->createNotification(
            $userId,
            'diagnosis_complete',
            'Diagnosis Complete',
            "Your car diagnosis for {$diagnosisData['make']} {$diagnosisData['model']} has been completed.",
            [
                'action_url' => "/diagnose/{$diagnosisData['session_id']}",
                'action_text' => 'View Results',
                'data' => $diagnosisData,
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Send a review approval notification.
     */
    public function sendReviewApprovedNotification(int $userId, array $reviewData): void
    {
        $this->createNotification(
            $userId,
            'review_approved',
            'Review Approved',
            "Your review for {$reviewData['mechanic_name']} has been approved and is now visible.",
            [
                'action_url' => "/mechanics/{$reviewData['mechanic_id']}",
                'action_text' => 'View Mechanic',
                'data' => $reviewData,
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Send a review rejection notification.
     */
    public function sendReviewRejectedNotification(int $userId, array $reviewData, string $reason = null): void
    {
        $message = "Your review for {$reviewData['mechanic_name']} was not approved.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        $this->createNotification(
            $userId,
            'review_rejected',
            'Review Not Approved',
            $message,
            [
                'action_url' => "/mechanics/{$reviewData['mechanic_id']}",
                'action_text' => 'View Mechanic',
                'data' => array_merge($reviewData, ['reason' => $reason]),
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Send a system alert notification.
     */
    public function sendSystemAlertNotification(string $title, string $message, string $priority = 'normal'): void
    {
        $userIds = User::where('role', '!=', 'admin')->pluck('id')->toArray();
        
        Notification::createForUsers(
            $userIds,
            'system_alert',
            $title,
            $message,
            [
                'priority' => $priority,
                'action_url' => '/dashboard',
                'action_text' => 'View Dashboard'
            ]
        );
    }

    /**
     * Send a maintenance reminder notification.
     */
    public function sendMaintenanceReminderNotification(int $userId, array $carData): void
    {
        $this->createNotification(
            $userId,
            'maintenance_reminder',
            'Maintenance Reminder',
            "It's time for maintenance on your {$carData['make']} {$carData['model']}.",
            [
                'action_url' => "/cars/{$carData['car_id']}",
                'action_text' => 'View Car',
                'data' => $carData,
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Send a welcome notification for new users.
     */
    public function sendWelcomeNotification(int $userId, string $userName): void
    {
        $this->createNotification(
            $userId,
            'welcome',
            'Welcome to CarWise.ai!',
            "Welcome {$userName}! Get started by adding your first car or trying our AI diagnosis.",
            [
                'action_url' => '/diagnose',
                'action_text' => 'Start Diagnosis',
                'priority' => 'normal',
                'expires_at' => now()->addDays(7)
            ]
        );
    }

    /**
     * Send a security alert notification.
     */
    public function sendSecurityAlertNotification(int $userId, string $alertType, array $details = []): void
    {
        $messages = [
            'login_attempt' => 'A new login was detected on your account.',
            'password_change' => 'Your password has been changed.',
            'suspicious_activity' => 'Suspicious activity detected on your account.',
        ];

        $message = $messages[$alertType] ?? 'Security alert on your account.';

        $this->createNotification(
            $userId,
            'security_alert',
            'Security Alert',
            $message,
            [
                'action_url' => '/profile/security',
                'action_text' => 'Review Security',
                'data' => $details,
                'priority' => 'high'
            ]
        );
    }

    /**
     * Send a payment success notification.
     */
    public function sendPaymentSuccessNotification(int $userId, array $paymentData): void
    {
        $this->createNotification(
            $userId,
            'payment_success',
            'Payment Successful',
            "Your payment of {$paymentData['amount']} has been processed successfully.",
            [
                'action_url' => '/dashboard/billing',
                'action_text' => 'View Billing',
                'data' => $paymentData,
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Send a payment failed notification.
     */
    public function sendPaymentFailedNotification(int $userId, array $paymentData): void
    {
        $this->createNotification(
            $userId,
            'payment_failed',
            'Payment Failed',
            "Your payment of {$paymentData['amount']} could not be processed. Please update your payment method.",
            [
                'action_url' => '/dashboard/billing',
                'action_text' => 'Update Payment',
                'data' => $paymentData,
                'priority' => 'high'
            ]
        );
    }

    /**
     * Create a notification for a single user.
     */
    public function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): Notification {
        $notification = Notification::createForUser($userId, $type, $title, $message, $options);

        // Send via other channels if requested
        if ($options['email'] ?? false) {
            $this->sendEmailNotification($notification);
        }

        if ($options['push'] ?? false) {
            $this->sendPushNotification($notification);
        }

        if ($options['sms'] ?? false) {
            $this->sendSmsNotification($notification);
        }

        return $notification;
    }

    /**
     * Send email notification.
     */
    private function sendEmailNotification(Notification $notification): void
    {
        try {
            // Here you would implement email sending logic
            // For now, we'll just log it
            Log::info('Email notification sent', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type
            ]);

            $notification->update([
                'sent_at' => now(),
                'delivered_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send email notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            $notification->update([
                'failed_at' => now(),
                'failure_reason' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send push notification.
     */
    private function sendPushNotification(Notification $notification): void
    {
        try {
            // Here you would implement push notification logic
            // For now, we'll just log it
            Log::info('Push notification sent', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type
            ]);

            $notification->update([
                'sent_at' => now(),
                'delivered_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            $notification->update([
                'failed_at' => now(),
                'failure_reason' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send SMS notification.
     */
    private function sendSmsNotification(Notification $notification): void
    {
        try {
            // Here you would implement SMS sending logic
            // For now, we'll just log it
            Log::info('SMS notification sent', [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
                'type' => $notification->type
            ]);

            $notification->update([
                'sent_at' => now(),
                'delivered_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send SMS notification', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage()
            ]);

            $notification->update([
                'failed_at' => now(),
                'failure_reason' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get notifications for a user.
     */
    public function getUserNotifications(int $userId, int $limit = 20, bool $unreadOnly = false): \Illuminate\Database\Eloquent\Collection
    {
        $query = Notification::where('user_id', $userId)
            ->active()
            ->orderBy('created_at', 'desc');

        if ($unreadOnly) {
            $query->unread();
        }

        return $query->limit($limit)->get();
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $userId)
            ->first();

        if ($notification) {
            return $notification->markAsRead();
        }

        return false;
    }

    /**
     * Mark all notifications as read for a user.
     */
    public function markAllAsRead(int $userId): int
    {
        return Notification::markAllAsReadForUser($userId);
    }

    /**
     * Get unread count for a user.
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::getUnreadCountForUser($userId);
    }

    /**
     * Clean up expired notifications.
     */
    public function cleanupExpired(): int
    {
        return Notification::cleanupExpired();
    }
}



