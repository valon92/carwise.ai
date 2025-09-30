<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'sender_type',
        'message',
        'message_type',
        'attachment_path',
        'attachment_name',
        'attachment_type',
        'attachment_size',
        'is_read',
        'read_at',
        'is_edited',
        'edited_at',
        'is_deleted',
        'deleted_at',
        'reply_to_id',
        'metadata'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'read_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'metadata' => 'array',
        'attachment_size' => 'integer'
    ];

    /**
     * Get the conversation this message belongs to.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class);
    }

    /**
     * Get the sender of this message.
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Get the message this is replying to.
     */
    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id');
    }

    /**
     * Get replies to this message.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'reply_to_id');
    }

    /**
     * Scope for unread messages.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read messages.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope for messages by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('message_type', $type);
    }

    /**
     * Scope for messages with attachments.
     */
    public function scopeWithAttachments($query)
    {
        return $query->whereNotNull('attachment_path');
    }

    /**
     * Scope for recent messages.
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Check if message is read.
     */
    public function isRead(): bool
    {
        return $this->is_read;
    }

    /**
     * Check if message is edited.
     */
    public function isEdited(): bool
    {
        return $this->is_edited;
    }

    /**
     * Check if message is deleted.
     */
    public function isDeleted(): bool
    {
        return $this->is_deleted;
    }

    /**
     * Check if message has attachment.
     */
    public function hasAttachment(): bool
    {
        return !empty($this->attachment_path);
    }

    /**
     * Check if message is a reply.
     */
    public function isReply(): bool
    {
        return !empty($this->reply_to_id);
    }

    /**
     * Mark message as read.
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
     * Mark message as unread.
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
     * Edit the message.
     */
    public function edit(string $newMessage): bool
    {
        $this->message = $newMessage;
        $this->is_edited = true;
        $this->edited_at = now();
        return $this->save();
    }

    /**
     * Delete the message (soft delete).
     */
    public function softDelete(): bool
    {
        $this->is_deleted = true;
        $this->deleted_at = now();
        $this->message = '[Message deleted]';
        return $this->save();
    }

    /**
     * Restore the message.
     */
    public function restore(): bool
    {
        $this->is_deleted = false;
        $this->deleted_at = null;
        return $this->save();
    }

    /**
     * Get formatted attachment size.
     */
    public function getFormattedAttachmentSizeAttribute(): string
    {
        if (!$this->attachment_size) {
            return '';
        }

        $bytes = $this->attachment_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get message type icon.
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->message_type) {
            'text' => '💬',
            'image' => '🖼️',
            'file' => '📎',
            'diagnosis' => '🔍',
            'appointment' => '📅',
            default => '💬'
        };
    }

    /**
     * Get sender display name.
     */
    public function getSenderDisplayNameAttribute(): string
    {
        if ($this->sender_type === 'mechanic') {
            $mechanic = Mechanic::find($this->sender_id);
            return $mechanic ? $mechanic->name : 'Mechanic';
        }
        
        return $this->sender ? $this->sender->name : 'User';
    }

    /**
     * Get message age in human readable format.
     */
    public function getAgeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get display message (handles deleted messages).
     */
    public function getDisplayMessageAttribute(): string
    {
        if ($this->is_deleted) {
            return '[Message deleted]';
        }
        
        return $this->message;
    }

    /**
     * Create a new message.
     */
    public static function createMessage(
        int $conversationId,
        int $senderId,
        string $senderType,
        string $message,
        string $messageType = 'text',
        array $options = []
    ): self {
        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id' => $senderId,
            'sender_type' => $senderType,
            'message' => $message,
            'message_type' => $messageType,
        ];

        // Add optional fields
        if (isset($options['attachment_path'])) {
            $messageData['attachment_path'] = $options['attachment_path'];
            $messageData['attachment_name'] = $options['attachment_name'] ?? null;
            $messageData['attachment_type'] = $options['attachment_type'] ?? null;
            $messageData['attachment_size'] = $options['attachment_size'] ?? null;
        }

        if (isset($options['reply_to_id'])) {
            $messageData['reply_to_id'] = $options['reply_to_id'];
        }

        if (isset($options['metadata'])) {
            $messageData['metadata'] = $options['metadata'];
        }

        $newMessage = static::create($messageData);

        // Update conversation's last message time
        $conversation = ChatConversation::find($conversationId);
        $conversation->updateLastMessageTime();

        return $newMessage;
    }

    /**
     * Get message statistics.
     */
    public static function getMessageStats(): array
    {
        return [
            'total_messages' => static::count(),
            'unread_messages' => static::unread()->count(),
            'messages_today' => static::whereDate('created_at', today())->count(),
            'messages_this_week' => static::where('created_at', '>=', now()->startOfWeek())->count(),
            'messages_this_month' => static::where('created_at', '>=', now()->startOfMonth())->count(),
            'avg_messages_per_day' => static::where('created_at', '>=', now()->subDays(30))->count() / 30,
        ];
    }
}