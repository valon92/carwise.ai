// Vitest setup file
import { config } from '@vue/test-utils'

// Global configuration for Vue Test Utils
config.global.mocks = {
  $t: (key) => key, // Mock translation function
  $route: {
    params: {},
    query: {},
    path: '/'
  },
  $router: {
    push: vi.fn(),
    replace: vi.fn(),
    go: vi.fn(),
    back: vi.fn(),
    forward: vi.fn()
  }
}

// Mock global properties that might be used in components
global.CSS = { supports: () => false }

// Mock console methods for cleaner test output
global.console = {
  ...console,
  // Uncomment to suppress console.log during tests
  // log: vi.fn(),
  // warn: vi.fn(),
  // error: vi.fn(),
}

