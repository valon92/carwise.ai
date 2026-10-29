<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\EmailService;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $userData;

    public function __construct(array $userData = [])
    {
        $this->userData = $userData;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $emailService = app(EmailService::class);

        // Prefer custom email service when configured
        if ($emailService->isEnabled()) {
            $emailService->sendWelcomeEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->userData
            );
        }

        return (new MailMessage)
            ->subject('Welcome to CarWise.ai - Your AI Car Diagnosis Platform')
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('Welcome to CarWise.ai, the AI-powered car diagnosis platform.')
            ->line('What you can do:')
            ->line('AI Diagnosis — preview free, unlock full result + dated PDF evidence')
            ->line('My Garage — keep vehicles ready for the next check')
            ->line('Diagnosis history — reopen results and download reports')
            ->action('Start Your First Diagnosis', url('/diagnose'))
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}
