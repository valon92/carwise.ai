import { ref, reactive, computed, onMounted, onUnmounted, watch } from 'vue'
import { useNotifications } from './useNotifications'

export function useLiveChat() {
  // Get notifications system
  const { showInfo, showSuccess, showError } = useNotifications()

  // State
  const isConnected = ref(false)
  const isChatOpen = ref(false)
  const isMinimized = ref(false)
  const isTyping = ref(false)
  const agentTyping = ref(false)
  const connectionStatus = ref('disconnected')
  const currentUser = ref(null)
  const activeConversation = ref(null)
  const conversations = ref([])
  const messages = ref([])
  const unreadCount = ref(0)
  const onlineAgents = ref([])
  const chatSettings = ref({
    enableSound: true,
    enableNotifications: true,
    autoOpenOnMessage: false,
    showTypingIndicator: true,
    maxFileSize: 10 * 1024 * 1024, // 10MB
    allowedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain']
  })

  // Chat status
  const CHAT_STATUS = {
    WAITING: 'waiting',
    CONNECTED: 'connected',
    ENDED: 'ended',
    TRANSFERRED: 'transferred'
  }

  // Message types
  const MESSAGE_TYPES = {
    TEXT: 'text',
    IMAGE: 'image',
    FILE: 'file',
    SYSTEM: 'system',
    TYPING: 'typing',
    AGENT_JOINED: 'agent_joined',
    AGENT_LEFT: 'agent_left',
    CONVERSATION_ENDED: 'conversation_ended'
  }

  // WebSocket connection
  let ws = null
  let reconnectAttempts = 0
  const maxReconnectAttempts = 5
  let typingTimeout = null

  // Connect to chat WebSocket
  const connectChat = () => {
    if (ws && ws.readyState === WebSocket.OPEN) {
      console.log('Chat WebSocket already connected')
      return
    }

    const wsUrl = import.meta.env.VITE_CHAT_WEBSOCKET_URL || 'ws://localhost:6001/chat'
    ws = new WebSocket(wsUrl)

    ws.onopen = () => {
      isConnected.value = true
      connectionStatus.value = 'connected'
      reconnectAttempts = 0
      console.log('Chat WebSocket connected')
      
      // Send authentication if user is logged in
      if (currentUser.value) {
        sendMessage({
          type: 'auth',
          user_id: currentUser.value.id,
          user_name: currentUser.value.name,
          user_email: currentUser.value.email
        })
      }
    }

    ws.onmessage = (event) => {
      const data = JSON.parse(event.data)
      handleIncomingMessage(data)
    }

    ws.onclose = () => {
      isConnected.value = false
      connectionStatus.value = 'disconnected'
      console.log('Chat WebSocket disconnected')
      
      // Attempt to reconnect
      if (reconnectAttempts < maxReconnectAttempts) {
        reconnectAttempts++
        setTimeout(() => {
          console.log(`Attempting to reconnect chat (${reconnectAttempts}/${maxReconnectAttempts})`)
          connectChat()
        }, 2000 * reconnectAttempts)
      }
    }

    ws.onerror = (error) => {
      console.error('Chat WebSocket error:', error)
      connectionStatus.value = 'error'
    }
  }

  // Disconnect chat
  const disconnectChat = () => {
    if (ws) {
      ws.close()
      ws = null
    }
  }

  // Handle incoming messages
  const handleIncomingMessage = (data) => {
    switch (data.type) {
      case 'message':
        addMessage(data.payload)
        break
      case 'typing':
        handleTypingIndicator(data.payload)
        break
      case 'agent_status':
        updateAgentStatus(data.payload)
        break
      case 'conversation_update':
        updateConversation(data.payload)
        break
      case 'system_notification':
        handleSystemNotification(data.payload)
        break
    }
  }

  // Send message via WebSocket
  const sendMessage = (message) => {
    if (ws && ws.readyState === WebSocket.OPEN) {
      ws.send(JSON.stringify(message))
    } else {
      console.error('Chat WebSocket not connected')
      showError('Chat Error', 'Connection lost. Please try again.')
    }
  }

  // Start new conversation
  const startConversation = async (initialMessage = null) => {
    try {
      const response = await fetch('/api/chat/conversations', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify({
          initial_message: initialMessage,
          user_agent: navigator.userAgent,
          page_url: window.location.href
        })
      })

      const data = await response.json()
      
      if (data.success) {
        activeConversation.value = data.data
        messages.value = data.data.messages || []
        isChatOpen.value = true
        
        // Send initial message if provided
        if (initialMessage) {
          await sendChatMessage(initialMessage)
        }
        
        showSuccess('Chat Started', 'Connected to support. An agent will be with you shortly.')
      } else {
        throw new Error(data.message || 'Failed to start conversation')
      }
    } catch (error) {
      console.error('Error starting conversation:', error)
      showError('Chat Error', 'Failed to start chat. Please try again.')
    }
  }

  // Send chat message
  const sendChatMessage = async (content, type = MESSAGE_TYPES.TEXT, attachments = []) => {
    if (!activeConversation.value) {
      await startConversation(content)
      return
    }

    const message = {
      id: `temp_${Date.now()}`,
      conversation_id: activeConversation.value.id,
      content,
      type,
      attachments,
      sender_type: 'user',
      sender_id: currentUser.value?.id,
      sender_name: currentUser.value?.name || 'Guest',
      timestamp: new Date(),
      is_read: false,
      temp: true
    }

    // Add message to local state immediately
    addMessage(message)

    try {
      // Send via WebSocket for real-time delivery
      sendMessage({
        type: 'chat_message',
        payload: message
      })

      // Also send via API for persistence
      const response = await fetch(`/api/chat/conversations/${activeConversation.value.id}/messages`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: JSON.stringify({
          content,
          type,
          attachments
        })
      })

      const data = await response.json()
      
      if (data.success) {
        // Update the temporary message with the real one
        const index = messages.value.findIndex(m => m.id === message.id)
        if (index !== -1) {
          messages.value[index] = { ...data.data, temp: false }
        }
      }
    } catch (error) {
      console.error('Error sending message:', error)
      // Mark message as failed
      const index = messages.value.findIndex(m => m.id === message.id)
      if (index !== -1) {
        messages.value[index].failed = true
      }
    }
  }

  // Add message to conversation
  const addMessage = (message) => {
    // Avoid duplicates
    const exists = messages.value.find(m => m.id === message.id)
    if (exists) return

    messages.value.push({
      ...message,
      timestamp: new Date(message.timestamp)
    })

    // Update unread count if message is from agent and chat is closed
    if (message.sender_type === 'agent' && (!isChatOpen.value || isMinimized.value)) {
      unreadCount.value++
      
      // Show notification
      if (chatSettings.value.enableNotifications) {
        showInfo('New Message', message.content.substring(0, 50) + '...')
      }
      
      // Auto-open chat if enabled
      if (chatSettings.value.autoOpenOnMessage) {
        openChat()
      }
    }

    // Play sound notification
    if (message.sender_type === 'agent' && chatSettings.value.enableSound) {
      playNotificationSound()
    }

    // Scroll to bottom
    setTimeout(scrollToBottom, 100)
  }

  // Handle typing indicator
  const handleTypingIndicator = (data) => {
    if (data.sender_type === 'agent') {
      agentTyping.value = data.is_typing
      
      if (data.is_typing) {
        // Clear typing indicator after 3 seconds
        setTimeout(() => {
          agentTyping.value = false
        }, 3000)
      }
    }
  }

  // Send typing indicator
  const sendTypingIndicator = (isTypingNow) => {
    if (!activeConversation.value) return

    clearTimeout(typingTimeout)
    
    sendMessage({
      type: 'typing',
      payload: {
        conversation_id: activeConversation.value.id,
        sender_type: 'user',
        is_typing: isTypingNow
      }
    })

    if (isTypingNow) {
      isTyping.value = true
      // Stop typing indicator after 3 seconds
      typingTimeout = setTimeout(() => {
        isTyping.value = false
        sendMessage({
          type: 'typing',
          payload: {
            conversation_id: activeConversation.value.id,
            sender_type: 'user',
            is_typing: false
          }
        })
      }, 3000)
    } else {
      isTyping.value = false
    }
  }

  // Update agent status
  const updateAgentStatus = (data) => {
    const agentIndex = onlineAgents.value.findIndex(a => a.id === data.agent_id)
    
    if (data.status === 'online') {
      if (agentIndex === -1) {
        onlineAgents.value.push(data.agent)
      } else {
        onlineAgents.value[agentIndex] = data.agent
      }
    } else if (data.status === 'offline' && agentIndex !== -1) {
      onlineAgents.value.splice(agentIndex, 1)
    }
  }

  // Update conversation
  const updateConversation = (conversationData) => {
    if (activeConversation.value && activeConversation.value.id === conversationData.id) {
      activeConversation.value = { ...activeConversation.value, ...conversationData }
    }
  }

  // Handle system notifications
  const handleSystemNotification = (data) => {
    addMessage({
      id: `system_${Date.now()}`,
      content: data.message,
      type: MESSAGE_TYPES.SYSTEM,
      sender_type: 'system',
      timestamp: new Date()
    })
  }

  // Open chat
  const openChat = () => {
    isChatOpen.value = true
    isMinimized.value = false
    unreadCount.value = 0
    
    // Mark messages as read
    markMessagesAsRead()
    
    setTimeout(scrollToBottom, 100)
  }

  // Close chat
  const closeChat = () => {
    isChatOpen.value = false
  }

  // Minimize chat
  const minimizeChat = () => {
    isMinimized.value = true
  }

  // Maximize chat
  const maximizeChat = () => {
    isMinimized.value = false
    unreadCount.value = 0
    markMessagesAsRead()
    setTimeout(scrollToBottom, 100)
  }

  // Toggle chat
  const toggleChat = () => {
    if (isChatOpen.value) {
      if (isMinimized.value) {
        maximizeChat()
      } else {
        minimizeChat()
      }
    } else {
      openChat()
    }
  }

  // End conversation
  const endConversation = async () => {
    if (!activeConversation.value) return

    try {
      const response = await fetch(`/api/chat/conversations/${activeConversation.value.id}/end`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      })

      const data = await response.json()
      
      if (data.success) {
        activeConversation.value.status = CHAT_STATUS.ENDED
        addMessage({
          id: `system_end_${Date.now()}`,
          content: 'Conversation ended. Thank you for contacting support!',
          type: MESSAGE_TYPES.SYSTEM,
          sender_type: 'system',
          timestamp: new Date()
        })
        
        showInfo('Chat Ended', 'Conversation has been ended. You can start a new one anytime.')
      }
    } catch (error) {
      console.error('Error ending conversation:', error)
      showError('Chat Error', 'Failed to end conversation.')
    }
  }

  // Upload file
  const uploadFile = async (file) => {
    if (!activeConversation.value) {
      showError('Upload Error', 'Please start a conversation first.')
      return
    }

    // Validate file
    if (file.size > chatSettings.value.maxFileSize) {
      showError('Upload Error', 'File size too large. Maximum 10MB allowed.')
      return
    }

    if (!chatSettings.value.allowedFileTypes.includes(file.type)) {
      showError('Upload Error', 'File type not allowed.')
      return
    }

    const formData = new FormData()
    formData.append('file', file)
    formData.append('conversation_id', activeConversation.value.id)

    try {
      const response = await fetch('/api/chat/upload', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        },
        body: formData
      })

      const data = await response.json()
      
      if (data.success) {
        // Send file message
        await sendChatMessage(
          `Shared a file: ${file.name}`,
          file.type.startsWith('image/') ? MESSAGE_TYPES.IMAGE : MESSAGE_TYPES.FILE,
          [data.data]
        )
      } else {
        throw new Error(data.message || 'Upload failed')
      }
    } catch (error) {
      console.error('Error uploading file:', error)
      showError('Upload Error', 'Failed to upload file.')
    }
  }

  // Mark messages as read
  const markMessagesAsRead = async () => {
    if (!activeConversation.value) return

    const unreadMessages = messages.value.filter(m => !m.is_read && m.sender_type === 'agent')
    
    if (unreadMessages.length === 0) return

    try {
      await fetch(`/api/chat/conversations/${activeConversation.value.id}/mark-read`, {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      })

      // Update local state
      unreadMessages.forEach(message => {
        message.is_read = true
      })
    } catch (error) {
      console.error('Error marking messages as read:', error)
    }
  }

  // Scroll to bottom of chat
  const scrollToBottom = () => {
    const chatContainer = document.getElementById('chat-messages-container')
    if (chatContainer) {
      chatContainer.scrollTop = chatContainer.scrollHeight
    }
  }

  // Play notification sound
  const playNotificationSound = () => {
    try {
      const audio = new Audio('/sounds/chat-notification.mp3')
      audio.volume = 0.5
      audio.play().catch(() => {
        // Ignore audio play errors
      })
    } catch (error) {
      // Ignore audio errors
    }
  }

  // Get conversation history
  const loadConversationHistory = async () => {
    try {
      const response = await fetch('/api/chat/conversations', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('auth_token')}`
        }
      })

      const data = await response.json()
      
      if (data.success) {
        conversations.value = data.data
      }
    } catch (error) {
      console.error('Error loading conversation history:', error)
    }
  }

  // Set current user
  const setCurrentUser = (user) => {
    currentUser.value = user
  }

  // Computed properties
  const hasActiveConversation = computed(() => {
    return activeConversation.value && activeConversation.value.status !== CHAT_STATUS.ENDED
  })

  const canSendMessages = computed(() => {
    return hasActiveConversation.value && isConnected.value
  })

  const sortedMessages = computed(() => {
    return messages.value.sort((a, b) => new Date(a.timestamp) - new Date(b.timestamp))
  })

  const lastMessage = computed(() => {
    return sortedMessages.value[sortedMessages.value.length - 1]
  })

  const agentInfo = computed(() => {
    return activeConversation.value?.agent || null
  })

  // Lifecycle
  onMounted(() => {
    connectChat()
    loadConversationHistory()
  })

  onUnmounted(() => {
    disconnectChat()
  })

  return {
    // State
    isConnected,
    isChatOpen,
    isMinimized,
    isTyping,
    agentTyping,
    connectionStatus,
    currentUser,
    activeConversation,
    conversations,
    messages: sortedMessages,
    unreadCount,
    onlineAgents,
    chatSettings,

    // Methods
    connectChat,
    disconnectChat,
    startConversation,
    sendChatMessage,
    sendTypingIndicator,
    openChat,
    closeChat,
    minimizeChat,
    maximizeChat,
    toggleChat,
    endConversation,
    uploadFile,
    setCurrentUser,
    loadConversationHistory,

    // Computed
    hasActiveConversation,
    canSendMessages,
    lastMessage,
    agentInfo,

    // Constants
    CHAT_STATUS,
    MESSAGE_TYPES
  }
}

