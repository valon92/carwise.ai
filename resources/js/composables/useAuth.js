import { ref, computed, readonly } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../services/api'

// Global auth state
const user = ref(null)
const isLoading = ref(false)
const isAuthenticated = computed(() => !!user.value)

export function useAuth() {
  const router = useRouter()

  const login = async (credentials) => {
    try {
      isLoading.value = true
      const response = await authAPI.login(credentials)
      
      if (response.data.success) {
        user.value = response.data.user
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        
        // Show success notification
        if (window.$notify) {
          window.$notify.success('Welcome back!', `Hello ${response.data.user.first_name || response.data.user.name}`)
        }
        
        return { success: true, user: response.data.user }
      }
      
      return { success: false, message: 'Login failed' }
    } catch (error) {
      console.error('Login error:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Login failed. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const register = async (userData) => {
    try {
      isLoading.value = true
      const response = await authAPI.register(userData)
      
      if (response.data.success) {
        user.value = response.data.user
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        
        // Show success notification
        if (window.$notify) {
          window.$notify.success('Welcome to CarWise!', `Account created for ${response.data.user.first_name || response.data.user.name}`)
        }
        
        return { success: true, user: response.data.user }
      }
      
      return { success: false, message: 'Registration failed' }
    } catch (error) {
      console.error('Registration error:', error)
      return { 
        success: false, 
        message: error.response?.data?.message || 'Registration failed. Please try again.' 
      }
    } finally {
      isLoading.value = false
    }
  }

  const logout = async () => {
    try {
      await authAPI.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      // Clear local state regardless of API response
      user.value = null
      localStorage.removeItem('token')
      localStorage.removeItem('user')
      
      if (window.$notify) {
        window.$notify.info('Logged Out', 'You have been successfully logged out')
      }
      
      router.push('/login')
    }
  }

  const checkAuth = async () => {
    const token = localStorage.getItem('token')
    const storedUser = localStorage.getItem('user')
    
    if (token && storedUser) {
      try {
        user.value = JSON.parse(storedUser)
        // Optionally verify token with server
        const response = await authAPI.getUser()
        if (response.data.success) {
          user.value = response.data.user
          localStorage.setItem('user', JSON.stringify(response.data.user))
        }
        return true
      } catch (error) {
        console.error('Auth check failed:', error)
        // Clear invalid auth data
        user.value = null
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        return false
      }
    }
    
    return false
  }

  const getUser = () => user.value
  const getIsAuthenticated = () => isAuthenticated.value
  const getIsLoading = () => isLoading.value

  return {
    // State
    user: readonly(user),
    isAuthenticated,
    isLoading: readonly(isLoading),
    
    // Actions
    login,
    register,
    logout,
    checkAuth,
    
    // Getters
    getUser,
    getIsAuthenticated,
    getIsLoading
  }
}

// Initialize auth state from localStorage
const initializeAuth = () => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      user.value = JSON.parse(storedUser)
    } catch (error) {
      console.error('Failed to parse stored user:', error)
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    }
  }
}

// Auto-initialize on module load
initializeAuth()
