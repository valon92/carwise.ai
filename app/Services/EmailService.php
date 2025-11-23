<?php

namespace App\Services;

use SendGrid;
use SendGrid\Mail\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as LaravelMail;
use Illuminate\Support\Facades\Http;

class EmailService
{
    private $sendGridEnabled;
    private $mailgunEnabled;
    private $sendGridApiKey;
    private $mailgunDomain;
    private $mailgunSecret;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $this->sendGridEnabled = config('services.sendgrid.enabled', false);
        $this->mailgunEnabled = config('services.mailgun.enabled', false);
        $this->sendGridApiKey = config('services.sendgrid.api_key');
        $this->mailgunDomain = config('services.mailgun.domain');
        $this->mailgunSecret = config('services.mailgun.secret');
        $this->fromEmail = config('services.sendgrid.from_email', config('services.mailgun.from_email', 'noreply@carwise.ai'));
        $this->fromName = config('services.sendgrid.from_name', config('services.mailgun.from_name', 'CarWise.ai'));
    }

    /**
     * Check if email service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->sendGridEnabled || $this->mailgunEnabled;
    }

    /**
     * Send email using SendGrid
     */
    private function sendViaSendGrid(string $to, string $subject, string $content, array $options = []): bool
    {
        if (!$this->sendGridEnabled || !$this->sendGridApiKey) {
            return false;
        }

        try {
            $email = new Mail();
            $email->setFrom($this->fromEmail, $this->fromName);
            $email->setSubject($subject);
            $email->addTo($to, $options['to_name'] ?? '');
            
            if (isset($options['reply_to'])) {
                $email->setReplyTo($options['reply_to']);
            }

            // Check if content is HTML or plain text
            if (isset($options['is_html']) && $options['is_html']) {
                $email->addContent('text/html', $content);
            } else {
                $email->addContent('text/plain', $content);
            }

            // Add attachments if provided
            if (isset($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $attachment) {
                    $email->addAttachment(
                        base64_encode($attachment['content']),
                        $attachment['type'] ?? 'application/octet-stream',
                        $attachment['filename'],
                        $attachment['disposition'] ?? 'attachment'
                    );
                }
            }

            // Use template if provided
            if (isset($options['template_id'])) {
                $email->setTemplateId($options['template_id']);
                $email->addDynamicTemplateData($options['template_data'] ?? []);
            }

            $sendgrid = new SendGrid($this->sendGridApiKey);
            $response = $sendgrid->send($email);

            if ($response->statusCode() >= 200 && $response->statusCode() < 300) {
                Log::info('SendGrid email sent successfully', [
                    'to' => $to,
                    'subject' => $subject,
                    'status_code' => $response->statusCode()
                ]);
                return true;
            } else {
                Log::warning('SendGrid email failed', [
                    'to' => $to,
                    'subject' => $subject,
                    'status_code' => $response->statusCode(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('SendGrid email error', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email using Mailgun
     */
    private function sendViaMailgun(string $to, string $subject, string $content, array $options = []): bool
    {
        if (!$this->mailgunEnabled || !$this->mailgunDomain || !$this->mailgunSecret) {
            return false;
        }

        try {
            $data = [
                'from' => "{$this->fromName} <{$this->fromEmail}>",
                'to' => $to,
                'subject' => $subject,
            ];

            if (isset($options['is_html']) && $options['is_html']) {
                $data['html'] = $content;
            } else {
                $data['text'] = $content;
            }

            if (isset($options['reply_to'])) {
                $data['h:Reply-To'] = $options['reply_to'];
            }

            if (isset($options['to_name'])) {
                $data['to'] = "{$options['to_name']} <{$to}>";
            }

            // Add attachments if provided
            if (isset($options['attachments']) && is_array($options['attachments'])) {
                foreach ($options['attachments'] as $index => $attachment) {
                    $data["attachment[{$index}]"] = $attachment['content'];
                }
            }

            $response = Http::withBasicAuth('api', $this->mailgunSecret)
                ->asMultipart()
                ->post("https://api.mailgun.net/v3/{$this->mailgunDomain}/messages", $data);

            if ($response->successful()) {
                Log::info('Mailgun email sent successfully', [
                    'to' => $to,
                    'subject' => $subject,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                Log::warning('Mailgun email failed', [
                    'to' => $to,
                    'subject' => $subject,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Mailgun email error', [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send email (automatically chooses provider)
     */
    public function sendEmail(string $to, string $subject, string $content, array $options = []): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('Email service not enabled');
            return false;
        }

        // Try SendGrid first, then Mailgun
        if ($this->sendGridEnabled) {
            $result = $this->sendViaSendGrid($to, $subject, $content, $options);
            if ($result) {
                return true;
            }
        }

        if ($this->mailgunEnabled) {
            return $this->sendViaMailgun($to, $subject, $content, $options);
        }

        return false;
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail(string $to, string $name, array $userData = []): bool
    {
        $subject = 'Welcome to CarWise.ai - Your AI Car Diagnosis Platform';
        
        $content = $this->getWelcomeEmailTemplate($name, $userData);
        
        return $this->sendEmail($to, $subject, $content, [
            'is_html' => true,
            'to_name' => $name,
            'template_data' => [
                'name' => $name,
                'email' => $to,
                'app_url' => config('app.url'),
                'support_email' => config('services.sendgrid.reply_to', 'support@carwise.ai'),
            ]
        ]);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $to, string $name, string $resetToken): bool
    {
        $subject = 'Reset Your CarWise.ai Password';
        
        $resetUrl = config('app.url') . '/reset-password?token=' . $resetToken;
        
        $content = $this->getPasswordResetEmailTemplate($name, $resetUrl);
        
        return $this->sendEmail($to, $subject, $content, [
            'is_html' => true,
            'to_name' => $name,
        ]);
    }

    /**
     * Send diagnosis completion email
     */
    public function sendDiagnosisEmail(string $to, string $name, array $diagnosisData = []): bool
    {
        $subject = 'Your Car Diagnosis is Ready - CarWise.ai';
        
        $content = $this->getDiagnosisEmailTemplate($name, $diagnosisData);
        
        return $this->sendEmail($to, $subject, $content, [
            'is_html' => true,
            'to_name' => $name,
        ]);
    }

    /**
     * Send maintenance reminder email
     */
    public function sendMaintenanceReminderEmail(string $to, string $name, array $carData = []): bool
    {
        $subject = 'Maintenance Reminder for Your ' . ($carData['brand'] ?? 'Vehicle');
        
        $content = $this->getMaintenanceReminderEmailTemplate($name, $carData);
        
        return $this->sendEmail($to, $subject, $content, [
            'is_html' => true,
            'to_name' => $name,
        ]);
    }

    /**
     * Send notification email
     */
    public function sendNotificationEmail(string $to, string $name, string $title, string $message, array $options = []): bool
    {
        $subject = $title;
        
        $content = $this->getNotificationEmailTemplate($name, $title, $message, $options);
        
        return $this->sendEmail($to, $subject, $content, [
            'is_html' => true,
            'to_name' => $name,
        ]);
    }

    /**
     * Send bulk email
     */
    public function sendBulkEmail(array $recipients, string $subject, string $content, array $options = []): array
    {
        $results = [];
        
        foreach ($recipients as $recipient) {
            $results[$recipient['email']] = $this->sendEmail(
                $recipient['email'],
                $subject,
                $content,
                array_merge($options, [
                    'to_name' => $recipient['name'] ?? '',
                ])
            );
        }
        
        return $results;
    }

    /**
     * Get welcome email template
     */
    private function getWelcomeEmailTemplate(string $name, array $userData = []): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Welcome to CarWise.ai</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🚗 Welcome to CarWise.ai!</h1>
                    <p>Your AI-Powered Car Diagnosis Platform</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name}!</h2>
                    <p>Welcome to CarWise.ai, the most advanced AI-powered car diagnosis platform. We're excited to have you on board!</p>
                    
                    <h3>What you can do with CarWise.ai:</h3>
                    <ul>
                        <li>🔍 <strong>AI Car Diagnosis</strong> - Get instant, accurate diagnosis for your vehicle problems</li>
                        <li>🚙 <strong>Car Management</strong> - Keep track of all your vehicles in one place</li>
                        <li>🔧 <strong>Part Search</strong> - Find authorized car parts for your vehicle</li>
                        <li>📊 <strong>Maintenance Tracking</strong> - Never miss important maintenance again</li>
                        <li>👨‍🔧 <strong>Mechanic Network</strong> - Connect with trusted mechanics in your area</li>
                    </ul>
                    
                    <p>Ready to get started? Click the button below to begin your first car diagnosis!</p>
                    
                    <a href='" . config('app.url') . "/diagnose' class='button'>Start Your First Diagnosis</a>
                    
                    <p>If you have any questions, feel free to reach out to our support team at " . config('services.sendgrid.reply_to', 'support@carwise.ai') . "</p>
                </div>
                <div class='footer'>
                    <p>© 2024 CarWise.ai. All rights reserved.</p>
                    <p>This email was sent to " . ($userData['email'] ?? 'you') . " because you signed up for CarWise.ai.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get password reset email template
     */
    private function getPasswordResetEmailTemplate(string $name, string $resetUrl): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Reset Your Password - CarWise.ai</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Password Reset</h1>
                    <p>CarWise.ai Account Security</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name}!</h2>
                    <p>We received a request to reset your password for your CarWise.ai account.</p>
                    
                    <p>Click the button below to reset your password:</p>
                    
                    <a href='{$resetUrl}' class='button'>Reset My Password</a>
                    
                    <div class='warning'>
                        <strong>⚠️ Security Notice:</strong>
                        <ul>
                            <li>This link will expire in 1 hour</li>
                            <li>If you didn't request this reset, please ignore this email</li>
                            <li>Never share your password with anyone</li>
                        </ul>
                    </div>
                    
                    <p>If the button doesn't work, copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; background: #f0f0f0; padding: 10px; border-radius: 5px;'>{$resetUrl}</p>
                </div>
                <div class='footer'>
                    <p>© 2024 CarWise.ai. All rights reserved.</p>
                    <p>If you have any questions, contact us at " . config('services.sendgrid.reply_to', 'support@carwise.ai') . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get diagnosis email template
     */
    private function getDiagnosisEmailTemplate(string $name, array $diagnosisData = []): string
    {
        $carInfo = $diagnosisData['car_brand'] ?? 'Unknown' . ' ' . $diagnosisData['car_model'] ?? 'Vehicle';
        $severity = $diagnosisData['severity'] ?? 'unknown';
        $confidence = $diagnosisData['confidence_score'] ?? 0;
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Your Car Diagnosis is Ready - CarWise.ai</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .diagnosis-box { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 5px solid #667eea; }
                .severity-high { border-left-color: #e74c3c; }
                .severity-medium { border-left-color: #f39c12; }
                .severity-low { border-left-color: #27ae60; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔍 Your Diagnosis is Ready!</h1>
                    <p>AI-Powered Car Analysis Complete</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name}!</h2>
                    <p>Your AI diagnosis for your <strong>{$carInfo}</strong> has been completed successfully.</p>
                    
                    <div class='diagnosis-box severity-{$severity}'>
                        <h3>📊 Diagnosis Summary</h3>
                        <p><strong>Vehicle:</strong> {$carInfo}</p>
                        <p><strong>Severity Level:</strong> " . ucfirst($severity) . "</p>
                        <p><strong>Confidence Score:</strong> {$confidence}%</p>
                        <p><strong>AI Provider:</strong> " . ($diagnosisData['ai_provider'] ?? 'OpenAI') . "</p>
                    </div>
                    
                    <p>View your complete diagnosis report with detailed analysis, recommended actions, and estimated costs.</p>
                    
                    <a href='" . config('app.url') . "/diagnosis/" . ($diagnosisData['session_id'] ?? '') . "' class='button'>View Full Report</a>
                    
                    <p>Need help understanding your diagnosis? Our support team is here to help!</p>
                </div>
                <div class='footer'>
                    <p>© 2024 CarWise.ai. All rights reserved.</p>
                    <p>Contact us at " . config('services.sendgrid.reply_to', 'support@carwise.ai') . " for any questions.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get maintenance reminder email template
     */
    private function getMaintenanceReminderEmailTemplate(string $name, array $carData = []): string
    {
        $carInfo = ($carData['brand'] ?? 'Unknown') . ' ' . ($carData['model'] ?? 'Vehicle') . ' (' . ($carData['year'] ?? 'N/A') . ')';
        $mileage = $carData['current_mileage'] ?? $carData['mileage'] ?? 'Unknown';
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Maintenance Reminder - CarWise.ai</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .reminder-box { background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 10px; margin: 20px 0; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔧 Maintenance Reminder</h1>
                    <p>Keep Your Vehicle in Top Condition</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name}!</h2>
                    <p>It's time for scheduled maintenance on your <strong>{$carInfo}</strong>.</p>
                    
                    <div class='reminder-box'>
                        <h3>📅 Maintenance Due</h3>
                        <p><strong>Vehicle:</strong> {$carInfo}</p>
                        <p><strong>Current Mileage:</strong> {$mileage} miles</p>
                        <p><strong>Maintenance Type:</strong> " . ($carData['maintenance_type'] ?? 'Regular Service') . "</p>
                        <p><strong>Estimated Cost:</strong> " . ($carData['estimated_cost'] ?? 'Contact mechanic for quote') . "</p>
                    </div>
                    
                    <p>Regular maintenance helps prevent costly repairs and keeps your vehicle running smoothly.</p>
                    
                    <a href='" . config('app.url') . "/my-cars' class='button'>View My Cars</a>
                    
                    <p>Need help finding a trusted mechanic? Check out our mechanic network!</p>
                </div>
                <div class='footer'>
                    <p>© 2024 CarWise.ai. All rights reserved.</p>
                    <p>Contact us at " . config('services.sendgrid.reply_to', 'support@carwise.ai') . " for any questions.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get notification email template
     */
    private function getNotificationEmailTemplate(string $name, string $title, string $message, array $options = []): string
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>{$title} - CarWise.ai</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .notification-box { background: white; padding: 20px; border-radius: 10px; margin: 20px 0; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔔 {$title}</h1>
                    <p>CarWise.ai Notification</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name}!</h2>
                    
                    <div class='notification-box'>
                        <p>{$message}</p>
                    </div>
                    
                    " . (isset($options['action_url']) ? "<a href='{$options['action_url']}' class='button'>" . ($options['action_text'] ?? 'Take Action') . "</a>" : '') . "
                </div>
                <div class='footer'>
                    <p>© 2024 CarWise.ai. All rights reserved.</p>
                    <p>Contact us at " . config('services.sendgrid.reply_to', 'support@carwise.ai') . " for any questions.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Get email service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'sendgrid_enabled' => $this->sendGridEnabled,
            'mailgun_enabled' => $this->mailgunEnabled,
            'sendgrid_configured' => !empty($this->sendGridApiKey),
            'mailgun_configured' => !empty($this->mailgunDomain) && !empty($this->mailgunSecret),
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName,
        ];
    }

    /**
     * Test email service
     */
    public function testEmailService(string $testEmail = null): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('Email service test skipped - not enabled');
            return false;
        }

        $testEmail = $testEmail ?? config('mail.test_email', 'test@example.com');
        
        try {
            $result = $this->sendNotificationEmail(
                $testEmail,
                'Test User',
                'Email Service Test - CarWise.ai',
                'This is a test email to verify that the email service is working correctly.',
                [
                    'action_url' => config('app.url'),
                    'action_text' => 'Visit CarWise.ai'
                ]
            );

            if ($result) {
                Log::info('Email service test successful', ['test_email' => $testEmail]);
                return true;
            } else {
                Log::error('Email service test failed', ['test_email' => $testEmail]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Email service test error: ' . $e->getMessage());
            return false;
        }
    }
}
