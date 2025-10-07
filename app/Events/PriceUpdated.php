<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PriceUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $partId;
    public $newPrice;
    public $oldPrice;
    public $change;
    public $changePercentage;
    public $timestamp;

    /**
     * Create a new event instance.
     *
     * @param int $partId
     * @param float $newPrice
     * @param float $oldPrice
     */
    public function __construct(int $partId, float $newPrice, float $oldPrice)
    {
        $this->partId = $partId;
        $this->newPrice = $newPrice;
        $this->oldPrice = $oldPrice;
        $this->change = $newPrice - $oldPrice;
        $this->changePercentage = $oldPrice > 0 ? (($newPrice - $oldPrice) / $oldPrice) * 100 : 0;
        $this->timestamp = now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('price-updates'),
            new Channel('price-updates.' . $this->partId),
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
            'type' => 'priceUpdate',
            'payload' => [
                'part_id' => $this->partId,
                'new_price' => $this->newPrice,
                'old_price' => $this->oldPrice,
                'change' => $this->change,
                'change_percentage' => round($this->changePercentage, 2),
                'timestamp' => $this->timestamp->toISOString(),
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
        return 'price.updated';
    }
}

