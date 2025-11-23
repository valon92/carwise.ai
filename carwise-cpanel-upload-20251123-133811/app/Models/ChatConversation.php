<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatConversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'agent_id',
        'status',
        'subject',
        'priority',
        'channel',
        'user_agent',
        'page_url',
        'ip_address',
        'started_at',
        'ended_at',
        'rating',
        'feedback',
        'metadata'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'array'
    ];

    const STATUS_WAITING = 'waiting';
    const STATUS_ACTIVE = 'active';
    const STATUS_ENDED = 'ended';
    const STATUS_TRANSFERRED = 'transferred';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    const CHANNEL_WEBSITE = 'website';
    const CHANNEL_MOBILE = 'mobile';
    const CHANNEL_EMAIL = 'email';

    /**
     * Get the user that owns the conversation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent assigned to the conversation.
     */
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the messages for the conversation.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at');
    }

    /**
     * Get the latest message for the conversation.
     */
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latest();
    }

    /**
     * Get unread messages count for the user.
     */
    public function unreadMessagesCount()
    {
        return $this->messages()
            ->where('sender_type', 'agent')
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Mark all messages as read for the user.
     */
    public function markAsRead()
    {
        $this->messages()
            ->where('sender_type', 'agent')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Check if conversation is active.
     */
    public function isActive()
    {
        return in_array($this->status, [self::STATUS_WAITING, self::STATUS_ACTIVE]);
    }

    /**
     * Check if conversation can receive messages.
     */
    public function canReceiveMessages()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Start the conversation.
     */
    public function start($agentId = null)
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'agent_id' => $agentId,
            'started_at' => now()
        ]);
    }

    /**
     * End the conversation.
     */
    public function end($rating = null, $feedback = null)
    {
        $this->update([
            'status' => self::STATUS_ENDED,
            'ended_at' => now(),
            'rating' => $rating,
            'feedback' => $feedback
        ]);
    }

    /**
     * Transfer conversation to another agent.
     */
    public function transferTo($agentId, $reason = null)
    {
        $oldAgentId = $this->agent_id;
        
        $this->update([
            'agent_id' => $agentId,
            'status' => self::STATUS_TRANSFERRED
        ]);

        // Create system message about transfer
        $this->messages()->create([
            'content' => "Conversation transferred to another agent. Reason: " . ($reason ?: 'No reason provided'),
            'type' => 'system',
            'sender_type' => 'system',
            'metadata' => [
                'old_agent_id' => $oldAgentId,
                'new_agent_id' => $agentId,
                'reason' => $reason
            ]
        ]);
    }

    /**
     * Get conversation duration in minutes.
     */
    public function getDurationAttribute()
    {
        if (!$this->started_at) return 0;
        
        $endTime = $this->ended_at ?: now();
        return $this->started_at->diffInMinutes($endTime);
    }

    /**
     * Get conversation summary.
     */
    public function getSummaryAttribute()
    {
        $messageCount = $this->messages()->count();
        $userMessageCount = $this->messages()->where('sender_type', 'user')->count();
        $agentMessageCount = $this->messages()->where('sender_type', 'agent')->count();

        return [
            'total_messages' => $messageCount,
            'user_messages' => $userMessageCount,
            'agent_messages' => $agentMessageCount,
            'duration_minutes' => $this->duration,
            'has_rating' => !is_null($this->rating),
            'has_feedback' => !is_null($this->feedback)
        ];
    }

    /**
     * Scope for active conversations.
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_WAITING, self::STATUS_ACTIVE]);
    }

    /**
     * Scope for conversations waiting for agent.
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', self::STATUS_WAITING);
    }

    /**
     * Scope for conversations by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for conversations by agent.
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    /**
     * Scope for recent conversations.
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}