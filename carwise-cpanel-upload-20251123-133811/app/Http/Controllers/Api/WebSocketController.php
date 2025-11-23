<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WebSocketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebSocketController extends Controller
{
    private $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        $this->webSocketService = $webSocketService;
    }

    /**
     * Authenticate channel access
     */
    public function authenticateChannel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'socket_id' => 'required|string',
            'channel_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $channelName = $request->channel_name;
            $socketId = $request->socket_id;

            // Check if user can access the channel
            if (!$this->canAccessChannel($user, $channelName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to channel'
                ], 403);
            }

            // Handle presence channels
            if (str_starts_with($channelName, 'presence-')) {
                $userData = [
                    'user_id' => $user->id,
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name ?? $user->first_name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                    ]
                ];
                
                $auth = $this->webSocketService->getPresenceAuth($channelName, $socketId, $userData);
            } else {
                $auth = $this->webSocketService->getChannelAuth($channelName, $socketId);
            }

            if (isset($auth['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed',
                    'error' => $auth['error']
                ], 500);
            }

            return response()->json($auth);

        } catch (\Exception $e) {
            Log::error('WebSocket channel authentication failed', [
                'user_id' => Auth::id(),
                'channel' => $request->channel_name,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send message to channel
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string',
            'event' => 'required|string',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $channel = $request->channel;
            $event = $request->event;
            $data = $request->data;

            // Add user context to message
            $data['user_id'] = $user->id;
            $data['user_name'] = $user->name ?? $user->first_name;
            $data['timestamp'] = now()->toISOString();

            // Check if user can send to channel
            if (!$this->canSendToChannel($user, $channel)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to send to channel'
                ], 403);
            }

            $result = $this->webSocketService->sendToChannel($channel, $event, $data);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send WebSocket message', [
                'user_id' => Auth::id(),
                'channel' => $request->channel,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer',
            'message' => 'required|string|max:1000',
            'message_type' => 'nullable|string|in:text,image,file,diagnosis',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $conversationId = $request->conversation_id;
            $message = $request->message;
            $messageType = $request->message_type ?? 'text';
            $attachments = $request->attachments ?? [];

            // Check if user can send to conversation
            if (!$this->canAccessConversation($user, $conversationId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to conversation'
                ], 403);
            }

            $messageData = [
                'id' => uniqid(),
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'user_name' => $user->name ?? $user->first_name,
                'user_avatar' => $user->avatar,
                'message' => $message,
                'message_type' => $messageType,
                'attachments' => $attachments,
                'timestamp' => now()->toISOString(),
            ];

            $result = $this->webSocketService->sendChatMessage($conversationId, $messageData);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chat message sent successfully',
                    'data' => $messageData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send chat message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send chat message', [
                'user_id' => Auth::id(),
                'conversation_id' => $request->conversation_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send chat message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer',
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $conversationId = $request->conversation_id;
            $isTyping = $request->is_typing;

            // Check if user can access conversation
            if (!$this->canAccessConversation($user, $conversationId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to conversation'
                ], 403);
            }

            $result = $this->webSocketService->sendTypingIndicator($conversationId, $user->id, $isTyping);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Typing indicator sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send typing indicator'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send typing indicator', [
                'user_id' => Auth::id(),
                'conversation_id' => $request->conversation_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send typing indicator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send user status update
     */
    public function sendUserStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:online,offline,away,busy',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $status = $request->status;

            $result = $this->webSocketService->sendUserStatus($user->id, $status);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'User status updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user status'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send user status', [
                'user_id' => Auth::id(),
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get WebSocket service status
     */
    public function getStatus(): JsonResponse
    {
        try {
            $status = $this->webSocketService->getStatus();
            $channels = $this->webSocketService->getAvailableChannels();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $status,
                    'channels' => $channels,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get WebSocket status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get WebSocket status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test WebSocket service
     */
    public function testWebSocket(): JsonResponse
    {
        try {
            $result = $this->webSocketService->testWebSocketService();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'WebSocket service test successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'WebSocket service test failed'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('WebSocket service test failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'WebSocket service test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get channel information
     */
    public function getChannelInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $channel = $request->channel;

            // Check if user can access channel info
            if (!$this->canAccessChannel($user, $channel)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to channel'
                ], 403);
            }

            $info = $this->webSocketService->getChannelInfo($channel);

            if (isset($info['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get channel info',
                    'error' => $info['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $info
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get channel info', [
                'user_id' => Auth::id(),
                'channel' => $request->channel,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get channel info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can access channel
     */
    private function canAccessChannel($user, string $channel): bool
    {
        // Public channels - everyone can access
        if (str_starts_with($channel, 'public-')) {
            return true;
        }

        // Private user channels - only the user can access
        if (str_starts_with($channel, 'private-user.')) {
            $channelUserId = (int) str_replace('private-user.', '', $channel);
            return $user->id === $channelUserId;
        }

        // Private conversation channels - check if user is part of conversation
        if (str_starts_with($channel, 'private-conversation.')) {
            $conversationId = (int) str_replace('private-conversation.', '', $channel);
            return $this->canAccessConversation($user, $conversationId);
        }

        // Private role channels - check if user has the role
        if (str_starts_with($channel, 'private-role.')) {
            $role = str_replace('private-role.', '', $channel);
            return $user->role === $role || $user->role === 'admin';
        }

        // Presence channels - check based on channel type
        if (str_starts_with($channel, 'presence-')) {
            if ($channel === 'presence-online-users') {
                return true; // Everyone can see online users
            }
            
            if (str_starts_with($channel, 'presence-conversation.')) {
                $conversationId = (int) str_replace('presence-conversation.', '', $channel);
                return $this->canAccessConversation($user, $conversationId);
            }
        }

        return false;
    }

    /**
     * Check if user can send to channel
     */
    private function canSendToChannel($user, string $channel): bool
    {
        // Public channels - everyone can send
        if (str_starts_with($channel, 'public-')) {
            return true;
        }

        // Private channels - use same logic as access
        return $this->canAccessChannel($user, $channel);
    }

    /**
     * Check if user can access conversation
     */
    private function canAccessConversation($user, int $conversationId): bool
    {
        // This would typically check the database for conversation participants
        // For now, return true for all authenticated users
        // In production, implement proper conversation access control
        
        return true;
    }
}

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WebSocketService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WebSocketController extends Controller
{
    private $webSocketService;

    public function __construct(WebSocketService $webSocketService)
    {
        $this->webSocketService = $webSocketService;
    }

    /**
     * Authenticate channel access
     */
    public function authenticateChannel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'socket_id' => 'required|string',
            'channel_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $channelName = $request->channel_name;
            $socketId = $request->socket_id;

            // Check if user can access the channel
            if (!$this->canAccessChannel($user, $channelName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to channel'
                ], 403);
            }

            // Handle presence channels
            if (str_starts_with($channelName, 'presence-')) {
                $userData = [
                    'user_id' => $user->id,
                    'user_info' => [
                        'id' => $user->id,
                        'name' => $user->name ?? $user->first_name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'avatar' => $user->avatar,
                    ]
                ];
                
                $auth = $this->webSocketService->getPresenceAuth($channelName, $socketId, $userData);
            } else {
                $auth = $this->webSocketService->getChannelAuth($channelName, $socketId);
            }

            if (isset($auth['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed',
                    'error' => $auth['error']
                ], 500);
            }

            return response()->json($auth);

        } catch (\Exception $e) {
            Log::error('WebSocket channel authentication failed', [
                'user_id' => Auth::id(),
                'channel' => $request->channel_name,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Authentication failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send message to channel
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string',
            'event' => 'required|string',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $channel = $request->channel;
            $event = $request->event;
            $data = $request->data;

            // Add user context to message
            $data['user_id'] = $user->id;
            $data['user_name'] = $user->name ?? $user->first_name;
            $data['timestamp'] = now()->toISOString();

            // Check if user can send to channel
            if (!$this->canSendToChannel($user, $channel)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to send to channel'
                ], 403);
            }

            $result = $this->webSocketService->sendToChannel($channel, $event, $data);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send WebSocket message', [
                'user_id' => Auth::id(),
                'channel' => $request->channel,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer',
            'message' => 'required|string|max:1000',
            'message_type' => 'nullable|string|in:text,image,file,diagnosis',
            'attachments' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $conversationId = $request->conversation_id;
            $message = $request->message;
            $messageType = $request->message_type ?? 'text';
            $attachments = $request->attachments ?? [];

            // Check if user can send to conversation
            if (!$this->canAccessConversation($user, $conversationId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to conversation'
                ], 403);
            }

            $messageData = [
                'id' => uniqid(),
                'conversation_id' => $conversationId,
                'user_id' => $user->id,
                'user_name' => $user->name ?? $user->first_name,
                'user_avatar' => $user->avatar,
                'message' => $message,
                'message_type' => $messageType,
                'attachments' => $attachments,
                'timestamp' => now()->toISOString(),
            ];

            $result = $this->webSocketService->sendChatMessage($conversationId, $messageData);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Chat message sent successfully',
                    'data' => $messageData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send chat message'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send chat message', [
                'user_id' => Auth::id(),
                'conversation_id' => $request->conversation_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send chat message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send typing indicator
     */
    public function sendTypingIndicator(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'conversation_id' => 'required|integer',
            'is_typing' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $conversationId = $request->conversation_id;
            $isTyping = $request->is_typing;

            // Check if user can access conversation
            if (!$this->canAccessConversation($user, $conversationId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to conversation'
                ], 403);
            }

            $result = $this->webSocketService->sendTypingIndicator($conversationId, $user->id, $isTyping);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Typing indicator sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send typing indicator'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send typing indicator', [
                'user_id' => Auth::id(),
                'conversation_id' => $request->conversation_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send typing indicator',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send user status update
     */
    public function sendUserStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:online,offline,away,busy',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $status = $request->status;

            $result = $this->webSocketService->sendUserStatus($user->id, $status);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'User status updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user status'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Failed to send user status', [
                'user_id' => Auth::id(),
                'status' => $request->status,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get WebSocket service status
     */
    public function getStatus(): JsonResponse
    {
        try {
            $status = $this->webSocketService->getStatus();
            $channels = $this->webSocketService->getAvailableChannels();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $status,
                    'channels' => $channels,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get WebSocket status', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get WebSocket status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test WebSocket service
     */
    public function testWebSocket(): JsonResponse
    {
        try {
            $result = $this->webSocketService->testWebSocketService();

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'WebSocket service test successful'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'WebSocket service test failed'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('WebSocket service test failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'WebSocket service test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get channel information
     */
    public function getChannelInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $channel = $request->channel;

            // Check if user can access channel info
            if (!$this->canAccessChannel($user, $channel)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied to channel'
                ], 403);
            }

            $info = $this->webSocketService->getChannelInfo($channel);

            if (isset($info['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get channel info',
                    'error' => $info['error']
                ], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $info
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get channel info', [
                'user_id' => Auth::id(),
                'channel' => $request->channel,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get channel info',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user can access channel
     */
    private function canAccessChannel($user, string $channel): bool
    {
        // Public channels - everyone can access
        if (str_starts_with($channel, 'public-')) {
            return true;
        }

        // Private user channels - only the user can access
        if (str_starts_with($channel, 'private-user.')) {
            $channelUserId = (int) str_replace('private-user.', '', $channel);
            return $user->id === $channelUserId;
        }

        // Private conversation channels - check if user is part of conversation
        if (str_starts_with($channel, 'private-conversation.')) {
            $conversationId = (int) str_replace('private-conversation.', '', $channel);
            return $this->canAccessConversation($user, $conversationId);
        }

        // Private role channels - check if user has the role
        if (str_starts_with($channel, 'private-role.')) {
            $role = str_replace('private-role.', '', $channel);
            return $user->role === $role || $user->role === 'admin';
        }

        // Presence channels - check based on channel type
        if (str_starts_with($channel, 'presence-')) {
            if ($channel === 'presence-online-users') {
                return true; // Everyone can see online users
            }
            
            if (str_starts_with($channel, 'presence-conversation.')) {
                $conversationId = (int) str_replace('presence-conversation.', '', $channel);
                return $this->canAccessConversation($user, $conversationId);
            }
        }

        return false;
    }

    /**
     * Check if user can send to channel
     */
    private function canSendToChannel($user, string $channel): bool
    {
        // Public channels - everyone can send
        if (str_starts_with($channel, 'public-')) {
            return true;
        }

        // Private channels - use same logic as access
        return $this->canAccessChannel($user, $channel);
    }

    /**
     * Check if user can access conversation
     */
    private function canAccessConversation($user, int $conversationId): bool
    {
        // This would typically check the database for conversation participants
        // For now, return true for all authenticated users
        // In production, implement proper conversation access control
        
        return true;
    }
}














