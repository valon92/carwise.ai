<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    private $firebaseEnabled;
    private $oneSignalEnabled;
    private $firebaseMessaging;
    private $oneSignalAppId;
    private $oneSignalRestApiKey;

    public function __construct()
    {
        $this->firebaseEnabled = config('services.firebase.enabled', false);
        $this->oneSignalEnabled = config('services.onesignal.enabled', false);
        $this->oneSignalAppId = config('services.onesignal.app_id');
        $this->oneSignalRestApiKey = config('services.onesignal.rest_api_key');

        // Initialize Firebase if enabled
        if ($this->firebaseEnabled) {
            $this->initializeFirebase();
        }
    }

    /**
     * Initialize Firebase messaging
     */
    private function initializeFirebase(): void
    {
        try {
            $firebaseConfig = [
                'type' => 'service_account',
                'project_id' => config('services.firebase.project_id'),
                'private_key_id' => config('services.firebase.private_key_id'),
                'private_key' => config('services.firebase.private_key'),
                'client_email' => config('services.firebase.client_email'),
                'client_id' => config('services.firebase.client_id'),
                'auth_uri' => config('services.firebase.auth_uri'),
                'token_uri' => config('services.firebase.token_uri'),
                'auth_provider_x509_cert_url' => config('services.firebase.auth_provider_x509_cert_url'),
                'client_x509_cert_url' => config('services.firebase.client_x509_cert_url'),
            ];

            $factory = (new Factory)->withServiceAccount($firebaseConfig);
            $this->firebaseMessaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            $this->firebaseEnabled = false;
        }
    }

    /**
     * Check if push notification service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->firebaseEnabled || $this->oneSignalEnabled;
    }

    /**
     * Send push notification via Firebase
     */
    private function sendViaFirebase(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->firebaseEnabled || !$this->firebaseMessaging) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);

            // Add platform-specific configurations
            $message = $message->withAndroidConfig(
                AndroidConfig::fromArray([
                    'notification' => [
                        'sound' => 'default',
                        'color' => '#667eea',
                        'icon' => 'ic_notification',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ])
            );

            $message = $message->withApnsConfig(
                ApnsConfig::fromArray([
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ])
            );

            $message = $message->withWebPushConfig(
                WebPushConfig::fromArray([
                    'notification' => [
                        'icon' => '/icons/icon-192x192.png',
                        'badge' => '/icons/icon-72x72.png',
                        'requireInteraction' => true,
                    ],
                ])
            );

            $result = $this->firebaseMessaging->send($message);

            Log::info('Firebase push notification sent successfully', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'message_id' => $result
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase push notification failed', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send push notification via OneSignal
     */
    private function sendViaOneSignal(string $playerId, string $title, string $body, array $data = []): bool
    {
        if (!$this->oneSignalEnabled || !$this->oneSignalAppId || !$this->oneSignalRestApiKey) {
            return false;
        }

        try {
            $payload = [
                'app_id' => $this->oneSignalAppId,
                'include_player_ids' => [$playerId],
                'headings' => ['en' => $title],
                'contents' => ['en' => $body],
                'data' => $data,
                'small_icon' => 'ic_notification',
                'large_icon' => 'ic_launcher',
                'android_accent_color' => 'FF667eea',
                'ios_badgeType' => 'Increase',
                'ios_badgeCount' => 1,
                'web_icon' => '/icons/icon-192x192.png',
                'web_badge' => '/icons/icon-72x72.png',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->oneSignalRestApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('OneSignal push notification sent successfully', [
                    'player_id' => substr($playerId, 0, 20) . '...',
                    'title' => $title,
                    'notification_id' => $responseData['id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::warning('OneSignal push notification failed', [
                    'player_id' => substr($playerId, 0, 20) . '...',
                    'title' => $title,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('OneSignal push notification error', [
                'player_id' => substr($playerId, 0, 20) . '...',
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send push notification (automatically chooses provider)
     */
    public function sendPushNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('Push notification service not enabled');
            return false;
        }

        // Try Firebase first, then OneSignal
        if ($this->firebaseEnabled) {
            $result = $this->sendViaFirebase($token, $title, $body, $data);
            if ($result) {
                return true;
            }
        }

        if ($this->oneSignalEnabled) {
            return $this->sendViaOneSignal($token, $title, $body, $data);
        }

        return false;
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        foreach ($tokens as $token) {
            $results[$token] = $this->sendPushNotification($token, $title, $body, $data);
        }
        
        return $results;
    }

    /**
     * Send diagnosis complete notification
     */
    public function sendDiagnosisCompleteNotification(string $token, array $diagnosisData = []): bool
    {
        $carInfo = ($diagnosisData['car_brand'] ?? 'Unknown') . ' ' . ($diagnosisData['car_model'] ?? 'Vehicle');
        $severity = $diagnosisData['severity'] ?? 'unknown';
        
        $title = '🔍 Diagnosis Complete!';
        $body = "Your {$carInfo} diagnosis is ready. Severity: " . ucfirst($severity);
        
        $data = [
            'type' => 'diagnosis_complete',
            'session_id' => $diagnosisData['session_id'] ?? '',
            'car_brand' => $diagnosisData['car_brand'] ?? '',
            'car_model' => $diagnosisData['car_model'] ?? '',
            'severity' => $severity,
            'confidence_score' => $diagnosisData['confidence_score'] ?? 0,
            'url' => '/diagnosis/' . ($diagnosisData['session_id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send maintenance reminder notification
     */
    public function sendMaintenanceReminderNotification(string $token, array $carData = []): bool
    {
        $carInfo = ($carData['brand'] ?? 'Unknown') . ' ' . ($carData['model'] ?? 'Vehicle');
        
        $title = '🔧 Maintenance Reminder';
        $body = "Time for maintenance on your {$carInfo}";
        
        $data = [
            'type' => 'maintenance_reminder',
            'car_id' => $carData['id'] ?? '',
            'car_brand' => $carData['brand'] ?? '',
            'car_model' => $carData['model'] ?? '',
            'maintenance_type' => $carData['maintenance_type'] ?? 'Regular Service',
            'estimated_cost' => $carData['estimated_cost'] ?? '',
            'url' => '/my-cars',
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send welcome notification
     */
    public function sendWelcomeNotification(string $token, string $userName = 'User'): bool
    {
        $title = '🚗 Welcome to CarWise.ai!';
        $body = "Hello {$userName}! Start your first AI car diagnosis now.";
        
        $data = [
            'type' => 'welcome',
            'url' => '/diagnose',
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send part availability notification
     */
    public function sendPartAvailabilityNotification(string $token, array $partData = []): bool
    {
        $partName = $partData['name'] ?? 'Car Part';
        $carInfo = ($partData['car_brand'] ?? '') . ' ' . ($partData['car_model'] ?? '');
        
        $title = '🔧 Part Available!';
        $body = "{$partName} for {$carInfo} is now in stock!";
        
        $data = [
            'type' => 'part_available',
            'part_id' => $partData['id'] ?? '',
            'part_name' => $partName,
            'car_brand' => $partData['car_brand'] ?? '',
            'car_model' => $partData['car_model'] ?? '',
            'price' => $partData['price'] ?? 0,
            'url' => '/car-parts/' . ($partData['id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send price drop notification
     */
    public function sendPriceDropNotification(string $token, array $partData = []): bool
    {
        $partName = $partData['name'] ?? 'Car Part';
        $oldPrice = $partData['old_price'] ?? 0;
        $newPrice = $partData['new_price'] ?? 0;
        $discount = $oldPrice - $newPrice;
        
        $title = '💰 Price Drop Alert!';
        $body = "{$partName} price dropped by \${$discount}! Now \${$newPrice}";
        
        $data = [
            'type' => 'price_drop',
            'part_id' => $partData['id'] ?? '',
            'part_name' => $partName,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'discount' => $discount,
            'url' => '/car-parts/' . ($partData['id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send mechanic appointment notification
     */
    public function sendMechanicAppointmentNotification(string $token, array $appointmentData = []): bool
    {
        $mechanicName = $appointmentData['mechanic_name'] ?? 'Mechanic';
        $appointmentDate = $appointmentData['appointment_date'] ?? 'soon';
        
        $title = '👨‍🔧 Appointment Reminder';
        $body = "Your appointment with {$mechanicName} is {$appointmentDate}";
        
        $data = [
            'type' => 'appointment_reminder',
            'appointment_id' => $appointmentData['id'] ?? '',
            'mechanic_name' => $mechanicName,
            'appointment_date' => $appointmentDate,
            'mechanic_id' => $appointmentData['mechanic_id'] ?? '',
            'url' => '/appointments/' . ($appointmentData['id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification(string $token, string $title, string $body, array $data = []): bool
    {
        $data['type'] = $data['type'] ?? 'custom';
        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send broadcast notification to all users
     */
    public function sendBroadcastNotification(string $title, string $body, array $data = []): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        // This would typically query the database for all user tokens
        // For now, return false as we need user tokens
        Log::warning('Broadcast notification requires user token collection');
        return false;
    }

    /**
     * Send notification to user segment
     */
    public function sendToUserSegment(array $userIds, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        // This would typically query the database for user tokens
        // For now, return empty results
        Log::warning('User segment notification requires user token collection');
        
        return $results;
    }

    /**
     * Get push notification service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'firebase_enabled' => $this->firebaseEnabled,
            'onesignal_enabled' => $this->oneSignalEnabled,
            'firebase_configured' => $this->firebaseMessaging !== null,
            'onesignal_configured' => !empty($this->oneSignalAppId) && !empty($this->oneSignalRestApiKey),
        ];
    }

    /**
     * Test push notification service
     */
    public function testPushNotificationService(string $testToken = null): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('Push notification service test skipped - not enabled');
            return false;
        }

        $testToken = $testToken ?? 'test_token_12345';
        
        try {
            $result = $this->sendCustomNotification(
                $testToken,
                'Test Notification - CarWise.ai',
                'This is a test notification to verify the push notification service is working correctly.',
                [
                    'type' => 'test',
                    'timestamp' => now()->toISOString(),
                    'url' => config('app.url')
                ]
            );

            if ($result) {
                Log::info('Push notification service test successful', ['test_token' => substr($testToken, 0, 20) . '...']);
                return true;
            } else {
                Log::error('Push notification service test failed', ['test_token' => substr($testToken, 0, 20) . '...']);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Push notification service test error: ' . $e->getMessage());
            return false;
        }
    }
}

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\ApnsConfig;
use Kreait\Firebase\Messaging\WebPushConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    private $firebaseEnabled;
    private $oneSignalEnabled;
    private $firebaseMessaging;
    private $oneSignalAppId;
    private $oneSignalRestApiKey;

    public function __construct()
    {
        $this->firebaseEnabled = config('services.firebase.enabled', false);
        $this->oneSignalEnabled = config('services.onesignal.enabled', false);
        $this->oneSignalAppId = config('services.onesignal.app_id');
        $this->oneSignalRestApiKey = config('services.onesignal.rest_api_key');

        // Initialize Firebase if enabled
        if ($this->firebaseEnabled) {
            $this->initializeFirebase();
        }
    }

    /**
     * Initialize Firebase messaging
     */
    private function initializeFirebase(): void
    {
        try {
            $firebaseConfig = [
                'type' => 'service_account',
                'project_id' => config('services.firebase.project_id'),
                'private_key_id' => config('services.firebase.private_key_id'),
                'private_key' => config('services.firebase.private_key'),
                'client_email' => config('services.firebase.client_email'),
                'client_id' => config('services.firebase.client_id'),
                'auth_uri' => config('services.firebase.auth_uri'),
                'token_uri' => config('services.firebase.token_uri'),
                'auth_provider_x509_cert_url' => config('services.firebase.auth_provider_x509_cert_url'),
                'client_x509_cert_url' => config('services.firebase.client_x509_cert_url'),
            ];

            $factory = (new Factory)->withServiceAccount($firebaseConfig);
            $this->firebaseMessaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization failed: ' . $e->getMessage());
            $this->firebaseEnabled = false;
        }
    }

    /**
     * Check if push notification service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->firebaseEnabled || $this->oneSignalEnabled;
    }

    /**
     * Send push notification via Firebase
     */
    private function sendViaFirebase(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->firebaseEnabled || !$this->firebaseMessaging) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body);
            
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);

            // Add platform-specific configurations
            $message = $message->withAndroidConfig(
                AndroidConfig::fromArray([
                    'notification' => [
                        'sound' => 'default',
                        'color' => '#667eea',
                        'icon' => 'ic_notification',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ])
            );

            $message = $message->withApnsConfig(
                ApnsConfig::fromArray([
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ])
            );

            $message = $message->withWebPushConfig(
                WebPushConfig::fromArray([
                    'notification' => [
                        'icon' => '/icons/icon-192x192.png',
                        'badge' => '/icons/icon-72x72.png',
                        'requireInteraction' => true,
                    ],
                ])
            );

            $result = $this->firebaseMessaging->send($message);

            Log::info('Firebase push notification sent successfully', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'message_id' => $result
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Firebase push notification failed', [
                'token' => substr($token, 0, 20) . '...',
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send push notification via OneSignal
     */
    private function sendViaOneSignal(string $playerId, string $title, string $body, array $data = []): bool
    {
        if (!$this->oneSignalEnabled || !$this->oneSignalAppId || !$this->oneSignalRestApiKey) {
            return false;
        }

        try {
            $payload = [
                'app_id' => $this->oneSignalAppId,
                'include_player_ids' => [$playerId],
                'headings' => ['en' => $title],
                'contents' => ['en' => $body],
                'data' => $data,
                'small_icon' => 'ic_notification',
                'large_icon' => 'ic_launcher',
                'android_accent_color' => 'FF667eea',
                'ios_badgeType' => 'Increase',
                'ios_badgeCount' => 1,
                'web_icon' => '/icons/icon-192x192.png',
                'web_badge' => '/icons/icon-72x72.png',
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->oneSignalRestApiKey,
                'Content-Type' => 'application/json',
            ])->post('https://onesignal.com/api/v1/notifications', $payload);

            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('OneSignal push notification sent successfully', [
                    'player_id' => substr($playerId, 0, 20) . '...',
                    'title' => $title,
                    'notification_id' => $responseData['id'] ?? 'unknown'
                ]);
                return true;
            } else {
                Log::warning('OneSignal push notification failed', [
                    'player_id' => substr($playerId, 0, 20) . '...',
                    'title' => $title,
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('OneSignal push notification error', [
                'player_id' => substr($playerId, 0, 20) . '...',
                'title' => $title,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send push notification (automatically chooses provider)
     */
    public function sendPushNotification(string $token, string $title, string $body, array $data = []): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('Push notification service not enabled');
            return false;
        }

        // Try Firebase first, then OneSignal
        if ($this->firebaseEnabled) {
            $result = $this->sendViaFirebase($token, $title, $body, $data);
            if ($result) {
                return true;
            }
        }

        if ($this->oneSignalEnabled) {
            return $this->sendViaOneSignal($token, $title, $body, $data);
        }

        return false;
    }

    /**
     * Send notification to multiple devices
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        foreach ($tokens as $token) {
            $results[$token] = $this->sendPushNotification($token, $title, $body, $data);
        }
        
        return $results;
    }

    /**
     * Send diagnosis complete notification
     */
    public function sendDiagnosisCompleteNotification(string $token, array $diagnosisData = []): bool
    {
        $carInfo = ($diagnosisData['car_brand'] ?? 'Unknown') . ' ' . ($diagnosisData['car_model'] ?? 'Vehicle');
        $severity = $diagnosisData['severity'] ?? 'unknown';
        
        $title = '🔍 Diagnosis Complete!';
        $body = "Your {$carInfo} diagnosis is ready. Severity: " . ucfirst($severity);
        
        $data = [
            'type' => 'diagnosis_complete',
            'session_id' => $diagnosisData['session_id'] ?? '',
            'car_brand' => $diagnosisData['car_brand'] ?? '',
            'car_model' => $diagnosisData['car_model'] ?? '',
            'severity' => $severity,
            'confidence_score' => $diagnosisData['confidence_score'] ?? 0,
            'url' => '/diagnosis/' . ($diagnosisData['session_id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send maintenance reminder notification
     */
    public function sendMaintenanceReminderNotification(string $token, array $carData = []): bool
    {
        $carInfo = ($carData['brand'] ?? 'Unknown') . ' ' . ($carData['model'] ?? 'Vehicle');
        
        $title = '🔧 Maintenance Reminder';
        $body = "Time for maintenance on your {$carInfo}";
        
        $data = [
            'type' => 'maintenance_reminder',
            'car_id' => $carData['id'] ?? '',
            'car_brand' => $carData['brand'] ?? '',
            'car_model' => $carData['model'] ?? '',
            'maintenance_type' => $carData['maintenance_type'] ?? 'Regular Service',
            'estimated_cost' => $carData['estimated_cost'] ?? '',
            'url' => '/my-cars',
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send welcome notification
     */
    public function sendWelcomeNotification(string $token, string $userName = 'User'): bool
    {
        $title = '🚗 Welcome to CarWise.ai!';
        $body = "Hello {$userName}! Start your first AI car diagnosis now.";
        
        $data = [
            'type' => 'welcome',
            'url' => '/diagnose',
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send part availability notification
     */
    public function sendPartAvailabilityNotification(string $token, array $partData = []): bool
    {
        $partName = $partData['name'] ?? 'Car Part';
        $carInfo = ($partData['car_brand'] ?? '') . ' ' . ($partData['car_model'] ?? '');
        
        $title = '🔧 Part Available!';
        $body = "{$partName} for {$carInfo} is now in stock!";
        
        $data = [
            'type' => 'part_available',
            'part_id' => $partData['id'] ?? '',
            'part_name' => $partName,
            'car_brand' => $partData['car_brand'] ?? '',
            'car_model' => $partData['car_model'] ?? '',
            'price' => $partData['price'] ?? 0,
            'url' => '/car-parts/' . ($partData['id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send price drop notification
     */
    public function sendPriceDropNotification(string $token, array $partData = []): bool
    {
        $partName = $partData['name'] ?? 'Car Part';
        $oldPrice = $partData['old_price'] ?? 0;
        $newPrice = $partData['new_price'] ?? 0;
        $discount = $oldPrice - $newPrice;
        
        $title = '💰 Price Drop Alert!';
        $body = "{$partName} price dropped by \${$discount}! Now \${$newPrice}";
        
        $data = [
            'type' => 'price_drop',
            'part_id' => $partData['id'] ?? '',
            'part_name' => $partName,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'discount' => $discount,
            'url' => '/car-parts/' . ($partData['id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send mechanic appointment notification
     */
    public function sendMechanicAppointmentNotification(string $token, array $appointmentData = []): bool
    {
        $mechanicName = $appointmentData['mechanic_name'] ?? 'Mechanic';
        $appointmentDate = $appointmentData['appointment_date'] ?? 'soon';
        
        $title = '👨‍🔧 Appointment Reminder';
        $body = "Your appointment with {$mechanicName} is {$appointmentDate}";
        
        $data = [
            'type' => 'appointment_reminder',
            'appointment_id' => $appointmentData['id'] ?? '',
            'mechanic_name' => $mechanicName,
            'appointment_date' => $appointmentDate,
            'mechanic_id' => $appointmentData['mechanic_id'] ?? '',
            'url' => '/appointments/' . ($appointmentData['id'] ?? ''),
        ];

        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send custom notification
     */
    public function sendCustomNotification(string $token, string $title, string $body, array $data = []): bool
    {
        $data['type'] = $data['type'] ?? 'custom';
        return $this->sendPushNotification($token, $title, $body, $data);
    }

    /**
     * Send broadcast notification to all users
     */
    public function sendBroadcastNotification(string $title, string $body, array $data = []): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        // This would typically query the database for all user tokens
        // For now, return false as we need user tokens
        Log::warning('Broadcast notification requires user token collection');
        return false;
    }

    /**
     * Send notification to user segment
     */
    public function sendToUserSegment(array $userIds, string $title, string $body, array $data = []): array
    {
        $results = [];
        
        // This would typically query the database for user tokens
        // For now, return empty results
        Log::warning('User segment notification requires user token collection');
        
        return $results;
    }

    /**
     * Get push notification service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'firebase_enabled' => $this->firebaseEnabled,
            'onesignal_enabled' => $this->oneSignalEnabled,
            'firebase_configured' => $this->firebaseMessaging !== null,
            'onesignal_configured' => !empty($this->oneSignalAppId) && !empty($this->oneSignalRestApiKey),
        ];
    }

    /**
     * Test push notification service
     */
    public function testPushNotificationService(string $testToken = null): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('Push notification service test skipped - not enabled');
            return false;
        }

        $testToken = $testToken ?? 'test_token_12345';
        
        try {
            $result = $this->sendCustomNotification(
                $testToken,
                'Test Notification - CarWise.ai',
                'This is a test notification to verify the push notification service is working correctly.',
                [
                    'type' => 'test',
                    'timestamp' => now()->toISOString(),
                    'url' => config('app.url')
                ]
            );

            if ($result) {
                Log::info('Push notification service test successful', ['test_token' => substr($testToken, 0, 20) . '...']);
                return true;
            } else {
                Log::error('Push notification service test failed', ['test_token' => substr($testToken, 0, 20) . '...']);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Push notification service test error: ' . $e->getMessage());
            return false;
        }
    }
}














