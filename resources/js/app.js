import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import './assets/main.css'

// Lazy load views for better performance
const Home = () => import('./views/Home.vue')
const Diagnose = () => import('./views/Diagnose.vue')
const MyCars = () => import('./views/MyCars.vue')
const Mechanics = () => import('./views/Mechanics.vue')
const Login = () => import('./views/Login.vue')
const Register = () => import('./views/Register.vue')
const Dashboard = () => import('./views/Dashboard.vue')

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
    path: '/mechanics', 
    name: 'Mechanics', 
    component: Mechanics,
    meta: { 
      title: 'Find Mechanics - CarWise AI',
      description: 'Connect with certified mechanics'
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
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

// Router guards for authentication and meta management
router.beforeEach((to, from, next) => {
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

const app = createApp(App)

app.use(router)
app.component('Navbar', Navbar)

app.mount('#app')