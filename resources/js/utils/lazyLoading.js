/**
 * Lazy loading utilities for better performance
 */

import { defineAsyncComponent } from 'vue'

/**
 * Create a lazy-loaded component with loading and error states
 */
export function createLazyComponent(loader, options = {}) {
  return defineAsyncComponent({
    loader,
    loadingComponent: options.loading || null,
    errorComponent: options.error || null,
    delay: options.delay || 200,
    timeout: options.timeout || 3000,
    suspensible: options.suspensible || false,
    onError: (error, retry, fail, attempts) => {
      console.error(`Failed to load component (attempt ${attempts}):`, error)
      if (attempts <= 3) {
        retry()
      } else {
        fail()
      }
    }
  })
}

/**
 * Lazy load route components
 */
export const lazyRoute = (componentPath) => {
  return () => import(`../views/${componentPath}.vue`)
}

/**
 * Lazy load modal components
 */
export const lazyModal = (componentPath) => {
  return createLazyComponent(
    () => import(`../components/modals/${componentPath}.vue`),
    {
      delay: 100,
      timeout: 5000
    }
  )
}

/**
 * Preload component for better UX
 */
export function preloadComponent(componentPath) {
  const componentLoader = () => import(`../views/${componentPath}.vue`)
  
  // Preload on hover or user interaction
  return {
    onMouseEnter: () => componentLoader(),
    onFocus: () => componentLoader()
  }
}

/**
 * Image lazy loading with intersection observer
 */
export class LazyImageLoader {
  constructor(options = {}) {
    this.options = {
      rootMargin: '50px',
      threshold: 0.1,
      ...options
    }
    this.observer = null
    this.init()
  }

  init() {
    if ('IntersectionObserver' in window) {
      this.observer = new IntersectionObserver(
        this.handleIntersection.bind(this),
        this.options
      )
    }
  }

  observe(element) {
    if (this.observer && element) {
      this.observer.observe(element)
    } else {
      // Fallback for browsers without IntersectionObserver
      this.loadImage(element)
    }
  }

  unobserve(element) {
    if (this.observer && element) {
      this.observer.unobserve(element)
    }
  }

  handleIntersection(entries) {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        this.loadImage(entry.target)
        this.observer.unobserve(entry.target)
      }
    })
  }

  loadImage(img) {
    const src = img.dataset.src
    if (src) {
      img.src = src
      img.classList.remove('lazy')
      img.classList.add('loaded')
    }
  }

  destroy() {
    if (this.observer) {
      this.observer.disconnect()
    }
  }
}

/**
 * Vue directive for lazy loading images
 */
export const vLazyImage = {
  mounted(el, binding) {
    const imageLoader = new LazyImageLoader()
    
    // Store original src in data attribute
    if (binding.value) {
      el.dataset.src = binding.value
      el.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMSIgaGVpZ2h0PSIxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9IiNjY2MiLz48L3N2Zz4='
      el.classList.add('lazy')
    }
    
    imageLoader.observe(el)
    
    // Store loader instance for cleanup
    el._lazyImageLoader = imageLoader
  },
  
  unmounted(el) {
    if (el._lazyImageLoader) {
      el._lazyImageLoader.destroy()
    }
  }
}

/**
 * Content lazy loading with virtual scrolling
 */
export class VirtualScrollLoader {
  constructor(container, options = {}) {
    this.container = container
    this.options = {
      itemHeight: 100,
      buffer: 5,
      ...options
    }
    this.visibleStart = 0
    this.visibleEnd = 0
    this.init()
  }

  init() {
    this.updateVisibleRange()
    this.container.addEventListener('scroll', this.throttledScroll.bind(this))
  }

  throttledScroll = this.throttle(this.updateVisibleRange.bind(this), 16)

  updateVisibleRange() {
    const scrollTop = this.container.scrollTop
    const containerHeight = this.container.clientHeight
    
    this.visibleStart = Math.max(0, Math.floor(scrollTop / this.options.itemHeight) - this.options.buffer)
    this.visibleEnd = Math.min(
      this.options.totalItems,
      Math.ceil((scrollTop + containerHeight) / this.options.itemHeight) + this.options.buffer
    )
    
    this.options.onUpdate?.(this.visibleStart, this.visibleEnd)
  }

  throttle(func, delay) {
    let timeoutId
    let lastExecTime = 0
    return function (...args) {
      const currentTime = Date.now()
      
      if (currentTime - lastExecTime > delay) {
        func.apply(this, args)
        lastExecTime = currentTime
      } else {
        clearTimeout(timeoutId)
        timeoutId = setTimeout(() => {
          func.apply(this, args)
          lastExecTime = Date.now()
        }, delay - (currentTime - lastExecTime))
      }
    }
  }

  destroy() {
    this.container.removeEventListener('scroll', this.throttledScroll)
  }
}

/**
 * Resource prefetching utilities
 */
export const ResourcePrefetcher = {
  /**
   * Prefetch a JavaScript module
   */
  prefetchModule(modulePath) {
    const link = document.createElement('link')
    link.rel = 'modulepreload'
    link.href = modulePath
    document.head.appendChild(link)
  },

  /**
   * Prefetch an image
   */
  prefetchImage(src) {
    const link = document.createElement('link')
    link.rel = 'prefetch'
    link.href = src
    link.as = 'image'
    document.head.appendChild(link)
  },

  /**
   * Prefetch a CSS file
   */
  prefetchCSS(href) {
    const link = document.createElement('link')
    link.rel = 'prefetch'
    link.href = href
    link.as = 'style'
    document.head.appendChild(link)
  },

  /**
   * Preload critical resources
   */
  preloadCritical(resources) {
    resources.forEach(resource => {
      const link = document.createElement('link')
      link.rel = 'preload'
      link.href = resource.href
      link.as = resource.as
      if (resource.type) link.type = resource.type
      if (resource.crossorigin) link.crossOrigin = resource.crossorigin
      document.head.appendChild(link)
    })
  }
}

/**
 * Performance monitoring for lazy loading
 */
export const LazyLoadingMetrics = {
  timings: new Map(),

  start(key) {
    this.timings.set(key, performance.now())
  },

  end(key) {
    const startTime = this.timings.get(key)
    if (startTime) {
      const duration = performance.now() - startTime
      console.log(`Lazy loading ${key}: ${duration.toFixed(2)}ms`)
      this.timings.delete(key)
      return duration
    }
  },

  measure(key, fn) {
    this.start(key)
    const result = fn()
    
    if (result instanceof Promise) {
      return result.finally(() => this.end(key))
    } else {
      this.end(key)
      return result
    }
  }
}

