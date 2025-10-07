<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $type;
    protected $title;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param string $type
     * @param string $title
     * @param string $message
     */
    public function __construct(string $type, string $title, string $message)
    {
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->title)
            ->line($this->message)
            ->action('View Dashboard', url('/dashboard'))
            ->line('Thank you for using CarWise.ai!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
            'priority' => 'medium',
            'icon' => $this->getIcon(),
            'color' => $this->getColor()
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param mixed $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'notification',
            'payload' => [
                'id' => $this->id,
                'type' => $this->type,
                'title' => $this->title,
                'message' => $this->message,
                'timestamp' => now()->toISOString(),
                'priority' => 'medium',
                'icon' => $this->getIcon(),
                'color' => $this->getColor(),
                'user_id' => $notifiable->id
            ]
        ]);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function broadcastOn($notifiable)
    {
        return [
            'user.' . $notifiable->id,
            'notifications'
        ];
    }

    /**
     * Get icon for notification type.
     *
     * @return string
     */
    protected function getIcon()
    {
        $icons = [
            'price_alert' => '💰',
            'stock_alert' => '📦',
            'order_update' => '🛒',
            'promotion' => '🏷️',
            'system_update' => '🔧'
        ];

        return $icons[$this->type] ?? '📢';
    }

    /**
     * Get color for notification type.
     *
     * @return string
     */
    protected function getColor()
    {
        $colors = [
            'price_alert' => 'yellow',
            'stock_alert' => 'purple',
            'order_update' => 'indigo',
            'promotion' => 'pink',
            'system_update' => 'gray'
        ];

        return $colors[$this->type] ?? 'blue';
    }
}

