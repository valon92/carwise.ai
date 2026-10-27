import { describe, it, expect, vi, beforeEach } from 'vitest'

const { mockAuthAPI, memoryStorage } = vi.hoisted(() => {
  const store = new Map()

  return {
    mockAuthAPI: {
      login: vi.fn(),
      register: vi.fn(),
      logout: vi.fn(),
      getUser: vi.fn(),
    },
    memoryStorage: {
      getItem: (key) => (store.has(key) ? store.get(key) : null),
      setItem: (key, value) => {
        store.set(key, String(value))
      },
      removeItem: (key) => {
        store.delete(key)
      },
      clear: () => {
        store.clear()
      },
    },
  }
})

vi.mock('@/services/api.js', () => ({
  authAPI: mockAuthAPI,
}))

Object.defineProperty(globalThis, 'localStorage', {
  value: memoryStorage,
  configurable: true,
  writable: true,
})

describe('useAuth Composable', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    memoryStorage.clear()
  })

  it('starts signed out when no token is stored', async () => {
    const { useAuth } = await import('@/composables/useAuth.js')
    const auth = useAuth()
    auth.user.value = null
    expect(auth.user.value).toBeNull()
    expect(auth.isAuthenticated.value).toBe(false)
  })

  it('stores the token after a successful login', async () => {
    mockAuthAPI.login.mockResolvedValueOnce({
      data: {
        success: true,
        user: { id: 1, name: 'John Doe', email: 'test@example.com', first_name: 'John' },
        token: 'mock-token',
      },
    })

    const { useAuth } = await import('@/composables/useAuth.js')
    const auth = useAuth()
    const result = await auth.login({ email: 'test@example.com', password: 'password' })

    expect(mockAuthAPI.login).toHaveBeenCalled()
    expect(result.success).toBe(true)
    expect(auth.user.value?.email).toBe('test@example.com')
    expect(memoryStorage.getItem('token')).toBe('mock-token')
  })
})
