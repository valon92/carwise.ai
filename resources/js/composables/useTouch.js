import { ref, onMounted, onUnmounted, computed } from 'vue'

export function useTouch() {
  // Touch state
  const isTouch = ref(false)
  const touchStart = ref({ x: 0, y: 0, time: 0 })
  const touchEnd = ref({ x: 0, y: 0, time: 0 })
  const touchDelta = ref({ x: 0, y: 0, time: 0 })
  const isSwiping = ref(false)
  const swipeDirection = ref(null)
  const swipeDistance = ref(0)
  const swipeVelocity = ref(0)
  
  // Pull to refresh state
  const isPulling = ref(false)
  const pullDistance = ref(0)
  const pullThreshold = ref(80)
  const isRefreshing = ref(false)
  
  // Pinch zoom state
  const isPinching = ref(false)
  const pinchScale = ref(1)
  const pinchCenter = ref({ x: 0, y: 0 })
  
  // Long press state
  const isLongPressing = ref(false)
  const longPressTimer = ref(null)
  const longPressDelay = ref(500) // 500ms
  
  // Touch configuration
  const config = ref({
    swipeThreshold: 50, // Minimum distance for swipe
    swipeTimeout: 300, // Maximum time for swipe
    velocityThreshold: 0.3, // Minimum velocity for swipe
    preventScroll: false, // Prevent default scroll behavior
    enablePullRefresh: true,
    enablePinchZoom: false,
    enableLongPress: true
  })

  // Gesture callbacks
  const callbacks = ref({
    onSwipeLeft: null,
    onSwipeRight: null,
    onSwipeUp: null,
    onSwipeDown: null,
    onPullRefresh: null,
    onPinchStart: null,
    onPinchMove: null,
    onPinchEnd: null,
    onLongPress: null,
    onTap: null,
    onDoubleTap: null
  })

  // Double tap detection
  const lastTap = ref(0)
  const doubleTapDelay = ref(300)

  // Detect if device supports touch
  const detectTouch = () => {
    isTouch.value = 'ontouchstart' in window || navigator.maxTouchPoints > 0
  }

  // Calculate distance between two points
  const getDistance = (point1, point2) => {
    return Math.sqrt(
      Math.pow(point2.x - point1.x, 2) + Math.pow(point2.y - point1.y, 2)
    )
  }

  // Calculate angle between two points
  const getAngle = (point1, point2) => {
    return Math.atan2(point2.y - point1.y, point2.x - point1.x) * 180 / Math.PI
  }

  // Get swipe direction based on angle
  const getSwipeDirection = (angle) => {
    if (angle >= -45 && angle <= 45) return 'right'
    if (angle >= 45 && angle <= 135) return 'down'
    if (angle >= -135 && angle <= -45) return 'up'
    return 'left'
  }

  // Handle touch start
  const handleTouchStart = (event) => {
    const touch = event.touches[0]
    const now = Date.now()
    
    touchStart.value = {
      x: touch.clientX,
      y: touch.clientY,
      time: now
    }
    
    isSwiping.value = false
    swipeDirection.value = null
    
    // Handle multiple touches (pinch)
    if (event.touches.length === 2 && config.value.enablePinchZoom) {
      const touch1 = event.touches[0]
      const touch2 = event.touches[1]
      
      isPinching.value = true
      pinchCenter.value = {
        x: (touch1.clientX + touch2.clientX) / 2,
        y: (touch1.clientY + touch2.clientY) / 2
      }
      
      if (callbacks.value.onPinchStart) {
        callbacks.value.onPinchStart({
          center: pinchCenter.value,
          distance: getDistance(touch1, touch2)
        })
      }
    }
    
    // Start long press timer
    if (config.value.enableLongPress) {
      longPressTimer.value = setTimeout(() => {
        isLongPressing.value = true
        if (callbacks.value.onLongPress) {
          callbacks.value.onLongPress({
            x: touch.clientX,
            y: touch.clientY
          })
        }
      }, longPressDelay.value)
    }
    
    // Prevent scroll if configured
    if (config.value.preventScroll) {
      event.preventDefault()
    }
  }

  // Handle touch move
  const handleTouchMove = (event) => {
    if (!touchStart.value.time) return
    
    const touch = event.touches[0]
    const now = Date.now()
    
    touchEnd.value = {
      x: touch.clientX,
      y: touch.clientY,
      time: now
    }
    
    touchDelta.value = {
      x: touchEnd.value.x - touchStart.value.x,
      y: touchEnd.value.y - touchStart.value.y,
      time: touchEnd.value.time - touchStart.value.time
    }
    
    // Clear long press timer on move
    if (longPressTimer.value) {
      clearTimeout(longPressTimer.value)
      longPressTimer.value = null
      isLongPressing.value = false
    }
    
    // Handle pinch zoom
    if (event.touches.length === 2 && isPinching.value && config.value.enablePinchZoom) {
      const touch1 = event.touches[0]
      const touch2 = event.touches[1]
      const currentDistance = getDistance(touch1, touch2)
      
      if (callbacks.value.onPinchMove) {
        callbacks.value.onPinchMove({
          scale: pinchScale.value,
          distance: currentDistance,
          center: pinchCenter.value
        })
      }
      
      event.preventDefault()
      return
    }
    
    // Handle pull to refresh
    if (config.value.enablePullRefresh && touchDelta.value.y > 0 && window.scrollY === 0) {
      isPulling.value = true
      pullDistance.value = Math.min(touchDelta.value.y, pullThreshold.value * 2)
      
      if (pullDistance.value >= pullThreshold.value && !isRefreshing.value) {
        // Visual feedback for pull to refresh
        document.body.style.transform = `translateY(${Math.min(pullDistance.value / 3, 30)}px)`
      }
    }
    
    // Detect swipe
    const distance = Math.abs(touchDelta.value.x) + Math.abs(touchDelta.value.y)
    if (distance > config.value.swipeThreshold) {
      isSwiping.value = true
      swipeDistance.value = distance
      
      const angle = getAngle(touchStart.value, touchEnd.value)
      swipeDirection.value = getSwipeDirection(angle)
    }
    
    // Prevent scroll during swipe if configured
    if (isSwiping.value && config.value.preventScroll) {
      event.preventDefault()
    }
  }

  // Handle touch end
  const handleTouchEnd = (event) => {
    if (!touchStart.value.time) return
    
    const now = Date.now()
    touchEnd.value.time = now
    
    // Clear long press timer
    if (longPressTimer.value) {
      clearTimeout(longPressTimer.value)
      longPressTimer.value = null
    }
    
    // Handle pinch end
    if (isPinching.value) {
      isPinching.value = false
      pinchScale.value = 1
      
      if (callbacks.value.onPinchEnd) {
        callbacks.value.onPinchEnd()
      }
      return
    }
    
    // Handle pull to refresh
    if (isPulling.value) {
      isPulling.value = false
      document.body.style.transform = ''
      
      if (pullDistance.value >= pullThreshold.value && !isRefreshing.value) {
        isRefreshing.value = true
        if (callbacks.value.onPullRefresh) {
          callbacks.value.onPullRefresh().finally(() => {
            isRefreshing.value = false
          })
        }
      }
      
      pullDistance.value = 0
      return
    }
    
    // Calculate swipe velocity
    if (touchDelta.value.time > 0) {
      swipeVelocity.value = swipeDistance.value / touchDelta.value.time
    }
    
    // Handle swipe gestures
    if (isSwiping.value && 
        touchDelta.value.time <= config.value.swipeTimeout &&
        swipeVelocity.value >= config.value.velocityThreshold) {
      
      const callback = callbacks.value[`onSwipe${swipeDirection.value.charAt(0).toUpperCase() + swipeDirection.value.slice(1)}`]
      if (callback) {
        callback({
          direction: swipeDirection.value,
          distance: swipeDistance.value,
          velocity: swipeVelocity.value,
          startPoint: touchStart.value,
          endPoint: touchEnd.value
        })
      }
    } else if (!isSwiping.value && !isLongPressing.value) {
      // Handle tap gestures
      const currentTime = Date.now()
      
      if (currentTime - lastTap.value < doubleTapDelay.value) {
        // Double tap
        if (callbacks.value.onDoubleTap) {
          callbacks.value.onDoubleTap({
            x: touchEnd.value.x,
            y: touchEnd.value.y
          })
        }
        lastTap.value = 0
      } else {
        // Single tap
        lastTap.value = currentTime
        setTimeout(() => {
          if (lastTap.value === currentTime && callbacks.value.onTap) {
            callbacks.value.onTap({
              x: touchEnd.value.x,
              y: touchEnd.value.y
            })
          }
        }, doubleTapDelay.value)
      }
    }
    
    // Reset state
    isLongPressing.value = false
    isSwiping.value = false
    swipeDirection.value = null
    swipeDistance.value = 0
    swipeVelocity.value = 0
    touchStart.value = { x: 0, y: 0, time: 0 }
    touchEnd.value = { x: 0, y: 0, time: 0 }
    touchDelta.value = { x: 0, y: 0, time: 0 }
  }

  // Add touch event listeners to element
  const addTouchListeners = (element) => {
    if (!element) return
    
    element.addEventListener('touchstart', handleTouchStart, { passive: false })
    element.addEventListener('touchmove', handleTouchMove, { passive: false })
    element.addEventListener('touchend', handleTouchEnd, { passive: false })
    element.addEventListener('touchcancel', handleTouchEnd, { passive: false })
  }

  // Remove touch event listeners from element
  const removeTouchListeners = (element) => {
    if (!element) return
    
    element.removeEventListener('touchstart', handleTouchStart)
    element.removeEventListener('touchmove', handleTouchMove)
    element.removeEventListener('touchend', handleTouchEnd)
    element.removeEventListener('touchcancel', handleTouchEnd)
  }

  // Set gesture callback
  const setCallback = (gesture, callback) => {
    callbacks.value[gesture] = callback
  }

  // Configure touch behavior
  const configure = (options) => {
    config.value = { ...config.value, ...options }
  }

  // Enable/disable pull to refresh
  const setPullRefresh = (enabled, threshold = 80) => {
    config.value.enablePullRefresh = enabled
    pullThreshold.value = threshold
  }

  // Enable/disable pinch zoom
  const setPinchZoom = (enabled) => {
    config.value.enablePinchZoom = enabled
  }

  // Enable/disable long press
  const setLongPress = (enabled, delay = 500) => {
    config.value.enableLongPress = enabled
    longPressDelay.value = delay
  }

  // Computed properties
  const isSwipingLeft = computed(() => swipeDirection.value === 'left')
  const isSwipingRight = computed(() => swipeDirection.value === 'right')
  const isSwipingUp = computed(() => swipeDirection.value === 'up')
  const isSwipingDown = computed(() => swipeDirection.value === 'down')
  
  const canPullRefresh = computed(() => 
    config.value.enablePullRefresh && 
    pullDistance.value >= pullThreshold.value && 
    !isRefreshing.value
  )

  // Lifecycle
  onMounted(() => {
    detectTouch()
  })

  onUnmounted(() => {
    if (longPressTimer.value) {
      clearTimeout(longPressTimer.value)
    }
  })

  return {
    // State
    isTouch,
    isSwiping,
    swipeDirection,
    swipeDistance,
    swipeVelocity,
    isPulling,
    pullDistance,
    isRefreshing,
    isPinching,
    pinchScale,
    isLongPressing,
    
    // Methods
    addTouchListeners,
    removeTouchListeners,
    setCallback,
    configure,
    setPullRefresh,
    setPinchZoom,
    setLongPress,
    
    // Computed
    isSwipingLeft,
    isSwipingRight,
    isSwipingUp,
    isSwipingDown,
    canPullRefresh,
    
    // Configuration
    config
  }
}

// Utility function for creating swipe-enabled elements
export function useSwipe(element, callbacks = {}) {
  const { addTouchListeners, removeTouchListeners, setCallback } = useTouch()
  
  onMounted(() => {
    if (element.value) {
      addTouchListeners(element.value)
      
      // Set callbacks
      Object.entries(callbacks).forEach(([gesture, callback]) => {
        setCallback(gesture, callback)
      })
    }
  })
  
  onUnmounted(() => {
    if (element.value) {
      removeTouchListeners(element.value)
    }
  })
}

// Utility function for pull-to-refresh
export function usePullToRefresh(onRefresh, threshold = 80) {
  const { setPullRefresh, setCallback, isRefreshing, canPullRefresh } = useTouch()
  
  onMounted(() => {
    setPullRefresh(true, threshold)
    setCallback('onPullRefresh', onRefresh)
  })
  
  return {
    isRefreshing,
    canPullRefresh
  }
}

