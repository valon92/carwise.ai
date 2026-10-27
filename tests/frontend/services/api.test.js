import { describe, it, expect, vi, beforeEach } from 'vitest'

const { mockAxios } = vi.hoisted(() => {
  const instance = {
    get: vi.fn(),
    post: vi.fn(),
    put: vi.fn(),
    delete: vi.fn(),
    interceptors: {
      request: { use: vi.fn() },
      response: { use: vi.fn() },
    },
  }

  return {
    mockAxios: Object.assign(instance, {
      create: vi.fn(() => instance),
      defaults: { headers: { common: {} } },
    }),
  }
})

vi.mock('axios', () => ({
  default: mockAxios,
}))

describe('API Service', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('exports the auth helpers', async () => {
    const apiModule = await import('@/services/api.js')
    expect(apiModule.default).toBeDefined()
    expect(typeof apiModule.authAPI.login).toBe('function')
    expect(typeof apiModule.authAPI.register).toBe('function')
    expect(typeof apiModule.authAPI.logout).toBe('function')
    expect(typeof apiModule.authAPI.getUser).toBe('function')
  })
})
