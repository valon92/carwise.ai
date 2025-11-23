<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'action_text',
        'is_read',
        'read_at',
        'priority',
        'in_app',
        'email',
        'push',
        'sms',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
        'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'in_app' => 'boolean',
        'email' => 'boolean',
        'push' => 'boolean',
        'sms' => 'boolean',
        'read_at' => 'datetime',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'expires_at' => 'datetime'
    ];

    /**
     * Get the user that owns this notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for notifications by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for notifications by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for active notifications (not expired).
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope for recent notifications.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if (!$this->is_read) {
            $this->is_read = true;
            $this->read_at = now();
            return $this->save();
        }
        return true;
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        if ($this->is_read) {
            $this->is_read = false;
            $this->read_at = null;
            return $this->save();
        }
        return true;
    }

    /**
     * Check if notification is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Get the age of the notification in human readable format.
     */
    public function getAgeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get formatted priority.
     */
    public function getFormattedPriorityAttribute(): string
    {
        return ucfirst($this->priority);
    }

    /**
     * Get priority color for UI.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'red',
            'high' => 'orange',
            'normal' => 'blue',
            'low' => 'gray',
            default => 'blue'
        };
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'diagnosis_complete' => '🔍',
            'review_approved' => '✅',
            'review_rejected' => '❌',
            'system_alert' => '⚠️',
            'maintenance_reminder' => '🔧',
            'appointment_reminder' => '📅',
            'payment_success' => '💳',
            'payment_failed' => '❌',
            'welcome' => '👋',
            'security_alert' => '🔒',
            default => '📢'
        };
    }

    /**
     * Create a notification for a user.
     */
    public static function createForUser(
        int $userId,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): self {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $options['data'] ?? null,
            'action_url' => $options['action_url'] ?? null,
            'action_text' => $options['action_text'] ?? null,
            'priority' => $options['priority'] ?? 'normal',
            'in_app' => $options['in_app'] ?? true,
            'email' => $options['email'] ?? false,
            'push' => $options['push'] ?? false,
            'sms' => $options['sms'] ?? false,
            'expires_at' => $options['expires_at'] ?? null,
        ]);
    }

    /**
     * Create a notification for multiple users.
     */
    public static function createForUsers(
        array $userIds,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): int {
        $notifications = [];
        $now = now();

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $options['data'] ?? null,
                'action_url' => $options['action_url'] ?? null,
                'action_text' => $options['action_text'] ?? null,
                'priority' => $options['priority'] ?? 'normal',
                'in_app' => $options['in_app'] ?? true,
                'email' => $options['email'] ?? false,
                'push' => $options['push'] ?? false,
                'sms' => $options['sms'] ?? false,
                'expires_at' => $options['expires_at'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return static::insert($notifications);
    }

    /**
     * Get unread count for a user.
     */
    public static function getUnreadCountForUser(int $userId): int
    {
        return static::where('user_id', $userId)
            ->unread()
            ->active()
            ->count();
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsReadForUser(int $userId): int
    {
        return static::where('user_id', $userId)
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Clean up expired notifications.
     */
    public static function cleanupExpired(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }
}