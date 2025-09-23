// Performance optimization utilities

/**
 * Lazy load images with intersection observer
 */
export const lazyLoadImages = () => {
  if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target
          if (img.dataset.src) {
            img.src = img.dataset.src
            img.classList.remove('lazy')
            img.classList.add('loaded')
            observer.unobserve(img)
          }
        }
      })
    }, {
      rootMargin: '50px 0px',
      threshold: 0.01
    })

    // Observe all lazy images
    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img)
    })

    return imageObserver
  }
}

/**
 * Debounce function to limit function calls
 */
export const debounce = (func, wait, immediate = false) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      timeout = null
      if (!immediate) func(...args)
    }
    const callNow = immediate && !timeout
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
    if (callNow) func(...args)
  }
}

/**
 * Throttle function to limit function calls
 */
export const throttle = (func, limit) => {
  let inThrottle
  return function(...args) {
    if (!inThrottle) {
      func.apply(this, args)
      inThrottle = true
      setTimeout(() => inThrottle = false, limit)
    }
  }
}

/**
 * Preload critical resources
 */
export const preloadCriticalResources = () => {
  // Preload critical CSS
  const criticalCSS = document.createElement('link')
  criticalCSS.rel = 'preload'
  criticalCSS.as = 'style'
  criticalCSS.href = '/css/critical.css'
  document.head.appendChild(criticalCSS)

  // Preload critical fonts
  const fontPreload = document.createElement('link')
  fontPreload.rel = 'preload'
  fontPreload.as = 'font'
  fontPreload.type = 'font/woff2'
  fontPreload.href = '/fonts/inter.woff2'
  fontPreload.crossOrigin = 'anonymous'
  document.head.appendChild(fontPreload)
}

/**
 * Optimize scroll performance
 */
export const optimizeScroll = () => {
  let ticking = false

  const updateScrollPosition = () => {
    // Add scroll-based optimizations here
    ticking = false
  }

  const requestTick = () => {
    if (!ticking) {
      requestAnimationFrame(updateScrollPosition)
      ticking = true
    }
  }

  window.addEventListener('scroll', requestTick, { passive: true })
}

/**
 * Service Worker registration for caching
 */
export const registerServiceWorker = async () => {
  if ('serviceWorker' in navigator) {
    try {
      const registration = await navigator.serviceWorker.register('/sw.js')
      console.log('Service Worker registered:', registration)
      return registration
    } catch (error) {
      console.log('Service Worker registration failed:', error)
    }
  }
}

/**
 * Performance monitoring
 */
export const initPerformanceMonitoring = () => {
  // Monitor Core Web Vitals
  if ('PerformanceObserver' in window) {
    // Largest Contentful Paint
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        console.log('LCP:', entry.startTime)
      }
    }).observe({ entryTypes: ['largest-contentful-paint'] })

    // First Input Delay
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        console.log('FID:', entry.processingStart - entry.startTime)
      }
    }).observe({ entryTypes: ['first-input'] })

    // Cumulative Layout Shift
    new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (!entry.hadRecentInput) {
          console.log('CLS:', entry.value)
        }
      }
    }).observe({ entryTypes: ['layout-shift'] })
  }
}

/**
 * Memory usage monitoring
 */
export const monitorMemoryUsage = () => {
  if ('memory' in performance) {
    const memoryInfo = performance.memory
    console.log('Memory Usage:', {
      used: Math.round(memoryInfo.usedJSHeapSize / 1048576) + ' MB',
      total: Math.round(memoryInfo.totalJSHeapSize / 1048576) + ' MB',
      limit: Math.round(memoryInfo.jsHeapSizeLimit / 1048576) + ' MB'
    })
  }
}

/**
 * Bundle size optimization
 */
export const optimizeBundleSize = () => {
  // Tree shake unused code
  // Use dynamic imports for non-critical features
  // Implement code splitting at route level
  // Use webpack-bundle-analyzer to identify large dependencies
}

/**
 * Image optimization
 */
export const optimizeImages = () => {
  // Convert images to WebP format
  // Implement responsive images
  // Use appropriate image sizes
  // Implement progressive loading
}

/**
 * Network optimization
 */
export const optimizeNetwork = () => {
  // Implement HTTP/2 server push
  // Use CDN for static assets
  // Enable gzip compression
  // Implement resource hints (preload, prefetch, preconnect)
}

/**
 * Initialize all performance optimizations
 */
export const initPerformanceOptimizations = () => {
  // Initialize performance monitoring
  initPerformanceMonitoring()
  
  // Preload critical resources
  preloadCriticalResources()
  
  // Optimize scroll performance
  optimizeScroll()
  
  // Lazy load images
  lazyLoadImages()
  
  // Register service worker
  registerServiceWorker()
  
  // Monitor memory usage periodically
  setInterval(monitorMemoryUsage, 30000) // Every 30 seconds
  
  console.log('Performance optimizations initialized')
}

export default {
  lazyLoadImages,
  debounce,
  throttle,
  preloadCriticalResources,
  optimizeScroll,
  registerServiceWorker,
  initPerformanceMonitoring,
  monitorMemoryUsage,
  optimizeBundleSize,
  optimizeImages,
  optimizeNetwork,
  initPerformanceOptimizations
}
