<template>
  <div 
    class="fixed bottom-4 z-50 transition-all duration-300" 
    :class="{ 
      'right-4': !isCartOpen, 
      'right-4 sm:right-80 md:right-96': isCartOpen 
    }"
  >
    <!-- Chat Button (when closed) -->
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <button
        v-if="!isChatOpen"
        @click="openChat"
        class="relative bg-primary-600 hover:bg-primary-700 text-white rounded-full p-4 shadow-lg hover:shadow-xl transition-all duration-200 group"
        title="Start Live Chat"
      >
        <!-- Chat Icon -->
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.874A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
        </svg>
        
        <!-- Unread Badge -->
        <span
          v-if="unreadCount > 0"
          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse"
        >
          {{ unreadCount > 99 ? '99+' : unreadCount }}
        </span>
        
        <!-- Online Indicator -->
        <span
          v-if="onlineAgents.length > 0"
          class="absolute -top-1 -left-1 bg-green-500 rounded-full h-3 w-3 border-2 border-white"
          title="Agents online"
        ></span>
        
        <!-- Pulse Animation -->
        <span class="absolute inset-0 rounded-full bg-primary-600 animate-ping opacity-20"></span>
      </button>
    </Transition>

    <!-- Chat Window -->
    <Transition
      enter-active-class="transition ease-out duration-300"
      enter-from-class="opacity-0 translate-y-4 scale-95"
      enter-to-class="opacity-100 translate-y-0 scale-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0 scale-100"
      leave-to-class="opacity-0 translate-y-4 scale-95"
    >
      <div
        v-if="isChatOpen"
        class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl border border-gray-200 dark:border-gray-700 overflow-hidden"
        :class="isMinimized ? 'w-80 h-16' : 'w-96 h-96'"
      >
        <!-- Chat Header -->
        <div class="bg-primary-600 text-white p-4 flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <!-- Agent Avatar -->
            <div class="relative">
              <div v-if="agentInfo" class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                <img
                  v-if="agentInfo.avatar"
                  :src="agentInfo.avatar"
                  :alt="agentInfo.name"
                  class="w-8 h-8 rounded-full object-cover"
                >
                <span v-else class="text-primary-600 font-semibold text-sm">
                  {{ agentInfo.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div v-else class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <!-- Online Status -->
              <span
                v-if="isConnected"
                class="absolute -bottom-1 -right-1 bg-green-500 rounded-full h-3 w-3 border-2 border-primary-600"
              ></span>
            </div>
            
            <!-- Agent Info -->
            <div v-if="!isMinimized">
              <h3 class="font-semibold text-sm">
                {{ agentInfo ? agentInfo.name : 'CarWise Support' }}
              </h3>
              <p class="text-xs opacity-90">
                {{ connectionStatus === 'connected' ? 'Online' : 'Connecting...' }}
              </p>
            </div>
          </div>
          
          <!-- Header Actions -->
          <div class="flex items-center space-x-2">
            <!-- Minimize/Maximize -->
            <button
              @click="isMinimized ? maximizeChat() : minimizeChat()"
              class="p-1 hover:bg-white hover:bg-opacity-20 rounded transition-colors"
              :title="isMinimized ? 'Maximize' : 'Minimize'"
            >
              <svg v-if="isMinimized" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 17L17 7M17 7H7M17 7v10"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
              </svg>
            </button>
            
            <!-- Close -->
            <button
              @click="closeChat"
              class="p-1 hover:bg-white hover:bg-opacity-20 rounded transition-colors"
              title="Close Chat"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Chat Content (when not minimized) -->
        <div v-if="!isMinimized" class="flex flex-col h-80">
          <!-- Messages Area -->
          <div
            id="chat-messages-container"
            class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900"
          >
            <!-- Welcome Message -->
            <div v-if="messages.length === 0" class="text-center py-8">
              <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.874A8.955 8.955 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path>
                </svg>
              </div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Welcome to CarWise Support!</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                How can we help you today? Our team is here to assist with any questions about car parts, diagnosis, or our services.
              </p>
              
              <!-- Quick Actions -->
              <div class="space-y-2">
                <button
                  @click="sendQuickMessage('I need help finding car parts')"
                  class="block w-full text-left p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  <div class="font-medium text-sm text-gray-900 dark:text-white">🔧 Find Car Parts</div>
                  <div class="text-xs text-gray-600 dark:text-gray-400">Get help finding the right parts for your vehicle</div>
                </button>
                
                <button
                  @click="sendQuickMessage('I have questions about car diagnosis')"
                  class="block w-full text-left p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  <div class="font-medium text-sm text-gray-900 dark:text-white">🔍 Car Diagnosis</div>
                  <div class="text-xs text-gray-600 dark:text-gray-400">Questions about our AI diagnosis service</div>
                </button>
                
                <button
                  @click="sendQuickMessage('I need technical support')"
                  class="block w-full text-left p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                >
                  <div class="font-medium text-sm text-gray-900 dark:text-white">💻 Technical Support</div>
                  <div class="text-xs text-gray-600 dark:text-gray-400">Help with using our platform</div>
                </button>
              </div>
            </div>

            <!-- Messages -->
            <div
              v-for="message in messages"
              :key="message.id"
              :class="[
                'flex',
                message.sender_type === 'user' ? 'justify-end' : 'justify-start'
              ]"
            >
              <div
                :class="[
                  'max-w-xs lg:max-w-md px-4 py-2 rounded-lg',
                  message.sender_type === 'user'
                    ? 'bg-primary-600 text-white'
                    : message.sender_type === 'system'
                    ? 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-center text-sm'
                    : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700',
                  message.temp && 'opacity-70',
                  message.failed && 'bg-red-100 dark:bg-red-900 border-red-300 dark:border-red-700'
                ]"
              >
                <!-- Message Content -->
                <div v-if="message.type === MESSAGE_TYPES.TEXT">
                  {{ message.content }}
                </div>
                
                <!-- Image Message -->
                <div v-else-if="message.type === MESSAGE_TYPES.IMAGE" class="space-y-2">
                  <img
                    v-for="attachment in message.attachments"
                    :key="attachment.id"
                    :src="attachment.url"
                    :alt="attachment.name"
                    class="max-w-full h-auto rounded"
                  >
                  <p v-if="message.content">{{ message.content }}</p>
                </div>
                
                <!-- File Message -->
                <div v-else-if="message.type === MESSAGE_TYPES.FILE" class="space-y-2">
                  <div
                    v-for="attachment in message.attachments"
                    :key="attachment.id"
                    class="flex items-center space-x-2 p-2 bg-gray-100 dark:bg-gray-700 rounded"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <a :href="attachment.url" :download="attachment.name" class="text-sm hover:underline">
                      {{ attachment.name }}
                    </a>
                  </div>
                  <p v-if="message.content">{{ message.content }}</p>
                </div>
                
                <!-- Message Timestamp -->
                <div
                  :class="[
                    'text-xs mt-1 opacity-70',
                    message.sender_type === 'user' ? 'text-right' : 'text-left'
                  ]"
                >
                  {{ formatMessageTime(message.timestamp) }}
                  <span v-if="message.temp" class="ml-1">⏳</span>
                  <span v-if="message.failed" class="ml-1">❌</span>
                </div>
              </div>
            </div>

            <!-- Typing Indicator -->
            <div v-if="agentTyping" class="flex justify-start">
              <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-2">
                <div class="flex space-x-1">
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                  <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Message Input -->
          <div class="border-t border-gray-200 dark:border-gray-700 p-4 bg-white dark:bg-gray-800">
            <div class="flex items-end space-x-2">
              <!-- File Upload -->
              <button
                @click="$refs.fileInput.click()"
                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors"
                title="Attach File"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
              </button>
              
              <!-- Message Input -->
              <div class="flex-1">
                <textarea
                  v-model="messageInput"
                  @keydown="handleKeyDown"
                  @input="handleTyping"
                  placeholder="Type your message..."
                  rows="1"
                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                  :disabled="!canSendMessages"
                ></textarea>
              </div>
              
              <!-- Send Button -->
              <button
                @click="sendMessage"
                :disabled="!canSendMessages || !messageInput.trim()"
                class="p-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                title="Send Message"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </button>
            </div>
            
            <!-- Connection Status -->
            <div v-if="connectionStatus !== 'connected'" class="mt-2 text-xs text-center">
              <span :class="connectionStatus === 'connecting' ? 'text-yellow-600' : 'text-red-600'">
                {{ connectionStatus === 'connecting' ? 'Connecting...' : 'Connection lost. Trying to reconnect...' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Hidden File Input -->
        <input
          ref="fileInput"
          type="file"
          class="hidden"
          @change="handleFileUpload"
          :accept="chatSettings.allowedFileTypes.join(',')"
        >
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick, watch } from 'vue'
import { useLiveChat } from '../composables/useLiveChat'

const props = defineProps({
  isCartOpen: {
    type: Boolean,
    default: false
  }
})

const {
  // State
  isConnected,
  isChatOpen,
  isMinimized,
  agentTyping,
  connectionStatus,
  messages,
  unreadCount,
  onlineAgents,
  chatSettings,
  
  // Methods
  openChat,
  closeChat,
  minimizeChat,
  maximizeChat,
  sendChatMessage,
  sendTypingIndicator,
  uploadFile,
  setCurrentUser,
  
  // Computed
  canSendMessages,
  agentInfo,
  
  // Constants
  MESSAGE_TYPES
} = useLiveChat()

// Local state
const messageInput = ref('')
const fileInput = ref(null)
let typingTimer = null

// Send message
const sendMessage = async () => {
  const content = messageInput.value.trim()
  if (!content || !canSendMessages.value) return
  
  await sendChatMessage(content)
  messageInput.value = ''
  
  // Stop typing indicator
  sendTypingIndicator(false)
}

// Send quick message
const sendQuickMessage = async (message) => {
  await sendChatMessage(message)
}

// Handle keyboard shortcuts
const handleKeyDown = (event) => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault()
    sendMessage()
  }
}

// Handle typing indicator
const handleTyping = () => {
  clearTimeout(typingTimer)
  
  if (messageInput.value.trim()) {
    sendTypingIndicator(true)
    
    // Stop typing after 3 seconds of inactivity
    typingTimer = setTimeout(() => {
      sendTypingIndicator(false)
    }, 3000)
  } else {
    sendTypingIndicator(false)
  }
}

// Handle file upload
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    uploadFile(file)
    // Clear the input
    event.target.value = ''
  }
}

// Format message timestamp
const formatMessageTime = (timestamp) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diffInMinutes = Math.floor((now - date) / (1000 * 60))
  
  if (diffInMinutes < 1) return 'Just now'
  if (diffInMinutes < 60) return `${diffInMinutes}m ago`
  if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`
  
  return date.toLocaleDateString()
}

// Auto-resize textarea
const autoResizeTextarea = () => {
  nextTick(() => {
    const textarea = document.querySelector('textarea')
    if (textarea) {
      textarea.style.height = 'auto'
      textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px'
    }
  })
}

// Watch for message input changes to auto-resize
watch(() => messageInput.value, autoResizeTextarea)

onMounted(() => {
  // Set current user if authenticated
  const user = JSON.parse(localStorage.getItem('user') || 'null')
  if (user) {
    setCurrentUser(user)
  }
})
</script>

<style scoped>
/* Custom scrollbar for messages */
#chat-messages-container::-webkit-scrollbar {
  width: 4px;
}

#chat-messages-container::-webkit-scrollbar-track {
  background: transparent;
}

#chat-messages-container::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.5);
  border-radius: 2px;
}

#chat-messages-container::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.7);
}

/* Smooth animations */
.animate-bounce {
  animation: bounce 1.4s infinite;
}

@keyframes bounce {
  0%, 80%, 100% {
    transform: scale(0);
  }
  40% {
    transform: scale(1);
  }
}
</style>
