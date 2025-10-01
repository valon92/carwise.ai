<template>
  <nav class="sticky top-0 z-40 bg-white/80 dark:bg-secondary-900/80 backdrop-blur-md border-b border-secondary-200 dark:border-secondary-700 shadow-soft">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <!-- Logo -->
        <div class="flex items-center">
          <router-link to="/" class="flex items-center group">
            <div class="w-14 h-14 flex items-center justify-center group-hover:scale-105 transition-all duration-300">
              <img src="/icons/icon1.png" alt="CarWise.ai" class="w-12 h-12" />
            </div>
            <span class="ml-3 text-xl font-bold text-secondary-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors duration-200">
              
            </span>
          </router-link>
        </div>
        
        <!-- Navigation Links -->
        <div class="hidden md:flex items-center space-x-1">
          <router-link 
            to="/" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            Home
          </router-link>
          <router-link 
            v-if="isAuthenticated"
            to="/dashboard" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/dashboard' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            Dashboard
          </router-link>
          <router-link 
            to="/diagnose" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/diagnose' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Diagnose
          </router-link>
          <router-link 
            to="/my-cars" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/my-cars' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            My Cars
          </router-link>
          <router-link 
            to="/car-parts" 
            class="nav-link"
            :class="{ 'nav-link-active': $route.path === '/car-parts' }"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
            Car Parts
          </router-link>
        </div>
        
        <!-- Right side actions -->
        <div class="flex items-center space-x-3">
          <!-- Language Switcher -->
          
          <!-- Dark mode toggle -->
          <button 
            @click="$emit('toggle-dark-mode')"
            class="p-2 rounded-lg text-secondary-600 dark:text-secondary-400 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
            :title="isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'"
          >
            <svg v-if="!isDarkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </button>

          <!-- Mobile menu button -->
          <button 
            @click="toggleMobileMenu"
            class="md:hidden p-2 rounded-lg text-secondary-600 dark:text-secondary-400 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-all duration-200"
            :class="{ 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400': mobileMenuOpen }"
            title="Toggle Mobile Menu"
          >
            <span class="sr-only">Toggle Mobile Menu</span>
            <svg class="w-6 h-6 transition-transform duration-200" :class="{ 'rotate-90': mobileMenuOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>

          <!-- Auth buttons -->
          <template v-if="!isAuthenticated">
            <router-link to="/login" class="btn-ghost hidden sm:inline-flex">
              Login
            </router-link>
            <router-link to="/register" class="btn-primary hidden sm:inline-flex">
              Register
            </router-link>
          </template>
          <template v-else>
            <!-- Desktop Profile Dropdown -->
            <div class="relative hidden md:block">
              <button 
                @click="userDropdownOpen = !userDropdownOpen"
                class="flex items-center space-x-2 px-3 py-2 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              >
                <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                  <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">
                    {{ userInitials }}
                  </span>
                </div>
                <span class="text-sm font-medium">{{ user?.first_name || user?.name }}</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <!-- Dropdown Menu -->
              <div 
                v-if="userDropdownOpen"
                ref="userDropdown"
                class="absolute right-0 mt-2 w-48 bg-white dark:bg-secondary-800 rounded-lg shadow-lg border border-secondary-200 dark:border-secondary-700 py-1 z-50"
              >
                <div class="px-4 py-2 border-b border-secondary-200 dark:border-secondary-700">
                  <div class="text-sm font-medium text-secondary-700 dark:text-secondary-300">
                    {{ user?.first_name || user?.name }}
                  </div>
                  <div class="text-xs text-secondary-500 dark:text-secondary-500">
                    {{ user?.role === 'mechanic' ? 'Mechanic' : 'Car Owner' }}
                  </div>
                </div>
                
                <router-link 
                  to="/dashboard" 
                  class="flex items-center px-4 py-2 text-sm text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-700 transition-colors duration-200"
                  @click="userDropdownOpen = false"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                  </svg>
                  Dashboard
                </router-link>
                
                <router-link 
                  v-if="user?.role === 'admin'"
                  to="/admin" 
                  class="flex items-center px-4 py-2 text-sm text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-700 transition-colors duration-200"
                  @click="userDropdownOpen = false"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  Admin Panel
                </router-link>
                
                <button 
                  @click="logout" 
                  class="flex items-center w-full px-4 py-2 text-sm text-danger-600 dark:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors duration-200"
                >
                  <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  Logout
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>

      <!-- Mobile menu -->
      <div v-if="mobileMenuOpen" class="md:hidden bg-white dark:bg-secondary-900 border-t border-secondary-200 dark:border-secondary-700 shadow-lg">
        <div class="px-4 py-6 space-y-4">
          <!-- Navigation Links -->
          <div class="space-y-2">
            <router-link 
              to="/" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              :class="{ 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400': $route.path === '/' }"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
              </svg>
              Home
            </router-link>
            
            <router-link 
              v-if="isAuthenticated"
              to="/dashboard" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              :class="{ 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400': $route.path === '/dashboard' }"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
              </svg>
              Dashboard
            </router-link>
            
            <router-link 
              to="/diagnose" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              :class="{ 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400': $route.path === '/diagnose' }"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Diagnose
            </router-link>
            
            <router-link 
              to="/my-cars" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              :class="{ 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400': $route.path === '/my-cars' }"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              My Cars
            </router-link>
            
            <router-link 
              to="/car-parts" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              :class="{ 'bg-primary-100 dark:bg-primary-900 text-primary-600 dark:text-primary-400': $route.path === '/car-parts' }"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
              </svg>
              Car Parts
            </router-link>
            
          </div>

          <!-- Auth Section -->
          <div v-if="!isAuthenticated" class="pt-4 border-t border-secondary-200 dark:border-secondary-700 space-y-3">
            <router-link 
              to="/login" 
              class="flex items-center justify-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200 border border-secondary-300 dark:border-secondary-600"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              Login
            </router-link>
            <router-link 
              to="/register" 
              class="flex items-center justify-center px-4 py-3 rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors duration-200 shadow-lg"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
              </svg>
              Register
            </router-link>
          </div>

          <!-- User Profile Section (when authenticated) -->
          <div v-else class="pt-4 border-t border-secondary-200 dark:border-secondary-700 space-y-3">
            <div class="flex items-center px-4 py-3 bg-secondary-50 dark:bg-secondary-800 rounded-lg">
              <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mr-3">
                <span class="text-primary-600 dark:text-primary-400 font-medium">
                  {{ userInitials }}
                </span>
              </div>
              <div>
                <div class="text-sm font-medium text-secondary-700 dark:text-secondary-300">
                  {{ user?.first_name || user?.name }}
                </div>
                <div class="text-xs text-secondary-500 dark:text-secondary-500">
                  {{ user?.role === 'mechanic' ? 'Mechanic' : 'Car Owner' }}
                </div>
              </div>
            </div>
            
            <router-link 
              to="/dashboard" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
              </svg>
              Dashboard
            </router-link>
            
            <router-link 
              v-if="user?.role === 'admin'"
              to="/admin" 
              class="flex items-center px-4 py-3 rounded-lg text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors duration-200"
              @click="mobileMenuOpen = false"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              Admin Panel
            </router-link>
            
            <button 
              @click="logout" 
              class="flex items-center w-full px-4 py-3 rounded-lg text-danger-600 dark:text-danger-400 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors duration-200"
            >
              <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { authAPI } from '../services/api'

export default {
  name: 'Navbar',
  components: {
  },
  emits: ['toggle-dark-mode'],
  props: {
    isDarkMode: {
      type: Boolean,
      default: false
    }
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const mobileMenuOpen = ref(false)
    const userDropdownOpen = ref(false)
    const isAuthenticated = ref(false)
    const user = ref(null)
    const userDropdown = ref(null)

    const userInitials = computed(() => {
      if (!user.value) return 'U'
      return user.value.name ? user.value.name.charAt(0).toUpperCase() : 'U'
    })

    const toggleMobileMenu = (event) => {
      if (event) {
        event.preventDefault()
        event.stopPropagation()
      }
      console.log('Button clicked! Current state:', mobileMenuOpen.value)
      mobileMenuOpen.value = !mobileMenuOpen.value
      console.log('Mobile menu toggled to:', mobileMenuOpen.value)
    }


    const logout = async () => {
      try {
        await authAPI.logout()
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        isAuthenticated.value = false
        user.value = null
        mobileMenuOpen.value = false
        router.push('/')
      }
    }

    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative') && !event.target.closest('.mobile-menu-container')) {
        mobileMenuOpen.value = false
        userDropdownOpen.value = false
      }
    }

    const checkAuthState = () => {
      const token = localStorage.getItem('token')
      const userData = localStorage.getItem('user')
      
      if (token && userData) {
        isAuthenticated.value = true
        user.value = JSON.parse(userData)
        console.log('Navbar: User authenticated', user.value)
      } else {
        isAuthenticated.value = false
        user.value = null
        console.log('Navbar: User not authenticated')
      }
    }

    // Watch for route changes to update auth state
    watch(() => route.path, () => {
      checkAuthState()
    })

    onMounted(() => {
      checkAuthState()
      document.addEventListener('click', handleClickOutside)
      
      // Listen for storage changes (when user logs in/out in another tab)
      window.addEventListener('storage', checkAuthState)
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      window.removeEventListener('storage', checkAuthState)
    })

    return {
      mobileMenuOpen,
      userDropdownOpen,
      isAuthenticated,
      user,
      userInitials,
      userDropdown,
      toggleMobileMenu,
      logout
    }
  }
}
</script>

<style scoped>
.nav-link {
  @apply flex items-center px-3 py-2 rounded-lg text-sm font-medium text-secondary-600 dark:text-secondary-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-200;
}

.nav-link-active {
  @apply text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20;
}

.mobile-nav-link {
  @apply block px-3 py-2 rounded-lg text-base font-medium text-secondary-600 dark:text-secondary-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all duration-200;
}

.mobile-nav-link-active {
  @apply text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20;
}
</style>
