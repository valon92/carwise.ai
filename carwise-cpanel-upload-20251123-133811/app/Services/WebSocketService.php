<?php

namespace App\Services;

use Pusher\Pusher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WebSocketService
{
    private $pusherEnabled;
    private $pusher;
    private $websocketEnabled;
    private $websocketHost;
    private $websocketPort;

    public function __construct()
    {
        $this->pusherEnabled = config('services.pusher.enabled', false);
        $this->websocketEnabled = config('services.websocket.enabled', false);
        $this->websocketHost = config('services.websocket.host', 'localhost');
        $this->websocketPort = config('services.websocket.port', 6001);

        // Initialize Pusher if enabled
        if ($this->pusherEnabled) {
            $this->initializePusher();
        }
    }

    /**
     * Initialize Pusher
     */
    private function initializePusher(): void
    {
        try {
            $this->pusher = new Pusher(
                config('services.pusher.key'),
                config('services.pusher.secret'),
                config('services.pusher.app_id'),
                [
                    'cluster' => config('services.pusher.cluster', 'us2'),
                    'useTLS' => config('services.pusher.use_tls', true),
                    'host' => config('services.pusher.host'),
                    'port' => config('services.pusher.port', 443),
                    'scheme' => config('services.pusher.scheme', 'https'),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Pusher initialization failed: ' . $e->getMessage());
            $this->pusherEnabled = false;
        }
    }

    /**
     * Check if WebSocket service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->pusherEnabled || $this->websocketEnabled;
    }

    /**
     * Send message to specific channel
     */
    public function sendToChannel(string $channel, string $event, array $data): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('WebSocket service not enabled');
            return false;
        }

        try {
            if ($this->pusherEnabled && $this->pusher) {
                $result = $this->pusher->trigger($channel, $event, $data);
                
                if ($result) {
                    Log::info('Message sent to Pusher channel', [
                        'channel' => $channel,
                        'event' => $event,
                        'data' => $data
                    ]);
                    return true;
                }
            }

            // Fallback to direct WebSocket (if implemented)
            if ($this->websocketEnabled) {
                return $this->sendToWebSocket($channel, $event, $data);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to send message to channel', [
                'channel' => $channel,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send message to user's private channel
     */
    public function sendToUser(int $userId, string $event, array $data): bool
    {
        $channel = 'private-user.' . $userId;
        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(int $conversationId, array $messageData): bool
    {
        $channel = 'private-conversation.' . $conversationId;
        
        $data = [
            'type' => 'chat_message',
            'conversation_id' => $conversationId,
            'message' => $messageData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'message', $data);
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(int $conversationId, int $userId, bool $isTyping): bool
    {
        $channel = 'private-conversation.' . $conversationId;
        
        $data = [
            'type' => 'typing_indicator',
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'is_typing' => $isTyping,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'typing', $data);
    }

    /**
     * Send user online status
     */
    public function sendUserStatus(int $userId, string $status): bool
    {
        $channel = 'presence-online-users';
        
        $data = [
            'type' => 'user_status',
            'user_id' => $userId,
            'status' => $status, // online, offline, away, busy
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'status_update', $data);
    }

    /**
     * Send diagnosis update
     */
    public function sendDiagnosisUpdate(int $userId, array $diagnosisData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'diagnosis_update',
            'diagnosis' => $diagnosisData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'diagnosis_progress', $data);
    }

    /**
     * Send maintenance reminder
     */
    public function sendMaintenanceReminder(int $userId, array $carData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'maintenance_reminder',
            'car' => $carData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'maintenance_alert', $data);
    }

    /**
     * Send part availability notification
     */
    public function sendPartAvailability(int $userId, array $partData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'part_availability',
            'part' => $partData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'part_available', $data);
    }

    /**
     * Send price drop alert
     */
    public function sendPriceDropAlert(int $userId, array $priceData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'price_drop',
            'price' => $priceData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'price_alert', $data);
    }

    /**
     * Send system notification
     */
    public function sendSystemNotification(int $userId, string $title, string $message, array $options = []): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'system_notification',
            'title' => $title,
            'message' => $message,
            'options' => $options,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'notification', $data);
    }

    /**
     * Send broadcast message to all users
     */
    public function sendBroadcast(string $event, array $data): bool
    {
        $channel = 'public-broadcast';
        
        $data = array_merge($data, [
            'type' => 'broadcast',
            'timestamp' => now()->toISOString(),
        ]);

        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send message to user segment
     */
    public function sendToUserSegment(array $userIds, string $event, array $data): array
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            $results[$userId] = $this->sendToUser($userId, $event, $data);
        }
        
        return $results;
    }

    /**
     * Send message to online users
     */
    public function sendToOnlineUsers(string $event, array $data): bool
    {
        $channel = 'presence-online-users';
        
        $data = array_merge($data, [
            'type' => 'online_broadcast',
            'timestamp' => now()->toISOString(),
        ]);

        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send message to specific role
     */
    public function sendToRole(string $role, string $event, array $data): bool
    {
        $channel = 'private-role.' . $role;
        
        $data = array_merge($data, [
            'type' => 'role_message',
            'role' => $role,
            'timestamp' => now()->toISOString(),
        ]);

        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send message to mechanics
     */
    public function sendToMechanics(string $event, array $data): bool
    {
        return $this->sendToRole('mechanic', $event, $data);
    }

    /**
     * Send message to customers
     */
    public function sendToCustomers(string $event, array $data): bool
    {
        return $this->sendToRole('customer', $event, $data);
    }

    /**
     * Send message to admins
     */
    public function sendToAdmins(string $event, array $data): bool
    {
        return $this->sendToRole('admin', $event, $data);
    }

    /**
     * Get channel authentication signature
     */
    public function getChannelAuth(string $channel, string $socketId): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $auth = $this->pusher->authorizeChannel($channel, $socketId);
            return $auth;
        } catch (\Exception $e) {
            Log::error('Failed to authorize channel', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get presence channel authentication
     */
    public function getPresenceAuth(string $channel, string $socketId, array $userData): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $auth = $this->pusher->authorizePresenceChannel($channel, $socketId, $userData['user_id'], $userData);
            return $auth;
        } catch (\Exception $e) {
            Log::error('Failed to authorize presence channel', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send to direct WebSocket (fallback)
     */
    private function sendToWebSocket(string $channel, string $event, array $data): bool
    {
        // This would implement direct WebSocket communication
        // For now, just log the attempt
        Log::info('Direct WebSocket message (not implemented)', [
            'channel' => $channel,
            'event' => $event,
            'data' => $data
        ]);
        
        return false;
    }

    /**
     * Get WebSocket service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'pusher_enabled' => $this->pusherEnabled,
            'websocket_enabled' => $this->websocketEnabled,
            'pusher_configured' => $this->pusher !== null,
            'websocket_host' => $this->websocketHost,
            'websocket_port' => $this->websocketPort,
        ];
    }

    /**
     * Test WebSocket service
     */
    public function testWebSocketService(): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('WebSocket service test skipped - not enabled');
            return false;
        }

        try {
            $result = $this->sendBroadcast('test', [
                'message' => 'WebSocket service test',
                'timestamp' => now()->toISOString(),
            ]);

            if ($result) {
                Log::info('WebSocket service test successful');
                return true;
            } else {
                Log::error('WebSocket service test failed');
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WebSocket service test error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available channels
     */
    public function getAvailableChannels(): array
    {
        return [
            'public' => [
                'public-broadcast' => 'Broadcast to all users',
                'public-announcements' => 'System announcements',
            ],
            'private' => [
                'private-user.{id}' => 'Private user channel',
                'private-conversation.{id}' => 'Private conversation channel',
                'private-role.{role}' => 'Private role channel',
            ],
            'presence' => [
                'presence-online-users' => 'Online users presence',
                'presence-conversation.{id}' => 'Conversation presence',
            ],
        ];
    }

    /**
     * Get channel information
     */
    public function getChannelInfo(string $channel): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $info = $this->pusher->getChannelInfo($channel);
            return $info;
        } catch (\Exception $e) {
            Log::error('Failed to get channel info', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get channel users (for presence channels)
     */
    public function getChannelUsers(string $channel): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $users = $this->pusher->getPresenceUsers($channel);
            return $users;
        } catch (\Exception $e) {
            Log::error('Failed to get channel users', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }
}

namespace App\Services;

use Pusher\Pusher;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WebSocketService
{
    private $pusherEnabled;
    private $pusher;
    private $websocketEnabled;
    private $websocketHost;
    private $websocketPort;

    public function __construct()
    {
        $this->pusherEnabled = config('services.pusher.enabled', false);
        $this->websocketEnabled = config('services.websocket.enabled', false);
        $this->websocketHost = config('services.websocket.host', 'localhost');
        $this->websocketPort = config('services.websocket.port', 6001);

        // Initialize Pusher if enabled
        if ($this->pusherEnabled) {
            $this->initializePusher();
        }
    }

    /**
     * Initialize Pusher
     */
    private function initializePusher(): void
    {
        try {
            $this->pusher = new Pusher(
                config('services.pusher.key'),
                config('services.pusher.secret'),
                config('services.pusher.app_id'),
                [
                    'cluster' => config('services.pusher.cluster', 'us2'),
                    'useTLS' => config('services.pusher.use_tls', true),
                    'host' => config('services.pusher.host'),
                    'port' => config('services.pusher.port', 443),
                    'scheme' => config('services.pusher.scheme', 'https'),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Pusher initialization failed: ' . $e->getMessage());
            $this->pusherEnabled = false;
        }
    }

    /**
     * Check if WebSocket service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->pusherEnabled || $this->websocketEnabled;
    }

    /**
     * Send message to specific channel
     */
    public function sendToChannel(string $channel, string $event, array $data): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('WebSocket service not enabled');
            return false;
        }

        try {
            if ($this->pusherEnabled && $this->pusher) {
                $result = $this->pusher->trigger($channel, $event, $data);
                
                if ($result) {
                    Log::info('Message sent to Pusher channel', [
                        'channel' => $channel,
                        'event' => $event,
                        'data' => $data
                    ]);
                    return true;
                }
            }

            // Fallback to direct WebSocket (if implemented)
            if ($this->websocketEnabled) {
                return $this->sendToWebSocket($channel, $event, $data);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Failed to send message to channel', [
                'channel' => $channel,
                'event' => $event,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Send message to user's private channel
     */
    public function sendToUser(int $userId, string $event, array $data): bool
    {
        $channel = 'private-user.' . $userId;
        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(int $conversationId, array $messageData): bool
    {
        $channel = 'private-conversation.' . $conversationId;
        
        $data = [
            'type' => 'chat_message',
            'conversation_id' => $conversationId,
            'message' => $messageData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'message', $data);
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(int $conversationId, int $userId, bool $isTyping): bool
    {
        $channel = 'private-conversation.' . $conversationId;
        
        $data = [
            'type' => 'typing_indicator',
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'is_typing' => $isTyping,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'typing', $data);
    }

    /**
     * Send user online status
     */
    public function sendUserStatus(int $userId, string $status): bool
    {
        $channel = 'presence-online-users';
        
        $data = [
            'type' => 'user_status',
            'user_id' => $userId,
            'status' => $status, // online, offline, away, busy
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'status_update', $data);
    }

    /**
     * Send diagnosis update
     */
    public function sendDiagnosisUpdate(int $userId, array $diagnosisData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'diagnosis_update',
            'diagnosis' => $diagnosisData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'diagnosis_progress', $data);
    }

    /**
     * Send maintenance reminder
     */
    public function sendMaintenanceReminder(int $userId, array $carData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'maintenance_reminder',
            'car' => $carData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'maintenance_alert', $data);
    }

    /**
     * Send part availability notification
     */
    public function sendPartAvailability(int $userId, array $partData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'part_availability',
            'part' => $partData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'part_available', $data);
    }

    /**
     * Send price drop alert
     */
    public function sendPriceDropAlert(int $userId, array $priceData): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'price_drop',
            'price' => $priceData,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'price_alert', $data);
    }

    /**
     * Send system notification
     */
    public function sendSystemNotification(int $userId, string $title, string $message, array $options = []): bool
    {
        $channel = 'private-user.' . $userId;
        
        $data = [
            'type' => 'system_notification',
            'title' => $title,
            'message' => $message,
            'options' => $options,
            'timestamp' => now()->toISOString(),
        ];

        return $this->sendToChannel($channel, 'notification', $data);
    }

    /**
     * Send broadcast message to all users
     */
    public function sendBroadcast(string $event, array $data): bool
    {
        $channel = 'public-broadcast';
        
        $data = array_merge($data, [
            'type' => 'broadcast',
            'timestamp' => now()->toISOString(),
        ]);

        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send message to user segment
     */
    public function sendToUserSegment(array $userIds, string $event, array $data): array
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            $results[$userId] = $this->sendToUser($userId, $event, $data);
        }
        
        return $results;
    }

    /**
     * Send message to online users
     */
    public function sendToOnlineUsers(string $event, array $data): bool
    {
        $channel = 'presence-online-users';
        
        $data = array_merge($data, [
            'type' => 'online_broadcast',
            'timestamp' => now()->toISOString(),
        ]);

        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send message to specific role
     */
    public function sendToRole(string $role, string $event, array $data): bool
    {
        $channel = 'private-role.' . $role;
        
        $data = array_merge($data, [
            'type' => 'role_message',
            'role' => $role,
            'timestamp' => now()->toISOString(),
        ]);

        return $this->sendToChannel($channel, $event, $data);
    }

    /**
     * Send message to mechanics
     */
    public function sendToMechanics(string $event, array $data): bool
    {
        return $this->sendToRole('mechanic', $event, $data);
    }

    /**
     * Send message to customers
     */
    public function sendToCustomers(string $event, array $data): bool
    {
        return $this->sendToRole('customer', $event, $data);
    }

    /**
     * Send message to admins
     */
    public function sendToAdmins(string $event, array $data): bool
    {
        return $this->sendToRole('admin', $event, $data);
    }

    /**
     * Get channel authentication signature
     */
    public function getChannelAuth(string $channel, string $socketId): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $auth = $this->pusher->authorizeChannel($channel, $socketId);
            return $auth;
        } catch (\Exception $e) {
            Log::error('Failed to authorize channel', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get presence channel authentication
     */
    public function getPresenceAuth(string $channel, string $socketId, array $userData): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $auth = $this->pusher->authorizePresenceChannel($channel, $socketId, $userData['user_id'], $userData);
            return $auth;
        } catch (\Exception $e) {
            Log::error('Failed to authorize presence channel', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Send to direct WebSocket (fallback)
     */
    private function sendToWebSocket(string $channel, string $event, array $data): bool
    {
        // This would implement direct WebSocket communication
        // For now, just log the attempt
        Log::info('Direct WebSocket message (not implemented)', [
            'channel' => $channel,
            'event' => $event,
            'data' => $data
        ]);
        
        return false;
    }

    /**
     * Get WebSocket service status
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'pusher_enabled' => $this->pusherEnabled,
            'websocket_enabled' => $this->websocketEnabled,
            'pusher_configured' => $this->pusher !== null,
            'websocket_host' => $this->websocketHost,
            'websocket_port' => $this->websocketPort,
        ];
    }

    /**
     * Test WebSocket service
     */
    public function testWebSocketService(): bool
    {
        if (!$this->isEnabled()) {
            Log::warning('WebSocket service test skipped - not enabled');
            return false;
        }

        try {
            $result = $this->sendBroadcast('test', [
                'message' => 'WebSocket service test',
                'timestamp' => now()->toISOString(),
            ]);

            if ($result) {
                Log::info('WebSocket service test successful');
                return true;
            } else {
                Log::error('WebSocket service test failed');
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WebSocket service test error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available channels
     */
    public function getAvailableChannels(): array
    {
        return [
            'public' => [
                'public-broadcast' => 'Broadcast to all users',
                'public-announcements' => 'System announcements',
            ],
            'private' => [
                'private-user.{id}' => 'Private user channel',
                'private-conversation.{id}' => 'Private conversation channel',
                'private-role.{role}' => 'Private role channel',
            ],
            'presence' => [
                'presence-online-users' => 'Online users presence',
                'presence-conversation.{id}' => 'Conversation presence',
            ],
        ];
    }

    /**
     * Get channel information
     */
    public function getChannelInfo(string $channel): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $info = $this->pusher->getChannelInfo($channel);
            return $info;
        } catch (\Exception $e) {
            Log::error('Failed to get channel info', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get channel users (for presence channels)
     */
    public function getChannelUsers(string $channel): array
    {
        if (!$this->pusherEnabled || !$this->pusher) {
            return ['error' => 'Pusher not enabled'];
        }

        try {
            $users = $this->pusher->getPresenceUsers($channel);
            return $users;
        } catch (\Exception $e) {
            Log::error('Failed to get channel users', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);
            return ['error' => $e->getMessage()];
        }
    }
}














