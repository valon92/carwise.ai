import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest'
import { mount } from '@vue/test-utils'
import { createRouter, createMemoryHistory } from 'vue-router'
import { nextTick, ref } from 'vue'
import Navbar from '@/components/Navbar.vue'

const mockAuth = {
  user: ref(null),
  isAuthenticated: ref(false),
  checkAuth: vi.fn(),
  logout: vi.fn(),
}

vi.mock('@/composables/useAuth', () => ({
  useAuth: () => mockAuth,
}))

vi.mock('@/composables/useAnalytics', () => ({
  useAnalytics: () => ({
    trackEvent: vi.fn(),
  }),
}))

describe('Navbar', () => {
  let wrapper
  let router

  beforeEach(async () => {
    mockAuth.user.value = null
    mockAuth.isAuthenticated.value = false
    mockAuth.checkAuth.mockReset()
    mockAuth.logout.mockReset()

    router = createRouter({
      history: createMemoryHistory(),
      routes: [
        { path: '/', name: 'Home', component: { template: '<div />' } },
        { path: '/login', name: 'Login', component: { template: '<div />' } },
        { path: '/register', name: 'Register', component: { template: '<div />' } },
        { path: '/diagnose', name: 'Diagnose', component: { template: '<div />' } },
      ],
    })

    await router.push('/')
    await router.isReady()
  })

  afterEach(() => {
    wrapper?.unmount()
  })

  it('renders the brand for guests', async () => {
    wrapper = mount(Navbar, {
      global: {
        plugins: [router],
        stubs: {
          RouterLink: { template: '<a><slot /></a>' },
        },
      },
    })
    await nextTick()

    expect(wrapper.exists()).toBe(true)
    expect(wrapper.text().toLowerCase()).toMatch(/carwise|login|diagnose|home/)
  })

  it('shows the signed-in account name', async () => {
    mockAuth.user.value = { name: 'John Doe', first_name: 'John', role: 'customer' }
    mockAuth.isAuthenticated.value = true

    wrapper = mount(Navbar, {
      global: {
        plugins: [router],
        stubs: {
          RouterLink: { template: '<a><slot /></a>' },
        },
      },
    })
    await nextTick()

    expect(wrapper.text()).toContain('John')
  })
})
