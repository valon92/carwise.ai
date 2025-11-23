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
        
        // Use custom email service if available
        if ($emailService->isEnabled()) {
            $emailService->sendWelcomeEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->userData
            );
        }

        // Fallback to Laravel's default mail
        return (new MailMessage)
            ->subject('Welcome to CarWise.ai - Your AI Car Diagnosis Platform')
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('Welcome to CarWise.ai, the most advanced AI-powered car diagnosis platform.')
            ->line('What you can do with CarWise.ai:')
            ->line('🔍 AI Car Diagnosis - Get instant, accurate diagnosis for your vehicle problems')
            ->line('🚙 Car Management - Keep track of all your vehicles in one place')
            ->line('🔧 Part Search - Find authorized car parts for your vehicle')
            ->line('📊 Maintenance Tracking - Never miss important maintenance again')
            ->line('👨‍🔧 Mechanic Network - Connect with trusted mechanics in your area')
            ->action('Start Your First Diagnosis', url('/diagnose'))
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}

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
        
        // Use custom email service if available
        if ($emailService->isEnabled()) {
            $emailService->sendWelcomeEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->userData
            );
        }

        // Fallback to Laravel's default mail
        return (new MailMessage)
            ->subject('Welcome to CarWise.ai - Your AI Car Diagnosis Platform')
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('Welcome to CarWise.ai, the most advanced AI-powered car diagnosis platform.')
            ->line('What you can do with CarWise.ai:')
            ->line('🔍 AI Car Diagnosis - Get instant, accurate diagnosis for your vehicle problems')
            ->line('🚙 Car Management - Keep track of all your vehicles in one place')
            ->line('🔧 Part Search - Find authorized car parts for your vehicle')
            ->line('📊 Maintenance Tracking - Never miss important maintenance again')
            ->line('👨‍🔧 Mechanic Network - Connect with trusted mechanics in your area')
            ->action('Start Your First Diagnosis', url('/diagnose'))
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}














