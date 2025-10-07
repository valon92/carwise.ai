<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'sender_name',
        'content',
        'type',
        'attachments',
        'read_at',
        'delivered_at',
        'metadata'
    ];

    protected $casts = [
        'attachments' => 'array',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'delivered_at' => 'datetime'
    ];

    const TYPE_TEXT = 'text';
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';
    const TYPE_SYSTEM = 'system';
    const TYPE_TYPING = 'typing';

    const SENDER_TYPE_USER = 'user';
    const SENDER_TYPE_AGENT = 'agent';
    const SENDER_TYPE_SYSTEM = 'system';

    /**
     * Get the conversation that owns the message.
     */
    public function conversation()
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Mark message as read.
     */
    public function markAsRead()
    {
        if (!$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Mark message as delivered.
     */
    public function markAsDelivered()
    {
        if (!$this->delivered_at) {
            $this->update(['delivered_at' => now()]);
        }
    }

    /**
     * Check if message is from user.
     */
    public function isFromUser()
    {
        return $this->sender_type === self::SENDER_TYPE_USER;
    }

    /**
     * Check if message is from agent.
     */
    public function isFromAgent()
    {
        return $this->sender_type === self::SENDER_TYPE_AGENT;
    }

    /**
     * Check if message is system message.
     */
    public function isSystemMessage()
    {
        return $this->sender_type === self::SENDER_TYPE_SYSTEM;
    }

    /**
     * Check if message has attachments.
     */
    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    /**
     * Get formatted content for display.
     */
    public function getFormattedContentAttribute()
    {
        switch ($this->type) {
            case self::TYPE_TEXT:
                return nl2br(e($this->content));
            
            case self::TYPE_SYSTEM:
                return '<em>' . e($this->content) . '</em>';
            
            case self::TYPE_IMAGE:
            case self::TYPE_FILE:
                $content = e($this->content);
                if ($this->hasAttachments()) {
                    $content .= ' (with ' . count($this->attachments) . ' attachment(s))';
                }
                return $content;
            
            default:
                return e($this->content);
        }
    }

    /**
     * Get message preview for notifications.
     */
    public function getPreviewAttribute()
    {
        switch ($this->type) {
            case self::TYPE_TEXT:
                return \Str::limit($this->content, 100);
            
            case self::TYPE_IMAGE:
                return '📷 Sent an image';
            
            case self::TYPE_FILE:
                return '📎 Sent a file';
            
            case self::TYPE_SYSTEM:
                return $this->content;
            
            default:
                return 'New message';
        }
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for messages by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for messages by sender type.
     */
    public function scopeBySenderType($query, $senderType)
    {
        return $query->where('sender_type', $senderType);
    }

    /**
     * Scope for messages from user.
     */
    public function scopeFromUser($query)
    {
        return $query->where('sender_type', self::SENDER_TYPE_USER);
    }

    /**
     * Scope for messages from agent.
     */
    public function scopeFromAgent($query)
    {
        return $query->where('sender_type', self::SENDER_TYPE_AGENT);
    }

    /**
     * Scope for system messages.
     */
    public function scopeSystemMessages($query)
    {
        return $query->where('sender_type', self::SENDER_TYPE_SYSTEM);
    }

    /**
     * Scope for recent messages.
     */
    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
}