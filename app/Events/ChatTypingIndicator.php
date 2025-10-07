<?php

namespace App\Events;

use App\Models\ChatConversation;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatTypingIndicator implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $user;
    public $senderType;
    public $isTyping;

    /**
     * Create a new event instance.
     *
     * @param ChatConversation $conversation
     * @param User|null $user
     * @param string $senderType
     * @param bool $isTyping
     */
    public function __construct(ChatConversation $conversation, ?User $user, string $senderType, bool $isTyping)
    {
        $this->conversation = $conversation;
        $this->user = $user;
        $this->senderType = $senderType;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.conversation.' . $this->conversation->id),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'typing',
            'payload' => [
                'conversation_id' => $this->conversation->id,
                'sender_id' => $this->user ? $this->user->id : null,
                'sender_type' => $this->senderType,
                'sender_name' => $this->user ? $this->user->name : 'Guest',
                'is_typing' => $this->isTyping,
                'timestamp' => now()->toISOString()
            ]
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'chat.typing';
    }
}

