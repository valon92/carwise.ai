<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChatConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'mechanic_id',
        'subject',
        'status',
        'priority',
        'related_type',
        'related_id',
        'last_message_at',
        'closed_at',
        'archived_at'
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
        'archived_at' => 'datetime'
    ];

    /**
     * Get the user in this conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the mechanic in this conversation.
     */
    public function mechanic(): BelongsTo
    {
        return $this->belongsTo(Mechanic::class);
    }

    /**
     * Get the related entity (polymorphic).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get all messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the latest message in this conversation.
     */
    public function latestMessage(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->latest();
    }

    /**
     * Scope for active conversations.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for closed conversations.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope for archived conversations.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope for conversations by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for conversations with unread messages.
     */
    public function scopeWithUnreadMessages($query, $userId)
    {
        return $query->whereHas('messages', function ($q) use ($userId) {
            $q->where('sender_id', '!=', $userId)
              ->where('is_read', false);
        });
    }

    /**
     * Check if conversation is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if conversation is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if conversation is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Get unread message count for a user.
     */
    public function getUnreadCountForUser(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark all messages as read for a user.
     */
    public function markAllMessagesAsReadForUser(int $userId): int
    {
        return $this->messages()
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Get the other participant in the conversation.
     */
    public function getOtherParticipant(int $currentUserId): User|Mechanic
    {
        if ($this->user_id === $currentUserId) {
            return $this->mechanic;
        }
        
        return $this->user;
    }

    /**
     * Get conversation status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'green',
            'closed' => 'gray',
            'archived' => 'blue',
            default => 'gray'
        };
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
     * Close the conversation.
     */
    public function close(): bool
    {
        $this->status = 'closed';
        $this->closed_at = now();
        return $this->save();
    }

    /**
     * Archive the conversation.
     */
    public function archive(): bool
    {
        $this->status = 'archived';
        $this->archived_at = now();
        return $this->save();
    }

    /**
     * Reactivate the conversation.
     */
    public function reactivate(): bool
    {
        $this->status = 'active';
        $this->closed_at = null;
        $this->archived_at = null;
        return $this->save();
    }

    /**
     * Update last message timestamp.
     */
    public function updateLastMessageTime(): bool
    {
        $this->last_message_at = now();
        return $this->save();
    }

    /**
     * Create a new conversation.
     */
    public static function createConversation(
        int $userId,
        int $mechanicId,
        string $subject = null,
        string $relatedType = null,
        int $relatedId = null,
        string $priority = 'normal'
    ): self {
        return static::create([
            'user_id' => $userId,
            'mechanic_id' => $mechanicId,
            'subject' => $subject,
            'related_type' => $relatedType,
            'related_id' => $relatedId,
            'priority' => $priority,
            'status' => 'active'
        ]);
    }

    /**
     * Find or create conversation between user and mechanic.
     */
    public static function findOrCreateConversation(
        int $userId,
        int $mechanicId,
        string $subject = null,
        string $relatedType = null,
        int $relatedId = null
    ): self {
        $conversation = static::where('user_id', $userId)
            ->where('mechanic_id', $mechanicId)
            ->where('status', 'active')
            ->first();

        if (!$conversation) {
            $conversation = static::createConversation(
                $userId,
                $mechanicId,
                $subject,
                $relatedType,
                $relatedId
            );
        }

        return $conversation;
    }

    /**
     * Get conversation statistics.
     */
    public static function getConversationStats(): array
    {
        return [
            'total_active' => static::active()->count(),
            'total_closed' => static::closed()->count(),
            'total_archived' => static::archived()->count(),
            'avg_messages_per_conversation' => static::withCount('messages')->avg('messages_count') ?? 0,
            'conversations_with_unread' => static::whereHas('messages', function ($q) {
                $q->where('is_read', false);
            })->count(),
        ];
    }
}