import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { lazyRoute, ResourcePrefetcher } from '@/utils/lazyLoading'

// Lazy load all route components for better performance
const routes = [
  {
    path: '/',
    name: 'Home',
    component: lazyRoute('Home'),
    meta: { 
      requiresAuth: false,
      preload: true // Preload this route for better UX
    }
  },
  {
    path: '/login',
    name: 'Login',
    component: lazyRoute('Login'),
    meta: { 
      requiresAuth: false,
      guest: true // Redirect if already authenticated
    }
  },
  {
    path: '/register',
    name: 'Register',
    component: lazyRoute('Register'),
    meta: { 
      requiresAuth: false,
      guest: true
    }
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: lazyRoute('Dashboard'),
    meta: { 
      requiresAuth: true,
      preload: true
    }
  },
  {
    path: '/diagnose',
    name: 'Diagnose',
    component: lazyRoute('Diagnose'),
    meta: { 
      requiresAuth: false,
      prefetch: ['api/car-brands/popular', 'api/languages']
    }
  },
  {
    path: '/diagnosis-history',
    name: 'DiagnosisHistory',
    component: lazyRoute('DiagnosisHistory'),
    meta: { 
      requiresAuth: true
    }
  },
  {
    path: '/cars',
    name: 'MyCars',
    component: lazyRoute('MyCars'),
    meta: { 
      requiresAuth: true,
      prefetch: ['api/car-brands', 'api/cars']
    }
  },
  {
    path: '/cars/simple',
    name: 'MyCarsSimple',
    component: lazyRoute('MyCarsSimple'),
    meta: { 
      requiresAuth: true
    }
  },
  {
    path: '/car-parts',
    name: 'CarParts',
    component: lazyRoute('CarParts'),
    meta: { 
      requiresAuth: false,
      prefetch: ['api/car-parts/featured', 'api/car-parts']
    }
  },
  {
    path: '/profile/edit',
    name: 'ProfileEdit',
    component: lazyRoute('ProfileEdit'),
    meta: { 
      requiresAuth: true
    }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: lazyRoute('Admin'),
    meta: { 
      requiresAuth: true,
      requiresRole: 'admin'
    }
  },
  {
    path: '/privacy-policy',
    name: 'PrivacyPolicy',
    component: lazyRoute('PrivacyPolicy'),
    meta: { 
      requiresAuth: false
    }
  },
  {
    path: '/terms-of-service',
    name: 'TermsOfService',
    component: lazyRoute('TermsOfService'),
    meta: { 
      requiresAuth: false
    }
  },
  {
    path: '/cookie-policy',
    name: 'CookiePolicy',
    component: lazyRoute('CookiePolicy'),
    meta: { 
      requiresAuth: false
    }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: lazyRoute('NotFound'),
    meta: { 
      requiresAuth: false
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    // Smooth scroll behavior
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth',
      }
    } else {
      return { 
        top: 0,
        behavior: 'smooth'
      }
    }
  }
})

// Navigation guards with performance optimizations
router.beforeEach(async (to, from, next) => {
  const { isAuthenticated, user } = useAuth()
  
  // Start preloading route component as early as possible
  if (to.meta.preload && to.component) {
    to.component().catch(() => {}) // Preload but don't block navigation
  }
  
  // Authentication check
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return next({ name: 'Login', query: { redirect: to.fullPath } })
  }
  
  // Guest-only routes (redirect if authenticated)
  if (to.meta.guest && isAuthenticated.value) {
    return next({ name: 'Dashboard' })
  }
  
  // Role-based access control
  if (to.meta.requiresRole && user.value) {
    if (user.value.role !== to.meta.requiresRole) {
      return next({ name: 'Dashboard' })
    }
  }
  
  // Prefetch API data for the route
  if (to.meta.prefetch) {
    prefetchRouteData(to.meta.prefetch)
  }
  
  next()
})

// After navigation, handle additional optimizations
router.afterEach((to, from) => {
  // Update document title
  if (to.meta.title) {
    document.title = `${to.meta.title} - CarWise.ai`
  } else {
    document.title = 'CarWise.ai - AI-Powered Car Diagnostics'
  }
  
  // Preload likely next routes based on current route
  preloadLikelyRoutes(to.name)
  
  // Track page view for analytics
  if (typeof gtag !== 'undefined') {
    gtag('config', 'GA_TRACKING_ID', {
      page_title: document.title,
      page_location: window.location.href,
    })
  }
})

/**
 * Prefetch API data for better UX
 */
function prefetchRouteData(endpoints) {
  if (Array.isArray(endpoints)) {
    endpoints.forEach(endpoint => {
      // Use fetch with cache for prefetching
      fetch(`/api/${endpoint}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
        }
      }).catch(() => {}) // Silent fail for prefetch
    })
  }
}

/**
 * Preload components for likely next routes
 */
function preloadLikelyRoutes(currentRoute) {
  const routeMap = {
    'Home': ['Login', 'Register', 'Diagnose'],
    'Login': ['Dashboard', 'Register'],
    'Register': ['Dashboard', 'Login'],
    'Dashboard': ['MyCars', 'DiagnosisHistory', 'Diagnose'],
    'Diagnose': ['DiagnosisHistory', 'MyCars'],
    'MyCars': ['Diagnose', 'Dashboard'],
  }
  
  const likelyRoutes = routeMap[currentRoute] || []
  
  likelyRoutes.forEach(routeName => {
    const route = routes.find(r => r.name === routeName)
    if (route && route.component) {
      // Preload with a small delay to not block current page
      setTimeout(() => {
        route.component().catch(() => {})
      }, 1000)
    }
  })
}

/**
 * Route-based resource prefetching
 */
router.beforeResolve((to, from, next) => {
  // Prefetch critical resources for the route
  const criticalResources = getCriticalResourcesForRoute(to.name)
  if (criticalResources.length > 0) {
    ResourcePrefetcher.preloadCritical(criticalResources)
  }
  
  next()
})

function getCriticalResourcesForRoute(routeName) {
  const resourceMap = {
    'Diagnose': [
      { href: '/images/cars/default-car.svg', as: 'image' },
      { href: '/api/car-brands/popular', as: 'fetch' }
    ],
    'MyCars': [
      { href: '/api/cars', as: 'fetch' },
      { href: '/api/car-brands', as: 'fetch' }
    ],
  }
  
  return resourceMap[routeName] || []
}

export default router
