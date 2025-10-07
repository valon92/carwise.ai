<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Events\ChatMessageSent;
use App\Events\ChatTypingIndicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Get user's conversations.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConversations(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $query = ChatConversation::where('user_id', $user->id)
            ->with(['latestMessage', 'agent:id,name,email'])
            ->orderBy('updated_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = $request->input('per_page', 20);
        $conversations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $conversations->items(),
            'pagination' => [
                'current_page' => $conversations->currentPage(),
                'total_pages' => $conversations->lastPage(),
                'total_count' => $conversations->total(),
                'per_page' => $conversations->perPage()
            ]
        ]);
    }

    /**
     * Start a new conversation.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'initial_message' => 'sometimes|string|max:1000',
            'subject' => 'sometimes|string|max:255',
            'priority' => 'sometimes|in:low,medium,high,urgent',
            'user_agent' => 'sometimes|string',
            'page_url' => 'sometimes|url'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Check if user has an active conversation
            $activeConversation = null;
            if ($user) {
                $activeConversation = ChatConversation::where('user_id', $user->id)
                    ->active()
                    ->first();
            }

            if ($activeConversation) {
                // Return existing active conversation
                $activeConversation->load(['messages', 'agent:id,name,email,avatar']);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Existing conversation found',
                    'data' => $activeConversation
                ]);
            }

            // Create new conversation
            $conversation = ChatConversation::create([
                'user_id' => $user ? $user->id : null,
                'status' => ChatConversation::STATUS_WAITING,
                'subject' => $request->input('subject', 'General Support'),
                'priority' => $request->input('priority', ChatConversation::PRIORITY_MEDIUM),
                'channel' => ChatConversation::CHANNEL_WEBSITE,
                'user_agent' => $request->input('user_agent'),
                'page_url' => $request->input('page_url'),
                'ip_address' => $request->ip(),
                'metadata' => [
                    'browser' => $request->header('User-Agent'),
                    'referrer' => $request->header('Referer'),
                    'created_via' => 'chat_widget'
                ]
            ]);

            // Send initial message if provided
            if ($request->has('initial_message')) {
                $message = $conversation->messages()->create([
                    'sender_id' => $user ? $user->id : null,
                    'sender_type' => ChatMessage::SENDER_TYPE_USER,
                    'sender_name' => $user ? $user->name : 'Guest',
                    'content' => $request->initial_message,
                    'type' => ChatMessage::TYPE_TEXT
                ]);

                // Broadcast message
                broadcast(new ChatMessageSent($message))->toOthers();
            }

            // Create welcome system message
            $welcomeMessage = $conversation->messages()->create([
                'sender_type' => ChatMessage::SENDER_TYPE_SYSTEM,
                'sender_name' => 'System',
                'content' => 'Welcome to CarWise Support! An agent will be with you shortly.',
                'type' => ChatMessage::TYPE_SYSTEM
            ]);

            // Load relationships
            $conversation->load(['messages', 'agent:id,name,email,avatar']);

            // Notify available agents about new conversation
            $this->notifyAvailableAgents($conversation);

            return response()->json([
                'success' => true,
                'message' => 'Conversation started successfully',
                'data' => $conversation
            ]);

        } catch (\Exception $e) {
            Log::error('Error starting conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to start conversation'
            ], 500);
        }
    }

    /**
     * Get conversation messages.
     *
     * @param int $conversationId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages($conversationId, Request $request)
    {
        $user = Auth::user();
        
        $conversation = ChatConversation::find($conversationId);
        
        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        // Check if user owns the conversation or is an agent
        if ($user && $conversation->user_id !== $user->id && !$user->isAgent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $query = $conversation->messages()
            ->with(['sender:id,name,email'])
            ->orderBy('created_at', 'asc');

        // Pagination
        $perPage = $request->input('per_page', 50);
        $messages = $query->paginate($perPage);

        // Mark messages as read for the user
        if ($user && $conversation->user_id === $user->id) {
            $conversation->markAsRead();
        }

        return response()->json([
            'success' => true,
            'data' => $messages->items(),
            'pagination' => [
                'current_page' => $messages->currentPage(),
                'total_pages' => $messages->lastPage(),
                'total_count' => $messages->total(),
                'per_page' => $messages->perPage()
            ]
        ]);
    }

    /**
     * Send a message.
     *
     * @param int $conversationId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage($conversationId, Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|max:2000',
            'type' => 'sometimes|in:text,image,file',
            'attachments' => 'sometimes|array',
            'attachments.*.id' => 'required_with:attachments|string',
            'attachments.*.name' => 'required_with:attachments|string',
            'attachments.*.url' => 'required_with:attachments|url',
            'attachments.*.type' => 'required_with:attachments|string',
            'attachments.*.size' => 'required_with:attachments|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $conversation = ChatConversation::find($conversationId);
        
        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        // Check if user owns the conversation or is an agent
        if ($user && $conversation->user_id !== $user->id && !$user->isAgent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if conversation can receive messages
        if (!$conversation->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Conversation is not active'
            ], 400);
        }

        try {
            // Determine sender type
            $senderType = ChatMessage::SENDER_TYPE_USER;
            if ($user && $user->isAgent()) {
                $senderType = ChatMessage::SENDER_TYPE_AGENT;
            }

            // Create message
            $message = $conversation->messages()->create([
                'sender_id' => $user ? $user->id : null,
                'sender_type' => $senderType,
                'sender_name' => $user ? $user->name : 'Guest',
                'content' => $request->content,
                'type' => $request->input('type', ChatMessage::TYPE_TEXT),
                'attachments' => $request->input('attachments', []),
                'delivered_at' => now()
            ]);

            // Update conversation timestamp
            $conversation->touch();

            // If this is the first agent message, mark conversation as active
            if ($senderType === ChatMessage::SENDER_TYPE_AGENT && $conversation->status === ChatConversation::STATUS_WAITING) {
                $conversation->start($user->id);
            }

            // Broadcast message
            broadcast(new ChatMessageSent($message))->toOthers();

            // Load sender relationship
            $message->load(['sender:id,name,email']);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending message: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send message'
            ], 500);
        }
    }

    /**
     * Upload file for chat.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // 10MB max
            'conversation_id' => 'required|exists:chat_conversations,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $conversation = ChatConversation::find($request->conversation_id);

        // Check permissions
        if ($user && $conversation->user_id !== $user->id && !$user->isAgent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('chat-uploads', $filename, 'public');

            $fileData = [
                'id' => \Str::uuid(),
                'name' => $file->getClientOriginalName(),
                'url' => Storage::url($path),
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'path' => $path
            ];

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $fileData
            ]);

        } catch (\Exception $e) {
            Log::error('Error uploading file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file'
            ], 500);
        }
    }

    /**
     * Mark conversation messages as read.
     *
     * @param int $conversationId
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead($conversationId)
    {
        $user = Auth::user();
        
        $conversation = ChatConversation::find($conversationId);
        
        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        // Check if user owns the conversation
        if ($user && $conversation->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $conversation->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);
    }

    /**
     * End conversation.
     *
     * @param int $conversationId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function endConversation($conversationId, Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'rating' => 'sometimes|integer|min:1|max:5',
            'feedback' => 'sometimes|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $conversation = ChatConversation::find($conversationId);
        
        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        // Check permissions
        if ($user && $conversation->user_id !== $user->id && !$user->isAgent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $conversation->end(
                $request->input('rating'),
                $request->input('feedback')
            );

            return response()->json([
                'success' => true,
                'message' => 'Conversation ended successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error ending conversation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to end conversation'
            ], 500);
        }
    }

    /**
     * Send typing indicator.
     *
     * @param int $conversationId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendTypingIndicator($conversationId, Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'is_typing' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $conversation = ChatConversation::find($conversationId);
        
        if (!$conversation) {
            return response()->json(['success' => false, 'message' => 'Conversation not found'], 404);
        }

        // Check permissions
        if ($user && $conversation->user_id !== $user->id && !$user->isAgent()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Determine sender type
        $senderType = ChatMessage::SENDER_TYPE_USER;
        if ($user && $user->isAgent()) {
            $senderType = ChatMessage::SENDER_TYPE_AGENT;
        }

        // Broadcast typing indicator
        broadcast(new ChatTypingIndicator(
            $conversation,
            $user,
            $senderType,
            $request->is_typing
        ))->toOthers();

        return response()->json([
            'success' => true,
            'message' => 'Typing indicator sent'
        ]);
    }

    /**
     * Get chat statistics.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_conversations' => ChatConversation::where('user_id', $user->id)->count(),
            'active_conversations' => ChatConversation::where('user_id', $user->id)->active()->count(),
            'total_messages' => ChatMessage::whereHas('conversation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count(),
            'unread_messages' => ChatMessage::whereHas('conversation', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('sender_type', ChatMessage::SENDER_TYPE_AGENT)->unread()->count(),
            'average_rating' => ChatConversation::where('user_id', $user->id)
                ->whereNotNull('rating')
                ->avg('rating')
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Notify available agents about new conversation.
     *
     * @param ChatConversation $conversation
     */
    private function notifyAvailableAgents(ChatConversation $conversation)
    {
        // This would typically integrate with your agent management system
        // For now, we'll just log it
        Log::info('New conversation waiting for agent', [
            'conversation_id' => $conversation->id,
            'user_id' => $conversation->user_id,
            'priority' => $conversation->priority
        ]);
    }
}

