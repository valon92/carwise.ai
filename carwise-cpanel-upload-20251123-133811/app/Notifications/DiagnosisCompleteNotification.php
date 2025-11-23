<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\EmailService;

class DiagnosisCompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $diagnosisData;

    public function __construct(array $diagnosisData = [])
    {
        $this->diagnosisData = $diagnosisData;
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
            $emailService->sendDiagnosisEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->diagnosisData
            );
        }

        // Fallback to Laravel's default mail
        $carInfo = ($this->diagnosisData['car_brand'] ?? 'Unknown') . ' ' . ($this->diagnosisData['car_model'] ?? 'Vehicle');
        $severity = $this->diagnosisData['severity'] ?? 'unknown';
        $confidence = $this->diagnosisData['confidence_score'] ?? 0;

        return (new MailMessage)
            ->subject('Your Car Diagnosis is Ready - CarWise.ai')
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('Your AI diagnosis for your **' . $carInfo . '** has been completed successfully.')
            ->line('**Diagnosis Summary:**')
            ->line('• Vehicle: ' . $carInfo)
            ->line('• Severity Level: ' . ucfirst($severity))
            ->line('• Confidence Score: ' . $confidence . '%')
            ->line('• AI Provider: ' . ($this->diagnosisData['ai_provider'] ?? 'OpenAI'))
            ->action('View Full Report', url('/diagnosis/' . ($this->diagnosisData['session_id'] ?? '')))
            ->line('Need help understanding your diagnosis? Our support team is here to help!')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\EmailService;

class DiagnosisCompleteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $diagnosisData;

    public function __construct(array $diagnosisData = [])
    {
        $this->diagnosisData = $diagnosisData;
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
            $emailService->sendDiagnosisEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->diagnosisData
            );
        }

        // Fallback to Laravel's default mail
        $carInfo = ($this->diagnosisData['car_brand'] ?? 'Unknown') . ' ' . ($this->diagnosisData['car_model'] ?? 'Vehicle');
        $severity = $this->diagnosisData['severity'] ?? 'unknown';
        $confidence = $this->diagnosisData['confidence_score'] ?? 0;

        return (new MailMessage)
            ->subject('Your Car Diagnosis is Ready - CarWise.ai')
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('Your AI diagnosis for your **' . $carInfo . '** has been completed successfully.')
            ->line('**Diagnosis Summary:**')
            ->line('• Vehicle: ' . $carInfo)
            ->line('• Severity Level: ' . ucfirst($severity))
            ->line('• Confidence Score: ' . $confidence . '%')
            ->line('• AI Provider: ' . ($this->diagnosisData['ai_provider'] ?? 'OpenAI'))
            ->action('View Full Report', url('/diagnosis/' . ($this->diagnosisData['session_id'] ?? '')))
            ->line('Need help understanding your diagnosis? Our support team is here to help!')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}














