<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-50 to-secondary-50 dark:from-secondary-900 dark:to-secondary-800 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 dark:bg-primary-800 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-70 animate-blob"></div>
      <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-secondary-200 dark:bg-secondary-700 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
      <div class="absolute top-40 left-40 w-80 h-80 bg-accent-200 dark:bg-accent-800 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    <div class="relative">
      <!-- Header -->
      <div class="sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="flex justify-center">
          <div class="w-16 h-16 flex items-center justify-center">
            <img src="/icons/icon1.png" alt="CarWise.ai" class="w-16 h-16 rounded-2xl shadow-lg" />
          </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-bold text-secondary-900 dark:text-white">
          {{ t('join_carwise') }}
        </h2>
        <p class="mt-2 text-center text-lg text-secondary-600 dark:text-secondary-400">
          {{ t('create_account_start_journey') }}
        </p>
        <p class="mt-1 text-center text-sm text-secondary-500 dark:text-secondary-500">
          {{ t('have_account') }}
          <router-link to="/login" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors duration-200">
            {{ t('sign_in_here') }}
          </router-link>
        </p>
      </div>

      <!-- Registration Form -->
      <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-2xl">
        <div class="bg-white/80 dark:bg-secondary-800/80 backdrop-blur-md py-8 px-4 shadow-xl sm:rounded-2xl sm:px-10 border border-white/20 dark:border-secondary-700/20">
          <form class="space-y-6" @submit.prevent="handleRegister">
            <!-- Error Message -->
            <div v-if="errorMessage" class="bg-danger-50 dark:bg-danger-900/20 border border-danger-200 dark:border-danger-800 rounded-lg p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-danger-800 dark:text-danger-200">
                    {{ errorMessage }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-4">
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5 text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
                <div class="ml-3">
                  <p class="text-sm font-medium text-success-800 dark:text-success-200">
                    {{ successMessage }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Personal Information Section -->
            <div class="space-y-6">
              <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                  <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  Personal Information
                </h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                  <label for="first_name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    First Name *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <input
                      id="first_name"
                      name="first_name"
                      type="text"
                      autocomplete="given-name"
                      required
                      v-model="form.first_name"
                      class="input pl-10"
                      placeholder="Enter your first name"
                    />
                  </div>
                </div>

                <!-- Last Name -->
                <div>
                  <label for="last_name" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Last Name *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <input
                      id="last_name"
                      name="last_name"
                      type="text"
                      autocomplete="family-name"
                      required
                      v-model="form.last_name"
                      class="input pl-10"
                      placeholder="Enter your last name"
                    />
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Email Address *
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                    </svg>
                  </div>
                  <input
                    id="email"
                    name="email"
                    type="email"
                    autocomplete="email"
                    required
                    v-model="form.email"
                    class="input pl-10"
                    placeholder="Enter your email address"
                  />
                </div>
              </div>

              <!-- Phone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Phone Number
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                  </div>
                  <input
                    id="phone"
                    name="phone"
                    type="tel"
                    autocomplete="tel"
                    v-model="form.phone"
                    class="input pl-10"
                    placeholder="Enter your phone number"
                  />
                </div>
              </div>


              <!-- Location -->
              <div>
                <label for="location" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Location
                </label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <input
                    id="location"
                    name="location"
                    type="text"
                    v-model="form.location"
                    class="input pl-10"
                    placeholder="Enter your city or location"
                  />
                </div>
              </div>
            </div>

            <!-- Account Type Section -->
            <div class="space-y-6">
              <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                  <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  Account Type
                </h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div 
                  class="relative cursor-pointer"
                  @click="form.role = 'customer'"
                >
                  <input
                    id="role_customer"
                    name="role"
                    type="radio"
                    value="customer"
                    v-model="form.role"
                    class="sr-only"
                  />
                  <label 
                    for="role_customer" 
                    class="flex flex-col items-center p-6 border-2 rounded-xl cursor-pointer transition-all duration-200"
                    :class="form.role === 'customer' 
                      ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
                      : 'border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-600'"
                  >
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mb-3">
                      <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-secondary-900 dark:text-white">Car Owner</h4>
                    <p class="text-sm text-secondary-600 dark:text-secondary-400 text-center mt-1">
                      Get AI-powered car diagnosis and connect with trusted mechanics
                    </p>
                  </label>
                </div>

                <div 
                  class="relative cursor-pointer"
                  @click="form.role = 'mechanic'"
                >
                  <input
                    id="role_mechanic"
                    name="role"
                    type="radio"
                    value="mechanic"
                    v-model="form.role"
                    class="sr-only"
                  />
                  <label 
                    for="role_mechanic" 
                    class="flex flex-col items-center p-6 border-2 rounded-xl cursor-pointer transition-all duration-200"
                    :class="form.role === 'mechanic' 
                      ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' 
                      : 'border-secondary-200 dark:border-secondary-700 hover:border-primary-300 dark:hover:border-primary-600'"
                  >
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mb-3">
                      <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-secondary-900 dark:text-white">Certified Mechanic</h4>
                    <p class="text-sm text-secondary-600 dark:text-secondary-400 text-center mt-1">
                      Offer your expertise and grow your business with CarWise AI
                    </p>
                  </label>
                </div>
              </div>
            </div>

            <!-- Mechanic Information Section -->
            <div v-if="form.role === 'mechanic'" class="space-y-6">
              <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                  <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Professional Information
                </h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Experience Years -->
                <div>
                  <label for="experience_years" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Years of Experience *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <input
                      id="experience_years"
                      name="experience_years"
                      type="number"
                      min="0"
                      max="50"
                      required
                      v-model="form.experience_years"
                      class="input pl-10"
                      placeholder="Years of experience"
                    />
                  </div>
                </div>

                <!-- Hourly Rate -->
                <div>
                  <label for="hourly_rate" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Hourly Rate (€)
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                      </svg>
                    </div>
                    <input
                      id="hourly_rate"
                      name="hourly_rate"
                      type="number"
                      min="0"
                      step="0.01"
                      v-model="form.hourly_rate"
                      class="input pl-10"
                      placeholder="25.00"
                    />
                  </div>
                </div>
              </div>

              <!-- Areas of Expertise -->
              <div>
                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-3">
                  Areas of Expertise *
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                  <label v-for="skill in availableSkills" :key="skill" class="flex items-center p-3 border border-secondary-200 dark:border-secondary-700 rounded-lg cursor-pointer hover:bg-secondary-50 dark:hover:bg-secondary-700/50 transition-colors duration-200">
                    <input 
                      type="checkbox" 
                      :value="skill"
                      v-model="form.expertise"
                      class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-secondary-300 dark:border-secondary-600 rounded"
                    />
                    <span class="ml-3 text-sm text-secondary-700 dark:text-secondary-300">{{ skill }}</span>
                  </label>
                </div>
              </div>

              <!-- Bio -->
              <div>
                <label for="bio" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                  Professional Bio
                </label>
                <textarea
                  id="bio"
                  name="bio"
                  rows="4"
                  v-model="form.bio"
                  class="input resize-none"
                  placeholder="Tell us about your experience and specialties..."
                ></textarea>
              </div>
            </div>

            <!-- Security Section -->
            <div class="space-y-6">
              <div class="border-b border-secondary-200 dark:border-secondary-700 pb-4">
                <h3 class="text-lg font-semibold text-secondary-900 dark:text-white flex items-center">
                  <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                  Security
                </h3>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Password -->
                <div>
                  <label for="password" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Password *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                      </svg>
                    </div>
                    <input
                      id="password"
                      name="password"
                      :type="showPassword ? 'text' : 'password'"
                      autocomplete="new-password"
                      required
                      v-model="form.password"
                      class="input pl-10 pr-10"
                      placeholder="Create a strong password"
                    />
                    <button
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg v-if="showPassword" class="h-5 w-5 text-secondary-400 hover:text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                      </svg>
                      <svg v-else class="h-5 w-5 text-secondary-400 hover:text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Confirm Password -->
                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-2">
                    Confirm Password *
                  </label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <svg class="h-5 w-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </div>
                    <input
                      id="password_confirmation"
                      name="password_confirmation"
                      :type="showPassword ? 'text' : 'password'"
                      autocomplete="new-password"
                      required
                      v-model="form.password_confirmation"
                      class="input pl-10"
                      placeholder="Confirm your password"
                    />
                  </div>
                </div>
              </div>

              <!-- Password Strength Indicator -->
              <div v-if="form.password" class="space-y-2">
                <div class="text-sm text-secondary-600 dark:text-secondary-400">Password Strength:</div>
                <div class="w-full bg-secondary-200 dark:bg-secondary-700 rounded-full h-2">
                  <div 
                    class="h-2 rounded-full transition-all duration-300"
                    :class="passwordStrength.color"
                    :style="{ width: passwordStrength.width }"
                  ></div>
                </div>
                <div class="text-xs" :class="passwordStrength.textColor">{{ passwordStrength.text }}</div>
              </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="flex items-start">
              <div class="flex items-center h-5">
                <input
                  id="terms"
                  name="terms"
                  type="checkbox"
                  required
                  v-model="form.terms"
                  class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-secondary-300 dark:border-secondary-600 rounded bg-white dark:bg-secondary-700"
                />
              </div>
              <div class="ml-3 text-sm">
                <label for="terms" class="text-secondary-700 dark:text-secondary-300">
                  I agree to the 
                  <a href="#" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors duration-200">Terms of Service</a>
                  and 
                  <a href="#" class="font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 dark:hover:text-primary-300 transition-colors duration-200">Privacy Policy</a>
                </label>
              </div>
            </div>

            <!-- Register Button -->
            <div>
              <button
                type="submit"
                :disabled="isLoading || !isFormValid"
                class="btn-primary w-full flex items-center justify-center relative"
              >
                <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                {{ isLoading ? 'Creating account...' : 'Create Account' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../services/api'
import { t } from '../utils/translations'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    const errorMessage = ref('')
    const successMessage = ref('')
    const showPassword = ref(false)
    
    const availableSkills = ref([
      'Engine Repair', 'Transmission', 'Brakes', 'Electrical Systems', 
      'Diagnostics', 'Suspension', 'Exhaust Systems', 'AC/Heating',
      'Oil Changes', 'Tire Services', 'Battery Services', 'General Maintenance'
    ])
    
    const form = ref({
      // Personal Information
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      location: '',
      
      // Account Type
      role: '',
      
      // Mechanic Information
      experience_years: '',
      hourly_rate: '25.00',
      expertise: [],
      bio: '',
      
      // Security
      password: '',
      password_confirmation: '',
      
      // Terms
      terms: false
    })

    const passwordStrength = computed(() => {
      const password = form.value.password
      if (!password) return { width: '0%', text: '', color: '', textColor: '' }
      
      let score = 0
      if (password.length >= 8) score++
      if (/[a-z]/.test(password)) score++
      if (/[A-Z]/.test(password)) score++
      if (/[0-9]/.test(password)) score++
      if (/[^A-Za-z0-9]/.test(password)) score++
      
      if (score < 2) {
        return { width: '20%', text: 'Weak', color: 'bg-danger-500', textColor: 'text-danger-600 dark:text-danger-400' }
      } else if (score < 4) {
        return { width: '60%', text: 'Medium', color: 'bg-warning-500', textColor: 'text-warning-600 dark:text-warning-400' }
      } else {
        return { width: '100%', text: 'Strong', color: 'bg-success-500', textColor: 'text-success-600 dark:text-success-400' }
      }
    })

    const isFormValid = computed(() => {
      const basicValid = form.value.first_name && 
                        form.value.last_name &&
                        form.value.email && 
                        form.value.password && 
                        form.value.password_confirmation && 
                        form.value.role && 
                        form.value.terms
      
      if (form.value.role === 'mechanic') {
        return basicValid && form.value.experience_years && form.value.expertise.length > 0
      }
      
      return basicValid
    })

    const handleRegister = async () => {
      if (!isFormValid.value) return

      // Validate password match
      if (form.value.password !== form.value.password_confirmation) {
        errorMessage.value = 'Passwords do not match.'
        return
      }

      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''

      try {
        const registrationData = {
          // Basic user info
          name: `${form.value.first_name} ${form.value.last_name}`,
          first_name: form.value.first_name,
          last_name: form.value.last_name,
          email: form.value.email,
          password: form.value.password,
          password_confirmation: form.value.password_confirmation,
          phone: form.value.phone,
          role: form.value.role,
          
          // Profile information
          location: form.value.location,
          bio: form.value.bio,
          
          // Mechanic specific
          ...(form.value.role === 'mechanic' && {
            experience_years: parseInt(form.value.experience_years),
            expertise: form.value.expertise,
            hourly_rate: parseFloat(form.value.hourly_rate) || 25.00
          })
        }

        const response = await authAPI.register(registrationData)

        if (response.data.success) {
          // Store user data and token
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          
          successMessage.value = 'Account created successfully! Redirecting...'
          
          // Show success notification
          if (window.$notify) {
            window.$notify.success('Welcome to CarWise!', `Account created for ${response.data.user.first_name || response.data.user.name}`)
          }
          
          // Redirect after a short delay
          setTimeout(() => {
            router.push('/dashboard')
          }, 1500)
        }
        
      } catch (error) {
        if (error.response?.data?.message) {
          errorMessage.value = error.response.data.message
        } else if (error.response?.data?.errors) {
          // Handle validation errors
          const errors = error.response.data.errors
          const firstError = Object.values(errors)[0]
          errorMessage.value = Array.isArray(firstError) ? firstError[0] : firstError
        } else {
          errorMessage.value = 'An error occurred during registration. Please try again.'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      form,
      isLoading,
      errorMessage,
      successMessage,
      showPassword,
      availableSkills,
      passwordStrength,
      isFormValid,
      handleRegister,
      t
    }
  }
}
</script>

<style scoped>
@keyframes blob {
  0% {
    transform: translate(0px, 0px) scale(1);
  }
  33% {
    transform: translate(30px, -50px) scale(1.1);
  }
  66% {
    transform: translate(-20px, 20px) scale(0.9);
  }
  100% {
    transform: translate(0px, 0px) scale(1);
  }
}

.animate-blob {
  animation: blob 7s infinite;
}

.animation-delay-2000 {
  animation-delay: 2s;
}

.animation-delay-4000 {
  animation-delay: 4s;
}
</style>