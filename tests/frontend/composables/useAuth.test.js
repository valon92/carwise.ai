import { describe, it, expect, vi, beforeEach } from 'vitest'
import { useAuth } from '@/composables/useAuth.js'

// Mock the API service
const mockAuthAPI = {
  login: vi.fn(),
  register: vi.fn(),
  logout: vi.fn(),
  me: vi.fn(),
  refresh: vi.fn()
}

const mockAPI = {
  authAPI: mockAuthAPI
}

vi.mock('@/services/api.js', () => mockAPI)

// Mock localStorage
const localStorageMock = {
  getItem: vi.fn(),
  setItem: vi.fn(),
  removeItem: vi.fn(),
  clear: vi.fn(),
}

global.localStorage = localStorageMock

describe('useAuth Composable', () => {
  let auth

  beforeEach(() => {
    vi.clearAllMocks()
    localStorage.getItem.mockReturnValue(null)
    
    // Get fresh instance of useAuth for each test
    auth = useAuth()
  })

  describe('Initial State', () => {
    it('should initialize with null user when no token in localStorage', () => {
      expect(auth.user.value).toBeNull()
      expect(auth.isAuthenticated.value).toBe(false)
    })

    it('should initialize with user data when token exists in localStorage', () => {
      const mockUser = { id: 1, name: 'John Doe', email: 'john@example.com' }
      localStorage.getItem.mockReturnValue('mock-token')
      
      // Re-initialize auth to test token loading
      auth = useAuth()
      
      expect(localStorage.getItem).toHaveBeenCalledWith('token')
    })
  })

  describe('Login', () => {
    it('should login successfully with valid credentials', async () => {
      const credentials = { email: 'test@example.com', password: 'password' }
      const mockResponse = {
        data: {
          success: true,
          data: {
            user: { id: 1, name: 'John Doe', email: 'test@example.com' },
            token: 'mock-token'
          }
        }
      }
      
      mockAuthAPI.login.mockResolvedValueOnce(mockResponse)
      
      const result = await auth.login(credentials)
      
      expect(mockAuthAPI.login).toHaveBeenCalledWith(credentials)
      expect(result.success).toBe(true)
      expect(auth.user.value).toEqual(mockResponse.data.data.user)
      expect(auth.isAuthenticated.value).toBe(true)
      expect(localStorage.setItem).toHaveBeenCalledWith('token', 'mock-token')
    })

    it('should handle login failure', async () => {
      const credentials = { email: 'test@example.com', password: 'wrong-password' }
      const mockError = {
        response: {
          data: {
            success: false,
            message: 'Invalid credentials'
          }
        }
      }
      
      mockAuthAPI.login.mockRejectedValueOnce(mockError)
      
      const result = await auth.login(credentials)
      
      expect(result.success).toBe(false)
      expect(result.message).toBe('Invalid credentials')
      expect(auth.user.value).toBeNull()
      expect(auth.isAuthenticated.value).toBe(false)
    })

    it('should handle network errors during login', async () => {
      const credentials = { email: 'test@example.com', password: 'password' }
      mockAuthAPI.login.mockRejectedValueOnce(new Error('Network Error'))
      
      const result = await auth.login(credentials)
      
      expect(result.success).toBe(false)
      expect(result.message).toBe('Network error. Please try again.')
    })
  })

  describe('Register', () => {
    it('should register successfully with valid data', async () => {
      const userData = {
        name: 'John Doe',
        email: 'john@example.com',
        password: 'password',
        password_confirmation: 'password'
      }
      
      const mockResponse = {
        data: {
          success: true,
          data: {
            user: { id: 1, name: 'John Doe', email: 'john@example.com' },
            token: 'mock-token'
          }
        }
      }
      
      mockAuthAPI.register.mockResolvedValueOnce(mockResponse)
      
      const result = await auth.register(userData)
      
      expect(mockAuthAPI.register).toHaveBeenCalledWith(userData)
      expect(result.success).toBe(true)
      expect(auth.user.value).toEqual(mockResponse.data.data.user)
      expect(auth.isAuthenticated.value).toBe(true)
    })

    it('should handle registration validation errors', async () => {
      const userData = {
        name: '',
        email: 'invalid-email',
        password: '123',
        password_confirmation: '456'
      }
      
      const mockError = {
        response: {
          data: {
            success: false,
            message: 'Validation failed',
            errors: {
              email: ['Email must be valid'],
              password: ['Password too short']
            }
          }
        }
      }
      
      mockAuthAPI.register.mockRejectedValueOnce(mockError)
      
      const result = await auth.register(userData)
      
      expect(result.success).toBe(false)
      expect(result.errors).toEqual(mockError.response.data.errors)
    })
  })

  describe('Logout', () => {
    it('should logout successfully', async () => {
      // Set up authenticated state
      auth.user.value = { id: 1, name: 'John Doe' }
      
      mockAuthAPI.logout.mockResolvedValueOnce({ data: { success: true } })
      
      await auth.logout()
      
      expect(mockAuthAPI.logout).toHaveBeenCalled()
      expect(auth.user.value).toBeNull()
      expect(auth.isAuthenticated.value).toBe(false)
      expect(localStorage.removeItem).toHaveBeenCalledWith('token')
    })

    it('should clear local state even if API call fails', async () => {
      // Set up authenticated state
      auth.user.value = { id: 1, name: 'John Doe' }
      
      mockAuthAPI.logout.mockRejectedValueOnce(new Error('API Error'))
      
      await auth.logout()
      
      expect(auth.user.value).toBeNull()
      expect(auth.isAuthenticated.value).toBe(false)
      expect(localStorage.removeItem).toHaveBeenCalledWith('token')
    })
  })

  describe('User Profile Management', () => {
    it('should load user profile when token exists', async () => {
      localStorage.getItem.mockReturnValue('mock-token')
      const mockUser = { id: 1, name: 'John Doe', email: 'john@example.com' }
      
      mockAuthAPI.me.mockResolvedValueOnce({
        data: { success: true, data: mockUser }
      })
      
      await auth.loadUser()
      
      expect(mockAuthAPI.me).toHaveBeenCalled()
      expect(auth.user.value).toEqual(mockUser)
      expect(auth.isAuthenticated.value).toBe(true)
    })

    it('should handle expired token', async () => {
      localStorage.getItem.mockReturnValue('expired-token')
      
      mockAuthAPI.me.mockRejectedValueOnce({
        response: { status: 401 }
      })
      
      await auth.loadUser()
      
      expect(auth.user.value).toBeNull()
      expect(auth.isAuthenticated.value).toBe(false)
      expect(localStorage.removeItem).toHaveBeenCalledWith('token')
    })
  })

  describe('Reactive Properties', () => {
    it('should update isAuthenticated when user changes', () => {
      expect(auth.isAuthenticated.value).toBe(false)
      
      auth.user.value = { id: 1, name: 'John Doe' }
      
      expect(auth.isAuthenticated.value).toBe(true)
      
      auth.user.value = null
      
      expect(auth.isAuthenticated.value).toBe(false)
    })
  })
})



