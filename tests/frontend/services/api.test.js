import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { api, authAPI, carsAPI, diagnosisAPI, mechanicsAPI } from '@/services/api.js'

// Mock axios
const mockAxios = {
  create: vi.fn(() => mockAxios),
  get: vi.fn(),
  post: vi.fn(),
  put: vi.fn(),
  delete: vi.fn(),
  interceptors: {
    request: { use: vi.fn() },
    response: { use: vi.fn() }
  }
}

vi.mock('axios', () => ({
  default: mockAxios
}))

describe('API Service', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  describe('Base API instance', () => {
    it('should be defined', () => {
      expect(api).toBeDefined()
    })
  })

  describe('Auth API', () => {
    it('should have login method', () => {
      expect(authAPI.login).toBeDefined()
      expect(typeof authAPI.login).toBe('function')
    })

    it('should have register method', () => {
      expect(authAPI.register).toBeDefined()
      expect(typeof authAPI.register).toBe('function')
    })

    it('should have logout method', () => {
      expect(authAPI.logout).toBeDefined()
      expect(typeof authAPI.logout).toBe('function')
    })

    it('should call correct endpoint for login', () => {
      const loginData = { email: 'test@example.com', password: 'password' }
      authAPI.login(loginData)
      
      expect(mockAxios.post).toHaveBeenCalledWith('/login', loginData)
    })

    it('should call correct endpoint for register', () => {
      const registerData = { 
        name: 'John Doe',
        email: 'john@example.com', 
        password: 'password',
        password_confirmation: 'password'
      }
      authAPI.register(registerData)
      
      expect(mockAxios.post).toHaveBeenCalledWith('/register', registerData)
    })

    it('should call correct endpoint for logout', () => {
      authAPI.logout()
      
      expect(mockAxios.post).toHaveBeenCalledWith('/logout')
    })
  })

  describe('Cars API', () => {
    it('should have all CRUD methods', () => {
      expect(carsAPI.getAll).toBeDefined()
      expect(carsAPI.create).toBeDefined()
      expect(carsAPI.getById).toBeDefined()
      expect(carsAPI.update).toBeDefined()
      expect(carsAPI.delete).toBeDefined()
    })

    it('should call correct endpoint for getting all cars', () => {
      carsAPI.getAll()
      
      expect(mockAxios.get).toHaveBeenCalledWith('/cars')
    })

    it('should call correct endpoint for creating car', () => {
      const carData = {
        brand_id: 1,
        model_id: 1,
        year: 2020,
        license_plate: 'ABC-123'
      }
      carsAPI.create(carData)
      
      expect(mockAxios.post).toHaveBeenCalledWith('/cars', carData)
    })

    it('should call correct endpoint for getting car by id', () => {
      const carId = 123
      carsAPI.getById(carId)
      
      expect(mockAxios.get).toHaveBeenCalledWith(`/cars/${carId}`)
    })

    it('should call correct endpoint for updating car', () => {
      const carId = 123
      const updateData = { mileage: 50000 }
      carsAPI.update(carId, updateData)
      
      expect(mockAxios.put).toHaveBeenCalledWith(`/cars/${carId}`, updateData)
    })

    it('should call correct endpoint for deleting car', () => {
      const carId = 123
      carsAPI.delete(carId)
      
      expect(mockAxios.delete).toHaveBeenCalledWith(`/cars/${carId}`)
    })

    it('should call statistics endpoint', () => {
      carsAPI.getStatistics()
      
      expect(mockAxios.get).toHaveBeenCalledWith('/cars/statistics')
    })
  })

  describe('Diagnosis API', () => {
    it('should have diagnosis methods', () => {
      expect(diagnosisAPI.start).toBeDefined()
      expect(diagnosisAPI.getResult).toBeDefined()
      expect(diagnosisAPI.getHistory).toBeDefined()
    })

    it('should call correct endpoint for starting diagnosis', () => {
      const diagnosisData = {
        make: 'Toyota',
        model: 'Camry',
        year: 2020,
        description: 'Engine problem'
      }
      diagnosisAPI.start(diagnosisData)
      
      expect(mockAxios.post).toHaveBeenCalledWith('/diagnosis/start', diagnosisData)
    })

    it('should call correct endpoint for getting diagnosis result', () => {
      const sessionId = 'test-session-123'
      diagnosisAPI.getResult(sessionId)
      
      expect(mockAxios.get).toHaveBeenCalledWith(`/diagnosis/result/${sessionId}`)
    })

    it('should call correct endpoint for getting diagnosis history', () => {
      diagnosisAPI.getHistory()
      
      expect(mockAxios.get).toHaveBeenCalledWith('/diagnosis/history')
    })
  })

  describe('Mechanics API', () => {
    it('should have mechanics methods', () => {
      expect(mechanicsAPI.getAll).toBeDefined()
      expect(mechanicsAPI.getById).toBeDefined()
      expect(mechanicsAPI.search).toBeDefined()
    })

    it('should call correct endpoint for getting all mechanics', () => {
      mechanicsAPI.getAll()
      
      expect(mockAxios.get).toHaveBeenCalledWith('/mechanics')
    })

    it('should call correct endpoint for getting mechanic by id', () => {
      const mechanicId = 456
      mechanicsAPI.getById(mechanicId)
      
      expect(mockAxios.get).toHaveBeenCalledWith(`/mechanics/${mechanicId}`)
    })

    it('should call search endpoint with query parameters', () => {
      const query = 'brake specialist'
      const location = 'New York'
      mechanicsAPI.search(query, location)
      
      expect(mockAxios.get).toHaveBeenCalledWith('/mechanics/search', {
        params: { query, location }
      })
    })
  })

  describe('Error Handling', () => {
    it('should handle API errors gracefully', async () => {
      const errorResponse = {
        response: {
          status: 400,
          data: { message: 'Bad Request' }
        }
      }
      
      mockAxios.get.mockRejectedValueOnce(errorResponse)
      
      try {
        await carsAPI.getAll()
      } catch (error) {
        expect(error).toBeDefined()
      }
    })

    it('should handle network errors', async () => {
      const networkError = new Error('Network Error')
      mockAxios.get.mockRejectedValueOnce(networkError)
      
      try {
        await carsAPI.getAll()
      } catch (error) {
        expect(error.message).toBe('Network Error')
      }
    })
  })

  describe('Request/Response Interceptors', () => {
    it('should setup request interceptors', () => {
      expect(mockAxios.interceptors.request.use).toHaveBeenCalled()
    })

    it('should setup response interceptors', () => {
      expect(mockAxios.interceptors.response.use).toHaveBeenCalled()
    })
  })
})



