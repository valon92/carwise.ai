<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\EmailService;

class MaintenanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $carData;

    public function __construct(array $carData = [])
    {
        $this->carData = $carData;
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
            $emailService->sendMaintenanceReminderEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->carData
            );
        }

        // Fallback to Laravel's default mail
        $carInfo = ($this->carData['brand'] ?? 'Unknown') . ' ' . ($this->carData['model'] ?? 'Vehicle') . ' (' . ($this->carData['year'] ?? 'N/A') . ')';
        $mileage = $this->carData['current_mileage'] ?? $this->carData['mileage'] ?? 'Unknown';

        return (new MailMessage)
            ->subject('Maintenance Reminder for Your ' . ($this->carData['brand'] ?? 'Vehicle'))
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('It\'s time for scheduled maintenance on your **' . $carInfo . '**.')
            ->line('**Maintenance Details:**')
            ->line('• Vehicle: ' . $carInfo)
            ->line('• Current Mileage: ' . $mileage . ' miles')
            ->line('• Maintenance Type: ' . ($this->carData['maintenance_type'] ?? 'Regular Service'))
            ->line('• Estimated Cost: ' . ($this->carData['estimated_cost'] ?? 'Contact mechanic for quote'))
            ->line('Regular maintenance helps prevent costly repairs and keeps your vehicle running smoothly.')
            ->action('View My Cars', url('/my-cars'))
            ->line('Need help finding a trusted mechanic? Check out our mechanic network!')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Services\EmailService;

class MaintenanceReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $carData;

    public function __construct(array $carData = [])
    {
        $this->carData = $carData;
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
            $emailService->sendMaintenanceReminderEmail(
                $notifiable->email,
                $notifiable->name ?? $notifiable->first_name ?? 'User',
                $this->carData
            );
        }

        // Fallback to Laravel's default mail
        $carInfo = ($this->carData['brand'] ?? 'Unknown') . ' ' . ($this->carData['model'] ?? 'Vehicle') . ' (' . ($this->carData['year'] ?? 'N/A') . ')';
        $mileage = $this->carData['current_mileage'] ?? $this->carData['mileage'] ?? 'Unknown';

        return (new MailMessage)
            ->subject('Maintenance Reminder for Your ' . ($this->carData['brand'] ?? 'Vehicle'))
            ->greeting('Hello ' . ($notifiable->name ?? $notifiable->first_name ?? 'User') . '!')
            ->line('It\'s time for scheduled maintenance on your **' . $carInfo . '**.')
            ->line('**Maintenance Details:**')
            ->line('• Vehicle: ' . $carInfo)
            ->line('• Current Mileage: ' . $mileage . ' miles')
            ->line('• Maintenance Type: ' . ($this->carData['maintenance_type'] ?? 'Regular Service'))
            ->line('• Estimated Cost: ' . ($this->carData['estimated_cost'] ?? 'Contact mechanic for quote'))
            ->line('Regular maintenance helps prevent costly repairs and keeps your vehicle running smoothly.')
            ->action('View My Cars', url('/my-cars'))
            ->line('Need help finding a trusted mechanic? Check out our mechanic network!')
            ->salutation('Best regards, The CarWise.ai Team');
    }
}














