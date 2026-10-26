import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './assets/main.css'
import { useAnalytics } from './composables/useAnalytics'
import { features } from './config/features'
import { authAPI } from './services/api'

// Lazy load views for better performance
const Home = () => import('./views/Home.vue')
const Diagnose = () => import('./views/Diagnose.vue')
const MyCars = () => import('./views/MyCars.vue')
const CarParts = () => import('./views/CarParts.vue')
const Login = () => import('./views/Login.vue')
const Register = () => import('./views/Register.vue')
const Dashboard = () => import('./views/Dashboard.vue')
const Admin = () => import('./views/Admin.vue')
const ApiCredits = () => import('./views/ApiCredits.vue')
const Visitors = () => import('./views/Visitors.vue')
const Statistics = () => import('./views/Statistics.vue')
const ProfileEdit = () => import('./views/ProfileEdit.vue')
const DiagnosisHistory = () => import('./views/DiagnosisHistory.vue')
const PrivacyPolicy = () => import('./views/PrivacyPolicy.vue')
const TermsOfService = () => import('./views/TermsOfService.vue')
const CookiePolicy = () => import('./views/CookiePolicy.vue')
const SubscriptionPlans = () => import('./views/SubscriptionPlans.vue')
const SubscriptionDashboard = () => import('./views/SubscriptionDashboard.vue')
const Pricing = () => import('./views/Pricing.vue')
const HowItWorks = () => import('./views/HowItWorks.vue')
const VehicleTwin = () => import('./diagnostic-ecosystem/views/VehicleTwin.vue')

// Lazy load components
const Navbar = () => import('./components/Navbar.vue')
const Footer = () => import('./components/Footer.vue')
const NotificationSystem = () => import('./components/NotificationSystem.vue')

// Router configuration with lazy loading
const routes = [
  { 
    path: '/', 
    name: 'Home', 
    component: Home,
    meta: { 
      title: 'CarWise AI - Smart Car Diagnosis',
      description: 'AI-powered car diagnosis and maintenance platform'
    }
  },
  {
    path: '/how-it-works',
    name: 'HowItWorks',
    component: HowItWorks,
    meta: {
      title: 'How CarWise.ai Works — From Diagnosis to Repair',
      description: 'Learn how CarWise.ai guides you from car symptoms to AI diagnosis, parts, and ongoing maintenance.'
    }
  },
  { 
    path: '/dashboard', 
    name: 'Dashboard', 
    component: Dashboard,
    meta: { 
      title: 'Dashboard - CarWise AI',
      description: 'Your personal car management dashboard',
      requiresAuth: true
    }
  },
  { 
    path: '/admin', 
    name: 'Admin', 
    component: Admin,
    meta: { 
      title: 'Admin Panel - CarWise AI',
      description: 'System administration panel',
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/admin/api-credits',
    name: 'ApiCredits',
    component: ApiCredits,
    meta: {
      title: 'API credits - CarWise AI',
      description: 'Admin view of API credit balances and usage',
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/admin/visitors',
    name: 'Visitors',
    component: Visitors,
    meta: {
      title: 'Visitors - CarWise AI',
      description: 'Admin view of visitors, registrations, requests, and spending',
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  {
    path: '/admin/statistics',
    name: 'Statistics',
    component: Statistics,
    meta: {
      title: 'Statistics - CarWise AI',
      description: 'Admin view of platform spend, user payments, and what is left',
      requiresAuth: true,
      requiresAdmin: true
    }
  },
  { 
    path: '/diagnose', 
    name: 'Diagnose', 
    component: Diagnose,
    meta: { 
      title: 'AI Diagnosis - CarWise AI',
      description: 'Get instant AI-powered car diagnosis'
    }
  },
  { 
    path: '/my-cars', 
    name: 'MyCars', 
    component: MyCars,
    meta: { 
      title: 'My Cars - CarWise AI',
      description: 'Manage your vehicle fleet',
      requiresAuth: true
    }
  },
  { 
    path: '/car-parts', 
    name: 'CarParts', 
    component: CarParts,
    meta: { 
      title: 'Car Parts - CarWise AI',
      description: 'Find authorized car parts for your vehicle'
    }
  },
  { 
    path: '/login', 
    name: 'Login', 
    component: Login,
    meta: { 
      title: 'Login - CarWise AI',
      description: 'Sign in to your CarWise account'
    }
  },
  { 
    path: '/register', 
    name: 'Register', 
    component: Register,
    meta: { 
      title: 'Register - CarWise AI',
      description: 'Create your CarWise account'
    }
  },
  { 
    path: '/profile/edit', 
    name: 'ProfileEdit', 
    component: ProfileEdit,
    meta: { 
      title: 'Edit Profile - CarWise AI',
      description: 'Update your profile information',
      requiresAuth: true
    }
  },
  { 
    path: '/diagnosis/history', 
    name: 'DiagnosisHistory', 
    component: DiagnosisHistory,
    meta: { 
      title: 'Diagnosis History - CarWise AI',
      description: 'View your past car diagnoses',
      requiresAuth: true
    }
  },
  { 
    path: '/privacy-policy', 
    name: 'PrivacyPolicy', 
    component: PrivacyPolicy,
    meta: { 
      title: 'Privacy Policy - CarWise AI',
      description: 'Learn how we protect your privacy and personal information'
    }
  },
  { 
    path: '/terms-of-service', 
    name: 'TermsOfService', 
    component: TermsOfService,
    meta: { 
      title: 'Terms of Service - CarWise AI',
      description: 'Read our terms and conditions for using CarWise AI'
    }
  },
  { 
    path: '/cookie-policy', 
    name: 'CookiePolicy', 
    component: CookiePolicy,
    meta: { 
      title: 'Cookie Policy - CarWise AI',
      description: 'Learn about our cookie usage and how to manage your preferences'
    }
  },
  { 
    path: '/subscription/plans', 
    name: 'SubscriptionPlans', 
    component: SubscriptionPlans,
    meta: { 
      title: 'Subscription Plans - CarWise AI',
      description: 'Choose the perfect subscription plan for your needs',
      requiresAuth: true
    }
  },
  { 
    path: '/subscription/dashboard', 
    name: 'SubscriptionDashboard', 
    component: SubscriptionDashboard,
    meta: { 
      title: 'Subscription Dashboard - CarWise AI',
      description: 'Manage your subscription and billing information',
      requiresAuth: true
    }
  },
  { 
    path: '/pricing', 
    name: 'Pricing', 
    component: Pricing,
    meta: { 
      title: 'Pricing & Subscription Plans - CarWise AI',
      description: 'Choose the perfect plan for your car diagnostic needs',
      requiresAuth: false
    }
  },
  {
    path: '/vehicle-twin',
    name: 'VehicleTwin',
    component: VehicleTwin,
    meta: {
      title: 'Vehicle Twin - CarWise AI',
      description: 'Your digital vehicle profiles — VIN identification, diagnostics, and history',
      requiresAuth: true
    }
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Router guards for authentication and meta management
router.beforeEach(async (to, from, next) => {
  if (!features.carParts && to.path.startsWith('/car-parts')) {
    next('/')
    return
  }

  if (to.meta.requiresAdmin) {
    const token = localStorage.getItem('token')
    if (!token) {
      next({ path: '/login', query: { redirect: to.fullPath } })
      return
    }

    try {
      const response = await authAPI.getUser()
      const account = response.data?.user
      if (!account || account.role !== 'admin') {
        next('/')
        return
      }
      localStorage.setItem('user', JSON.stringify(account))
    } catch (error) {
      next({ path: '/login', query: { redirect: to.fullPath } })
      return
    }
  }

  // Update page title and meta description
  if (to.meta.title) {
    document.title = to.meta.title
  }
  if (to.meta.description) {
    const metaDescription = document.querySelector('meta[name="description"]')
    if (metaDescription) {
      metaDescription.setAttribute('content', to.meta.description)
    }
  }

  // Track page view with Google Analytics
  if (typeof window !== 'undefined' && window.gtag) {
    window.gtag('event', 'page_view', {
      page_title: to.meta.title || to.name,
      page_location: window.location.href,
      page_path: to.path
    })
  }

  // Check authentication for protected routes
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('token')
    if (!token) {
      next('/login')
      return
    }
  }

  // Redirect authenticated users away from auth pages
  if ((to.name === 'Login' || to.name === 'Register') && localStorage.getItem('token')) {
    const redirect = to.query.redirect
    if (typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')) {
      next(redirect)
      return
    }
    next('/dashboard')
    return
  }

  next()
})

// Global loading state for route changes
router.beforeResolve((to, from, next) => {
  // Show loading indicator for route changes
  if (to.name !== from.name) {
    // You can add a global loading state here if needed
  }
  next()
})

const visitorId = () => {
  const existing = localStorage.getItem('carwise_visitor_id')
  if (existing) return existing
  const bytes = crypto.getRandomValues(new Uint8Array(16))
  bytes[6] = (bytes[6] & 0x0f) | 0x40
  bytes[8] = (bytes[8] & 0x3f) | 0x80
  const hex = [...bytes].map((byte) => byte.toString(16).padStart(2, '0')).join('')
  const created = `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`
  localStorage.setItem('carwise_visitor_id', created)
  return created
}

let lastVisitPath = ''

router.afterEach((to) => {
  try {
    if (!to.path || to.path === lastVisitPath) return
    lastVisitPath = to.path
    const headers = { 'Content-Type': 'application/json', Accept: 'application/json' }
    const token = localStorage.getItem('token')
    if (token) headers.Authorization = `Bearer ${token}`
    fetch('/api/visits', {
      method: 'POST',
      headers,
      body: JSON.stringify({ path: to.path, visitor_id: visitorId() }),
      keepalive: true,
    }).catch(() => {})
  } catch (error) {
    // A missed visit must not interrupt the page.
  }
})

const app = createApp(App)

app.use(router)
app.component('Navbar', Navbar)

app.mount('#app')