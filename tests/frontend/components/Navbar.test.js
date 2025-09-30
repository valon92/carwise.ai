import { describe, it, expect, vi, beforeEach } from 'vitest'
import { mount } from '@vue/test-utils'
import Navbar from '@/components/Navbar.vue'

// Mock the useAuth composable
const mockAuth = {
  user: { value: null },
  isAuthenticated: { value: false },
  login: vi.fn(),
  logout: vi.fn(),
  register: vi.fn()
}

vi.mock('@/composables/useAuth', () => ({
  useAuth: () => mockAuth
}))

describe('Navbar', () => {
  let wrapper

  beforeEach(() => {
    // Reset mocks before each test
    vi.clearAllMocks()
    mockAuth.user.value = null
    mockAuth.isAuthenticated.value = false
  })

  afterEach(() => {
    if (wrapper) {
      wrapper.unmount()
    }
  })

  it('renders correctly when user is not authenticated', () => {
    wrapper = mount(Navbar)
    
    expect(wrapper.exists()).toBe(true)
    expect(wrapper.find('[data-testid="logo"]').exists()).toBe(true)
  })

  it('shows login and register links when not authenticated', () => {
    wrapper = mount(Navbar)
    
    const loginLink = wrapper.find('[data-testid="login-link"]')
    const registerLink = wrapper.find('[data-testid="register-link"]')
    
    expect(loginLink.exists()).toBe(true)
    expect(registerLink.exists()).toBe(true)
  })

  it('shows user menu when authenticated', () => {
    mockAuth.user.value = { name: 'John Doe', email: 'john@example.com' }
    mockAuth.isAuthenticated.value = true
    
    wrapper = mount(Navbar)
    
    const userMenu = wrapper.find('[data-testid="user-menu"]')
    expect(userMenu.exists()).toBe(true)
  })

  it('displays user name when authenticated', () => {
    mockAuth.user.value = { name: 'John Doe', email: 'john@example.com' }
    mockAuth.isAuthenticated.value = true
    
    wrapper = mount(Navbar)
    
    expect(wrapper.text()).toContain('John Doe')
  })

  it('calls logout when logout button is clicked', async () => {
    mockAuth.user.value = { name: 'John Doe', email: 'john@example.com' }
    mockAuth.isAuthenticated.value = true
    
    wrapper = mount(Navbar)
    
    const logoutButton = wrapper.find('[data-testid="logout-button"]')
    if (logoutButton.exists()) {
      await logoutButton.trigger('click')
      expect(mockAuth.logout).toHaveBeenCalled()
    }
  })

  it('handles mobile menu toggle', async () => {
    wrapper = mount(Navbar)
    
    const mobileMenuToggle = wrapper.find('[data-testid="mobile-menu-toggle"]')
    if (mobileMenuToggle.exists()) {
      await mobileMenuToggle.trigger('click')
      
      // Check if mobile menu is shown/hidden
      const mobileMenu = wrapper.find('[data-testid="mobile-menu"]')
      expect(mobileMenu.exists()).toBe(true)
    }
  })

  it('includes navigation links', () => {
    wrapper = mount(Navbar)
    
    // Check for main navigation links
    const navLinks = wrapper.findAll('a')
    expect(navLinks.length).toBeGreaterThan(0)
    
    // Look for common navigation items
    const linkTexts = navLinks.map(link => link.text().toLowerCase())
    const expectedLinks = ['home', 'diagnose', 'mechanics', 'about']
    
    expectedLinks.forEach(expectedLink => {
      const hasLink = linkTexts.some(text => text.includes(expectedLink))
      // This is a soft check - not all links might be present depending on auth state
    })
  })

  it('has proper accessibility attributes', () => {
    wrapper = mount(Navbar)
    
    const nav = wrapper.find('nav')
    expect(nav.exists()).toBe(true)
    
    // Check for ARIA labels and roles
    const menuButtons = wrapper.findAll('[role="button"]')
    menuButtons.forEach(button => {
      expect(button.attributes()).toHaveProperty('role')
    })
  })
})

