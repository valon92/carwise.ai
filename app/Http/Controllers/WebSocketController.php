<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;

class WebSocketController extends Controller implements MessageComponentInterface
{
    protected $clients;
    protected $subscriptions;

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
        $this->subscriptions = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection
        $this->clients->attach($conn);
        
        Log::info("New WebSocket connection: {$conn->resourceId}");
        
        // Send welcome message
        $conn->send(json_encode([
            'type' => 'welcome',
            'message' => 'Connected to CarWise real-time updates',
            'connection_id' => $conn->resourceId
        ]));
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $data = json_decode($msg, true);
            
            if (!$data || !isset($data['type'])) {
                $this->sendError($from, 'Invalid message format');
                return;
            }

            switch ($data['type']) {
                case 'auth':
                    $this->handleAuth($from, $data);
                    break;
                    
                case 'subscribe':
                    $this->handleSubscribe($from, $data);
                    break;
                    
                case 'unsubscribe':
                    $this->handleUnsubscribe($from, $data);
                    break;
                    
                case 'ping':
                    $this->handlePing($from);
                    break;
                    
                default:
                    $this->sendError($from, 'Unknown message type');
            }
            
        } catch (\Exception $e) {
            Log::error('WebSocket message error: ' . $e->getMessage());
            $this->sendError($from, 'Message processing error');
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Remove the connection
        $this->clients->detach($conn);
        
        // Remove from subscriptions
        foreach ($this->subscriptions as $channel => $connections) {
            if (($key = array_search($conn, $connections)) !== false) {
                unset($this->subscriptions[$channel][$key]);
            }
        }
        
        Log::info("Connection {$conn->resourceId} has disconnected");
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        Log::error("WebSocket error on connection {$conn->resourceId}: " . $e->getMessage());
        $conn->close();
    }

    protected function handleAuth(ConnectionInterface $conn, array $data)
    {
        // In a real implementation, validate the JWT token here
        $token = $data['token'] ?? null;
        
        if ($token) {
            // Store user info with connection (simplified)
            $conn->user_id = $this->validateToken($token);
            
            $conn->send(json_encode([
                'type' => 'auth_success',
                'message' => 'Authentication successful'
            ]));
        } else {
            $this->sendError($conn, 'Authentication failed');
        }
    }

    protected function handleSubscribe(ConnectionInterface $conn, array $data)
    {
        $channel = $data['channel'] ?? null;
        
        if (!$channel) {
            $this->sendError($conn, 'Channel name required');
            return;
        }

        // Add connection to channel subscription
        if (!isset($this->subscriptions[$channel])) {
            $this->subscriptions[$channel] = [];
        }
        
        $this->subscriptions[$channel][] = $conn;
        
        $conn->send(json_encode([
            'type' => 'subscription_success',
            'channel' => $channel,
            'message' => "Subscribed to {$channel}"
        ]));
        
        Log::info("Connection {$conn->resourceId} subscribed to {$channel}");
    }

    protected function handleUnsubscribe(ConnectionInterface $conn, array $data)
    {
        $channel = $data['channel'] ?? null;
        
        if ($channel && isset($this->subscriptions[$channel])) {
            if (($key = array_search($conn, $this->subscriptions[$channel])) !== false) {
                unset($this->subscriptions[$channel][$key]);
                
                $conn->send(json_encode([
                    'type' => 'unsubscription_success',
                    'channel' => $channel,
                    'message' => "Unsubscribed from {$channel}"
                ]));
            }
        }
    }

    protected function handlePing(ConnectionInterface $conn)
    {
        $conn->send(json_encode([
            'type' => 'pong',
            'timestamp' => now()->toISOString()
        ]));
    }

    protected function sendError(ConnectionInterface $conn, string $message)
    {
        $conn->send(json_encode([
            'type' => 'error',
            'message' => $message
        ]));
    }

    protected function validateToken($token)
    {
        // Simplified token validation - in real implementation use JWT
        // For now, just return a mock user ID
        return 'user_' . substr(md5($token), 0, 8);
    }

    /**
     * Broadcast stock update to all subscribed clients
     */
    public function broadcastStockUpdate(array $stockData)
    {
        $message = json_encode([
            'type' => 'stock_update',
            'payload' => $stockData,
            'timestamp' => now()->toISOString()
        ]);

        $this->broadcastToChannel('stock_updates', $message);
    }

    /**
     * Broadcast bulk stock updates
     */
    public function broadcastBulkStockUpdate(array $stockUpdates)
    {
        $message = json_encode([
            'type' => 'bulk_update',
            'payload' => $stockUpdates,
            'timestamp' => now()->toISOString()
        ]);

        $this->broadcastToChannel('stock_updates', $message);
    }

    /**
     * Send stock alert to subscribed clients
     */
    public function broadcastStockAlert(array $alertData)
    {
        $message = json_encode([
            'type' => 'stock_alert',
            'payload' => $alertData,
            'timestamp' => now()->toISOString()
        ]);

        $this->broadcastToChannel('stock_updates', $message);
    }

    /**
     * Broadcast message to all connections in a channel
     */
    protected function broadcastToChannel(string $channel, string $message)
    {
        if (!isset($this->subscriptions[$channel])) {
            return;
        }

        foreach ($this->subscriptions[$channel] as $conn) {
            try {
                $conn->send($message);
            } catch (\Exception $e) {
                Log::error("Failed to send message to connection: " . $e->getMessage());
                // Remove broken connection
                if (($key = array_search($conn, $this->subscriptions[$channel])) !== false) {
                    unset($this->subscriptions[$channel][$key]);
                }
            }
        }
    }
}

