import { ref, computed, onMounted, onUnmounted } from 'vue'

export function useMobileLayout() {
  // Screen size tracking
  const screenWidth = ref(window.innerWidth)
  const screenHeight = ref(window.innerHeight)
  const orientation = ref(screen.orientation?.type || 'portrait-primary')
  
  // Device detection
  const userAgent = navigator.userAgent
  const isIOS = /iPad|iPhone|iPod/.test(userAgent)
  const isAndroid = /Android/.test(userAgent)
  const isMobile = /Mobi|Android/i.test(userAgent)
  const isTablet = /(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(userAgent)
  
  // Breakpoints (following Tailwind CSS conventions)
  const breakpoints = {
    xs: 475,
    sm: 640,
    md: 768,
    lg: 1024,
    xl: 1280,
    '2xl': 1536
  }
  
  // Responsive states
  const isXs = computed(() => screenWidth.value < breakpoints.xs)
  const isSm = computed(() => screenWidth.value >= breakpoints.sm && screenWidth.value < breakpoints.md)
  const isMd = computed(() => screenWidth.value >= breakpoints.md && screenWidth.value < breakpoints.lg)
  const isLg = computed(() => screenWidth.value >= breakpoints.lg && screenWidth.value < breakpoints.xl)
  const isXl = computed(() => screenWidth.value >= breakpoints.xl && screenWidth.value < breakpoints['2xl'])
  const is2Xl = computed(() => screenWidth.value >= breakpoints['2xl'])
  
  // Mobile-specific states
  const isMobileDevice = computed(() => isMobile || isTablet)
  const isSmallMobile = computed(() => screenWidth.value < 375)
  const isMediumMobile = computed(() => screenWidth.value >= 375 && screenWidth.value < 414)
  const isLargeMobile = computed(() => screenWidth.value >= 414 && screenWidth.value < breakpoints.sm)
  const isPortrait = computed(() => screenHeight.value > screenWidth.value)
  const isLandscape = computed(() => screenWidth.value > screenHeight.value)
  
  // Safe area detection (for devices with notches)
  const safeAreaTop = ref(0)
  const safeAreaBottom = ref(0)
  const safeAreaLeft = ref(0)
  const safeAreaRight = ref(0)
  
  // Viewport height handling (for mobile browsers)
  const viewportHeight = ref(window.innerHeight)
  const documentHeight = ref(document.documentElement.clientHeight)
  
  // Update screen dimensions
  const updateDimensions = () => {
    screenWidth.value = window.innerWidth
    screenHeight.value = window.innerHeight
    viewportHeight.value = window.innerHeight
    documentHeight.value = document.documentElement.clientHeight
  }
  
  // Update orientation
  const updateOrientation = () => {
    orientation.value = screen.orientation?.type || 
      (window.innerWidth > window.innerHeight ? 'landscape-primary' : 'portrait-primary')
  }
  
  // Detect safe areas
  const detectSafeAreas = () => {
    const style = getComputedStyle(document.documentElement)
    safeAreaTop.value = parseInt(style.getPropertyValue('--sat') || '0')
    safeAreaBottom.value = parseInt(style.getPropertyValue('--sab') || '0')
    safeAreaLeft.value = parseInt(style.getPropertyValue('--sal') || '0')
    safeAreaRight.value = parseInt(style.getPropertyValue('--sar') || '0')
  }
  
  // Get responsive grid columns
  const getGridColumns = (options = {}) => {
    const { xs = 1, sm = 2, md = 3, lg = 4, xl = 5, '2xl': xxl = 6 } = options
    
    if (is2Xl.value) return xxl
    if (isXl.value) return xl
    if (isLg.value) return lg
    if (isMd.value) return md
    if (isSm.value) return sm
    return xs
  }
  
  // Get responsive spacing
  const getSpacing = (options = {}) => {
    const { xs = 2, sm = 3, md = 4, lg = 6, xl = 8 } = options
    
    if (isXl.value || is2Xl.value) return xl
    if (isLg.value) return lg
    if (isMd.value) return md
    if (isSm.value) return sm
    return xs
  }
  
  // Get responsive text size
  const getTextSize = (options = {}) => {
    const { xs = 'text-sm', sm = 'text-base', md = 'text-lg', lg = 'text-xl' } = options
    
    if (isLg.value || isXl.value || is2Xl.value) return lg
    if (isMd.value) return md
    if (isSm.value) return sm
    return xs
  }
  
  // Get container padding for mobile
  const getContainerPadding = () => {
    if (isSmallMobile.value) return 'px-3'
    if (isMediumMobile.value) return 'px-4'
    if (isLargeMobile.value) return 'px-5'
    if (isMd.value) return 'px-6'
    return 'px-8'
  }
  
  // Get button size for mobile
  const getButtonSize = (size = 'md') => {
    const sizes = {
      xs: isMobileDevice.value ? 'px-3 py-2 text-sm min-h-[40px]' : 'px-2 py-1 text-xs min-h-[32px]',
      sm: isMobileDevice.value ? 'px-4 py-2 text-base min-h-[44px]' : 'px-3 py-2 text-sm min-h-[36px]',
      md: isMobileDevice.value ? 'px-6 py-3 text-lg min-h-[48px]' : 'px-4 py-2 text-base min-h-[44px]',
      lg: isMobileDevice.value ? 'px-8 py-4 text-xl min-h-[56px]' : 'px-6 py-3 text-lg min-h-[48px]',
      xl: isMobileDevice.value ? 'px-10 py-5 text-2xl min-h-[64px]' : 'px-8 py-4 text-xl min-h-[56px]'
    }
    return sizes[size] || sizes.md
  }
  
  // Check if device has notch/safe areas
  const hasNotch = computed(() => {
    return safeAreaTop.value > 0 || safeAreaBottom.value > 0 || 
           safeAreaLeft.value > 0 || safeAreaRight.value > 0
  })
  
  // Get device type string
  const deviceType = computed(() => {
    if (isSmallMobile.value) return 'small-mobile'
    if (isMediumMobile.value) return 'medium-mobile'
    if (isLargeMobile.value) return 'large-mobile'
    if (isTablet) return 'tablet'
    if (isMd.value) return 'small-desktop'
    if (isLg.value) return 'desktop'
    if (isXl.value) return 'large-desktop'
    return 'extra-large-desktop'
  })
  
  // Get optimal card width
  const getCardWidth = () => {
    if (isSmallMobile.value) return 'w-full'
    if (isMediumMobile.value) return 'w-full max-w-sm'
    if (isLargeMobile.value) return 'w-full max-w-md'
    if (isMd.value) return 'w-full max-w-lg'
    return 'w-full max-w-xl'
  }
  
  // Get modal positioning
  const getModalPosition = () => {
    if (isMobileDevice.value) {
      return {
        position: 'fixed',
        top: '0',
        left: '0',
        right: '0',
        bottom: '0',
        transform: 'none',
        maxHeight: '100vh',
        borderRadius: '0'
      }
    }
    
    return {
      position: 'fixed',
      top: '50%',
      left: '50%',
      transform: 'translate(-50%, -50%)',
      maxHeight: '90vh',
      borderRadius: '0.5rem'
    }
  }
  
  // Handle viewport height changes (mobile browser address bar)
  const handleViewportChange = () => {
    // Update CSS custom property for mobile viewport height
    document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`)
    
    // Update reactive values
    updateDimensions()
  }
  
  // Lifecycle
  onMounted(() => {
    // Initial setup
    updateDimensions()
    updateOrientation()
    detectSafeAreas()
    handleViewportChange()
    
    // Event listeners
    window.addEventListener('resize', updateDimensions)
    window.addEventListener('orientationchange', updateOrientation)
    window.addEventListener('resize', handleViewportChange)
    
    // Safe area CSS variables
    const style = document.createElement('style')
    style.textContent = `
      :root {
        --sat: env(safe-area-inset-top);
        --sab: env(safe-area-inset-bottom);
        --sal: env(safe-area-inset-left);
        --sar: env(safe-area-inset-right);
      }
    `
    document.head.appendChild(style)
  })
  
  onUnmounted(() => {
    window.removeEventListener('resize', updateDimensions)
    window.removeEventListener('orientationchange', updateOrientation)
    window.removeEventListener('resize', handleViewportChange)
  })
  
  return {
    // Screen dimensions
    screenWidth,
    screenHeight,
    viewportHeight,
    orientation,
    
    // Device detection
    isIOS,
    isAndroid,
    isMobile,
    isTablet,
    isMobileDevice,
    
    // Responsive breakpoints
    isXs,
    isSm,
    isMd,
    isLg,
    isXl,
    is2Xl,
    
    // Mobile-specific
    isSmallMobile,
    isMediumMobile,
    isLargeMobile,
    isPortrait,
    isLandscape,
    
    // Safe areas
    safeAreaTop,
    safeAreaBottom,
    safeAreaLeft,
    safeAreaRight,
    hasNotch,
    
    // Computed properties
    deviceType,
    
    // Helper methods
    getGridColumns,
    getSpacing,
    getTextSize,
    getContainerPadding,
    getButtonSize,
    getCardWidth,
    getModalPosition,
    
    // Breakpoints object
    breakpoints
  }
}

