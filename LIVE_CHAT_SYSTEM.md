# Live Chat Support System Documentation

## Overview

The CarWise.ai Live Chat Support System is a comprehensive, real-time customer support solution that enables instant communication between users and support agents. The system features modern UI/UX, real-time messaging, file sharing, typing indicators, and comprehensive backend management.

## 🎯 Key Features

### **Frontend Features**
- ✅ **Real-time messaging** via WebSocket connections
- ✅ **Modern chat widget** with smooth animations
- ✅ **File upload support** (images, documents, up to 10MB)
- ✅ **Typing indicators** for both users and agents
- ✅ **Message status tracking** (sent, delivered, read)
- ✅ **Quick action buttons** for common inquiries
- ✅ **Responsive design** for all screen sizes
- ✅ **Dark mode support**
- ✅ **Notification integration** with the existing system
- ✅ **Conversation history** and persistence

### **Backend Features**
- ✅ **Laravel-based API** with comprehensive endpoints
- ✅ **Database models** for conversations and messages
- ✅ **WebSocket broadcasting** for real-time updates
- ✅ **File upload handling** with security validation
- ✅ **Agent management** and assignment system
- ✅ **Conversation status tracking** (waiting, active, ended)
- ✅ **Rating and feedback** collection
- ✅ **Statistics and analytics** tracking
- ✅ **Multi-channel support** (website, mobile, email)

## 🚀 Architecture

### **Frontend Stack**
- **Vue.js 3** with Composition API
- **Composable Pattern** (`useLiveChat.js`)
- **Component-based UI** (`LiveChatWidget.vue`)
- **WebSocket** for real-time communication
- **LocalStorage** for persistence
- **Tailwind CSS** for styling

### **Backend Stack**
- **Laravel 10** with Eloquent ORM
- **WebSocket Broadcasting** via Laravel Echo
- **Database** with optimized schema
- **Event-driven** architecture
- **File Storage** with security validation
- **API Authentication** with Sanctum

## 📱 Frontend Implementation

### **1. Live Chat Composable**

```javascript
// Import the composable
import { useLiveChat } from '../composables/useLiveChat'

const {
  // State
  isConnected,              // WebSocket connection status
  isChatOpen,              // Chat widget visibility
  isMinimized,             // Chat minimization state
  messages,                // Array of chat messages
  unreadCount,             // Unread messages count
  activeConversation,      // Current conversation object
  
  // Methods
  openChat,                // Open chat widget
  closeChat,               // Close chat widget
  sendChatMessage,         // Send a message
  uploadFile,              // Upload file attachment
  startConversation,       // Start new conversation
  endConversation,         // End current conversation
  
  // Computed
  canSendMessages,         // Whether messages can be sent
  hasActiveConversation,   // Whether there's an active conversation
  agentInfo,              // Current agent information
  
  // Constants
  MESSAGE_TYPES,          // Message type constants
  CHAT_STATUS            // Chat status constants
} = useLiveChat()
```

### **2. Chat Widget Component**

```vue
<template>
  <div class="your-layout">
    <!-- Live Chat Widget -->
    <LiveChatWidget />
  </div>
</template>

<script setup>
import LiveChatWidget from '../components/LiveChatWidget.vue'
</script>
```

### **3. Sending Messages**

```javascript
// Send text message
await sendChatMessage('Hello, I need help with car parts')

// Send message with file
const file = event.target.files[0]
await uploadFile(file)
await sendChatMessage('I uploaded a photo of the issue', MESSAGE_TYPES.IMAGE)

// Quick action messages
await sendChatMessage('I need help finding car parts')
await sendChatMessage('I have questions about car diagnosis')
await sendChatMessage('I need technical support')
```

### **4. Chat Events**

```javascript
// Listen for new messages
window.addEventListener('chat-message', (event) => {
  const message = event.detail
  console.log('New message received:', message)
})

// Listen for typing indicators
window.addEventListener('chat-typing', (event) => {
  const { isTyping, senderType } = event.detail
  console.log(`${senderType} is ${isTyping ? 'typing' : 'stopped typing'}`)
})

// Listen for connection status changes
watch(isConnected, (connected) => {
  console.log('Chat connection:', connected ? 'Connected' : 'Disconnected')
})
```

## 🔧 Backend Implementation

### **1. Database Models**

#### **ChatConversation Model**
```php
// Create conversation
$conversation = ChatConversation::create([
    'user_id' => $user->id,
    'status' => ChatConversation::STATUS_WAITING,
    'subject' => 'General Support',
    'priority' => ChatConversation::PRIORITY_MEDIUM,
    'channel' => ChatConversation::CHANNEL_WEBSITE
]);

// Start conversation with agent
$conversation->start($agentId);

// End conversation
$conversation->end($rating, $feedback);
```

#### **ChatMessage Model**
```php
// Create message
$message = ChatMessage::create([
    'conversation_id' => $conversation->id,
    'sender_id' => $user->id,
    'sender_type' => ChatMessage::SENDER_TYPE_USER,
    'content' => 'Hello, I need help',
    'type' => ChatMessage::TYPE_TEXT
]);

// Mark as read
$message->markAsRead();
```

### **2. API Endpoints**

#### **Public Endpoints**
```
POST   /api/chat/conversations          - Start new conversation
POST   /api/chat/upload                 - Upload file
```

#### **Protected Endpoints**
```
GET    /api/chat/conversations          - Get user conversations
GET    /api/chat/conversations/{id}/messages - Get conversation messages
POST   /api/chat/conversations/{id}/messages - Send message
POST   /api/chat/conversations/{id}/mark-read - Mark messages as read
POST   /api/chat/conversations/{id}/end - End conversation
POST   /api/chat/conversations/{id}/typing - Send typing indicator
GET    /api/chat/statistics             - Get chat statistics
```

### **3. WebSocket Events**

#### **ChatMessageSent Event**
```php
// Broadcast new message
broadcast(new ChatMessageSent($message))->toOthers();

// Event payload
{
  "type": "message",
  "payload": {
    "id": 123,
    "conversation_id": 456,
    "sender_type": "user",
    "content": "Hello",
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

#### **ChatTypingIndicator Event**
```php
// Broadcast typing indicator
broadcast(new ChatTypingIndicator($conversation, $user, $senderType, $isTyping))->toOthers();

// Event payload
{
  "type": "typing",
  "payload": {
    "conversation_id": 456,
    "sender_type": "user",
    "is_typing": true,
    "timestamp": "2024-01-15T10:30:00Z"
  }
}
```

## 🎨 UI Components

### **1. Chat Button (Closed State)**
- 🔵 **Primary color** circular button
- 💬 **Chat icon** with smooth animations
- 🔴 **Unread badge** with count
- 🟢 **Online indicator** when agents available
- ⚡ **Pulse animation** to attract attention

### **2. Chat Window (Open State)**
- 📱 **Responsive design** (396px width, 384px height)
- 🎨 **Modern UI** with rounded corners and shadows
- 🌙 **Dark mode support**
- 📊 **Header** with agent info and controls
- 💬 **Messages area** with auto-scroll
- ⌨️ **Input area** with file upload and send button

### **3. Message Types**
- 📝 **Text messages** with word wrapping
- 🖼️ **Image messages** with thumbnails
- 📎 **File messages** with download links
- 🤖 **System messages** with special styling
- ⏳ **Typing indicators** with animated dots

### **4. Quick Actions**
- 🔧 **Find Car Parts** - Help finding parts
- 🔍 **Car Diagnosis** - Questions about AI diagnosis
- 💻 **Technical Support** - Platform usage help

## 🔊 Real-Time Features

### **WebSocket Integration**
```javascript
// Connection management
const connectChat = () => {
  const wsUrl = import.meta.env.VITE_CHAT_WEBSOCKET_URL || 'ws://localhost:6001/chat'
  ws = new WebSocket(wsUrl)
  
  ws.onopen = () => {
    isConnected.value = true
    // Send authentication
  }
  
  ws.onmessage = (event) => {
    const data = JSON.parse(event.data)
    handleIncomingMessage(data)
  }
}
```

### **Broadcasting Channels**
```php
// Private conversation channel
'chat.conversation.' . $conversationId

// Global chat channel
'chat.global'
```

### **Message Delivery**
1. **User types message** → Frontend validation
2. **Send via WebSocket** → Real-time delivery
3. **Send via API** → Database persistence
4. **Broadcast to agents** → Real-time notification
5. **Update UI** → Immediate feedback

## 📊 Features Breakdown

### **Message Management**
- ✅ **Real-time delivery** via WebSocket
- ✅ **Persistence** in database
- ✅ **Message status** tracking (sent, delivered, read)
- ✅ **Message types** (text, image, file, system)
- ✅ **Attachments** with security validation
- ✅ **Message history** with pagination

### **Conversation Management**
- ✅ **Status tracking** (waiting, active, ended, transferred)
- ✅ **Agent assignment** and management
- ✅ **Priority levels** (low, medium, high, urgent)
- ✅ **Multi-channel** support (website, mobile, email)
- ✅ **Rating and feedback** collection
- ✅ **Conversation transfer** between agents

### **User Experience**
- ✅ **Typing indicators** for real-time feedback
- ✅ **File upload** with drag & drop support
- ✅ **Quick actions** for common inquiries
- ✅ **Responsive design** for all devices
- ✅ **Keyboard shortcuts** (Enter to send)
- ✅ **Auto-scroll** to latest messages

### **Agent Features**
- ✅ **Agent status** management (online/offline)
- ✅ **Multiple conversations** handling
- ✅ **Conversation transfer** capabilities
- ✅ **Canned responses** support
- ✅ **File sharing** with customers
- ✅ **Customer information** access

## 🔒 Security Features

### **Input Validation**
- ✅ **Message content** length limits (2000 chars)
- ✅ **File upload** size limits (10MB max)
- ✅ **File type** validation (images, PDFs, text)
- ✅ **XSS protection** with content sanitization
- ✅ **Rate limiting** on API endpoints

### **Authentication**
- ✅ **User authentication** via Laravel Sanctum
- ✅ **Guest support** for unauthenticated users
- ✅ **Agent verification** and role-based access
- ✅ **Conversation ownership** validation
- ✅ **Secure file storage** with access controls

### **Privacy**
- ✅ **Data encryption** in transit and at rest
- ✅ **Conversation isolation** between users
- ✅ **Soft deletes** for data retention
- ✅ **GDPR compliance** features
- ✅ **Audit logging** for security monitoring

## 📈 Analytics & Monitoring

### **Chat Statistics**
```javascript
// Get user statistics
const stats = await getChatStatistics()
// Returns: total conversations, active chats, messages, ratings

// Conversation metrics
{
  total_conversations: 25,
  active_conversations: 2,
  total_messages: 150,
  unread_messages: 3,
  average_rating: 4.2
}
```

### **Performance Metrics**
- 📊 **Response times** tracking
- 📈 **Message delivery** success rates
- 🎯 **Customer satisfaction** ratings
- ⏱️ **Average resolution** time
- 👥 **Agent utilization** metrics

## 🚀 Deployment

### **Environment Variables**
```bash
# WebSocket Configuration
VITE_CHAT_WEBSOCKET_URL=ws://localhost:6001/chat

# Broadcasting
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret

# File Storage
FILESYSTEM_DISK=public
```

### **Database Migrations**
```bash
# Run chat system migrations
php artisan migrate

# Seed sample data (optional)
php artisan db:seed --class=ChatSeeder
```

### **Queue Workers**
```bash
# Start queue worker for chat events
php artisan queue:work --queue=chat

# WebSocket server (if using Laravel Echo Server)
laravel-echo-server start
```

## 🧪 Testing

### **Frontend Testing**
```javascript
// Test chat connection
const { isConnected, connectChat } = useLiveChat()
await connectChat()
expect(isConnected.value).toBe(true)

// Test message sending
await sendChatMessage('Test message')
expect(messages.value).toContain('Test message')

// Test file upload
const file = new File(['test'], 'test.txt', { type: 'text/plain' })
await uploadFile(file)
expect(lastMessage.value.type).toBe(MESSAGE_TYPES.FILE)
```

### **Backend Testing**
```bash
# Test API endpoints
curl -X POST /api/chat/conversations \
  -H "Authorization: Bearer {token}" \
  -d '{"initial_message": "Hello"}'

# Test WebSocket connection
wscat -c ws://localhost:6001/chat

# Run automated tests
php artisan test --filter=ChatTest
```

## 🔮 Future Enhancements

### **Planned Features**
- 📱 **Mobile app integration**
- 🤖 **AI chatbot** for initial responses
- 🎥 **Video chat** capabilities
- 🔄 **Screen sharing** for technical support
- 📞 **Voice messages** support
- 🌐 **Multi-language** chat interface
- 📋 **Canned responses** library
- 📊 **Advanced analytics** dashboard

### **Advanced Features**
- **Smart routing** based on inquiry type
- **Sentiment analysis** for conversation monitoring
- **Auto-translation** for international support
- **Integration** with CRM systems
- **Chatbot handoff** to human agents
- **Conversation templates** for common issues

## 💡 Best Practices

### **User Experience**
1. **Quick response** - Acknowledge messages within 30 seconds
2. **Clear communication** - Use simple, helpful language
3. **Proactive support** - Offer help based on user behavior
4. **Follow up** - Check if issues are resolved
5. **Feedback collection** - Always ask for ratings

### **Performance**
1. **Message batching** - Group multiple messages for efficiency
2. **Connection management** - Handle reconnections gracefully
3. **File optimization** - Compress images and validate uploads
4. **Caching** - Cache conversation data for quick access
5. **Monitoring** - Track performance metrics continuously

### **Security**
1. **Input validation** - Always validate and sanitize input
2. **Rate limiting** - Prevent spam and abuse
3. **Access control** - Verify user permissions
4. **Audit logging** - Track all chat activities
5. **Data retention** - Follow privacy regulations

## 🎉 Conclusion

The CarWise.ai Live Chat Support System provides a comprehensive, modern, and scalable solution for customer support. With its real-time capabilities, intuitive interface, and robust backend, it enhances customer experience while providing agents with powerful tools to deliver excellent support.

**The system is production-ready and fully integrated!** 🚗💬✨

