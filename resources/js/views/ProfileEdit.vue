<template>
  <div class="min-h-screen bg-gradient-to-br from-primary-600 via-primary-700 to-secondary-600">
    <!-- Modern Header Section -->
    <div class="relative overflow-hidden">
      <!-- Animated Background -->
      <div class="absolute inset-0 bg-gradient-to-r from-primary-600 via-primary-700 to-secondary-600 opacity-90">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="absolute top-0 left-0 w-full h-full">
          <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl animate-float"></div>
          <div class="absolute top-20 right-20 w-96 h-96 bg-purple-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 2s;"></div>
          <div class="absolute bottom-10 left-1/3 w-80 h-80 bg-pink-400/20 rounded-full blur-3xl animate-float" style="animation-delay: 4s;"></div>
        </div>
      </div>
      
      <!-- Header Content -->
      <div class="relative bg-white/10 dark:bg-slate-900/10 backdrop-blur-xl border-b border-white/20 dark:border-slate-700/50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-6">
              <button @click="goBack" class="p-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-2xl border border-white/20 transition-all duration-200 group">
                <svg class="w-6 h-6 text-white group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
              </button>
              <div>
                <h1 class="text-4xl font-bold text-white drop-shadow-lg mb-2">Edit Profile</h1>
                <p class="text-white/90 text-lg">Customize your CarWise.ai experience</p>
              </div>
            </div>
            
            <div class="flex items-center space-x-4">
              <!-- Profile Preview -->
              <div class="bg-white/10 backdrop-blur-sm rounded-2xl px-4 py-3 border border-white/20">
                <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 bg-gradient-to-r from-white/20 to-white/10 rounded-xl flex items-center justify-center">
                    <span class="text-white font-bold text-lg">{{ userInitials }}</span>
                  </div>
                  <div>
                    <p class="text-white/90 text-sm font-medium">{{ user?.first_name || user?.name }}</p>
                    <p class="text-white/70 text-xs">{{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Profile Avatar & Basic Info -->
        <div class="lg:col-span-1 space-y-8">
          <!-- Profile Avatar Section -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
            <div class="text-center">
              <div class="relative inline-block mb-6">
                <div class="w-32 h-32 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-3xl flex items-center justify-center shadow-2xl border-4 border-white/20">
                  <span v-if="!avatarPreview && !user?.avatar" class="text-white font-bold text-4xl">{{ userInitials }}</span>
                  <img v-else :src="avatarPreview || (user?.avatar ? `/storage/${user.avatar}` : null)" alt="Profile" class="w-full h-full object-cover rounded-2xl">
                </div>
                <button @click="triggerAvatarUpload" class="absolute -bottom-2 -right-2 w-10 h-10 bg-white dark:bg-slate-700 rounded-full shadow-lg border-2 border-white/20 flex items-center justify-center hover:scale-110 transition-transform duration-200">
                  <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </button>
                <input ref="avatarInput" type="file" accept="image/*" @change="handleAvatarUpload" class="hidden">
              </div>
              <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ user?.first_name || user?.name }}</h3>
              <p class="text-slate-600 dark:text-slate-400 mb-4">{{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }}</p>
              <button @click="triggerAvatarUpload" class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-2xl font-semibold transition-all duration-200 flex items-center gap-2 mx-auto shadow-lg hover:shadow-xl transform hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Change Avatar
              </button>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-6">Profile Stats</h3>
            <div class="space-y-4">
              <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-2xl">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Cars Registered</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ profileStats.carsCount }}</p>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Diagnoses</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ profileStats.diagnosesCount }}</p>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-2xl">
                <div class="flex items-center space-x-3">
                  <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-white">Member Since</p>
                    <p class="text-lg font-bold text-purple-600 dark:text-purple-400">{{ formatJoinDate(user?.created_at) }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column - Form Sections -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Success/Error Messages -->
          <div v-if="successMessage" class="p-6 bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 border border-emerald-200 dark:border-emerald-700 rounded-2xl">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-emerald-800 dark:text-emerald-200 font-medium">{{ successMessage }}</p>
            </div>
          </div>

          <div v-if="errorMessage" class="p-6 bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-700 rounded-2xl">
            <div class="flex items-center">
              <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <p class="text-red-800 dark:text-red-200 font-medium">{{ errorMessage }}</p>
            </div>
          </div>

          <form @submit.prevent="handleSubmit" class="space-y-8">
            <!-- Personal Information -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
              <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Personal Information</h3>
                  <p class="text-slate-600 dark:text-slate-400">Update your basic profile details</p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- First Name -->
                <div>
                  <label for="first_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    First Name *
                  </label>
                  <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Enter your first name"
                  />
                </div>

                <!-- Last Name -->
                <div>
                  <label for="last_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Last Name *
                  </label>
                  <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Enter your last name"
                  />
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Email Address *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Enter your email"
                  />
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Phone Number
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Enter your phone number"
                  />
                </div>

                <!-- Location -->
                <div class="sm:col-span-2">
                  <label for="location" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Location
                  </label>
                  <input
                    id="location"
                    v-model="form.location"
                    type="text"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Enter your location (city, country)"
                  />
                </div>
              </div>
            </div>

            <!-- Mechanic Information (if user is mechanic) -->
            <div v-if="user?.role === 'mechanic'" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
              <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Professional Information</h3>
                  <p class="text-slate-600 dark:text-slate-400">Your mechanic credentials and experience</p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Experience Years -->
                <div>
                  <label for="experience_years" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Years of Experience
                  </label>
                  <input
                    id="experience_years"
                    v-model="form.experience_years"
                    type="number"
                    min="0"
                    max="50"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Years of experience"
                  />
                </div>

                <!-- Hourly Rate -->
                <div>
                  <label for="hourly_rate" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Hourly Rate ({{ user?.currency_code || 'USD' }})
                  </label>
                  <input
                    id="hourly_rate"
                    v-model="form.hourly_rate"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 shadow-sm"
                    placeholder="Hourly rate"
                  />
                </div>

                <!-- Bio -->
                <div class="sm:col-span-2">
                  <label for="bio" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Professional Bio
                  </label>
                  <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="4"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 shadow-sm resize-none"
                    placeholder="Tell us about your experience and expertise..."
                  ></textarea>
                </div>
              </div>
            </div>

            <!-- User Preferences Section -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
              <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Preferences</h3>
                  <p class="text-slate-600 dark:text-slate-400">Customize your CarWise.ai experience</p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Language -->
                <div>
                  <label for="language" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Language
                  </label>
                  <select
                    id="language"
                    v-model="form.language"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 shadow-sm"
                  >
                    <option value="en">English</option>
                    <option value="sq">Albanian</option>
                    <option value="de">German</option>
                    <option value="fr">French</option>
                    <option value="it">Italian</option>
                  </select>
                </div>

                <!-- Currency -->
                <div>
                  <label for="currency" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Currency
                  </label>
                  <select
                    id="currency"
                    v-model="form.currency"
                    class="w-full px-4 py-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 shadow-sm"
                  >
                    <option value="USD">USD ($)</option>
                    <option value="EUR">EUR (€)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="ALL">ALL (L)</option>
                  </select>
                </div>

                <!-- Notifications -->
                <div class="sm:col-span-2">
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Notification Preferences
                  </label>
                  <div class="space-y-3">
                    <label class="flex items-center space-x-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200 cursor-pointer">
                      <input
                        v-model="form.email_notifications"
                        type="checkbox"
                        class="w-5 h-5 text-purple-600 bg-white border-slate-300 rounded focus:ring-purple-500 focus:ring-2"
                      />
                      <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Email Notifications</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Receive updates via email</p>
                      </div>
                    </label>
                    
                    <label class="flex items-center space-x-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200 cursor-pointer">
                      <input
                        v-model="form.push_notifications"
                        type="checkbox"
                        class="w-5 h-5 text-purple-600 bg-white border-slate-300 rounded focus:ring-purple-500 focus:ring-2"
                      />
                      <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Push Notifications</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Receive browser notifications</p>
                      </div>
                    </label>
                    
                    <label class="flex items-center space-x-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors duration-200 cursor-pointer">
                      <input
                        v-model="form.maintenance_reminders"
                        type="checkbox"
                        class="w-5 h-5 text-purple-600 bg-white border-slate-300 rounded focus:ring-purple-500 focus:ring-2"
                      />
                      <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Maintenance Reminders</p>
                        <p class="text-xs text-slate-600 dark:text-slate-400">Get notified about car maintenance</p>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Password Change -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-3xl p-8 border border-white/20 dark:border-slate-700/50 shadow-2xl hover:shadow-3xl transition-all duration-300">
              <div class="flex items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center mr-4 shadow-lg">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                  </svg>
                </div>
                <div>
                  <h3 class="text-2xl font-bold text-slate-900 dark:text-white">Security Settings</h3>
                  <p class="text-slate-600 dark:text-slate-400">Update your password and security preferences</p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- New Password -->
                <div>
                  <label for="new_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    New Password
                  </label>
                  <div class="relative">
                    <input
                      id="new_password"
                      v-model="form.new_password"
                      :type="showNewPassword ? 'text' : 'password'"
                      class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 shadow-sm"
                      placeholder="Enter new password"
                    />
                    <button
                      type="button"
                      @click="showNewPassword = !showNewPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg v-if="showNewPassword" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                      </svg>
                      <svg v-else class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                  <label for="new_password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-3">
                    Confirm New Password
                  </label>
                  <div class="relative">
                    <input
                      id="new_password_confirmation"
                      v-model="form.new_password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      class="w-full px-4 py-3 pr-12 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all duration-200 shadow-sm"
                      placeholder="Confirm new password"
                    />
                    <button
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute inset-y-0 right-0 pr-3 flex items-center"
                    >
                      <svg v-if="showConfirmPassword" class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                      </svg>
                      <svg v-else class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-8">
              <button
                type="button"
                @click="goBack"
                class="px-8 py-4 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-2xl font-semibold transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isLoading"
                class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-2xl font-semibold transition-all duration-200 flex items-center gap-2 shadow-lg hover:shadow-xl transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
              >
                <svg v-if="isLoading" class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ isLoading ? 'Updating...' : 'Update Profile' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { authAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'

export default {
  name: 'ProfileEdit',
  setup() {
    const router = useRouter()
    const isLoading = ref(false)
    
    // Use the global auth state
    const { user, isAuthenticated, checkAuth, updateUser } = useAuth()
    const errorMessage = ref('')
    const successMessage = ref('')
    const showNewPassword = ref(false)
    const showConfirmPassword = ref(false)
    const avatarInput = ref(null)
    const avatarPreview = ref(null)
    const avatarFile = ref(null)

    const form = ref({
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      location: '',
      experience_years: '',
      hourly_rate: '',
      bio: '',
      new_password: '',
      new_password_confirmation: '',
      language: 'en',
      currency: 'USD',
      email_notifications: true,
      push_notifications: true,
      maintenance_reminders: true
    })

    const profileStats = ref({
      carsCount: 0,
      diagnosesCount: 0
    })

    const userInitials = computed(() => {
      if (!user.value) return 'U'
      const first = user.value.first_name ? user.value.first_name[0] : (user.value.name ? user.value.name[0] : 'U')
      const last = user.value.last_name ? user.value.last_name[0] : ''
      return (first + last).toUpperCase()
    })

    const loadUserData = async () => {
      try {
        const response = await authAPI.getUser()
        if (response.data.success) {
          user.value = response.data.user
          
          // Populate form with current user data
          form.value.first_name = user.value.first_name || ''
          form.value.last_name = user.value.last_name || ''
          form.value.email = user.value.email || ''
          form.value.phone = user.value.phone || ''
          form.value.location = user.value.location || ''
          form.value.experience_years = user.value.experience_years || ''
          form.value.hourly_rate = user.value.hourly_rate || ''
          form.value.bio = user.value.bio || ''
        }
      } catch (error) {
        console.error('Error loading user data:', error)
        errorMessage.value = 'Failed to load user data'
      }
    }

    const handleSubmit = async () => {
      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''

      try {
        // Validate password if provided
        if (form.value.new_password && form.value.new_password !== form.value.new_password_confirmation) {
          errorMessage.value = 'New passwords do not match'
          return
        }

        // Create FormData for file upload
        const formData = new FormData()
        formData.append('first_name', form.value.first_name)
        formData.append('last_name', form.value.last_name)
        formData.append('email', form.value.email)
        formData.append('phone', form.value.phone)
        formData.append('location', form.value.location)

        // Add password if provided
        if (form.value.new_password) {
          formData.append('password', form.value.new_password)
          formData.append('password_confirmation', form.value.new_password_confirmation)
        }

        // Add mechanic-specific fields if user is mechanic
        if (user.value?.role === 'mechanic') {
          formData.append('experience_years', form.value.experience_years ? parseInt(form.value.experience_years) : '')
          formData.append('hourly_rate', form.value.hourly_rate ? parseFloat(form.value.hourly_rate) : '')
          formData.append('bio', form.value.bio)
        }

        // Add avatar if selected
        if (avatarFile.value) {
          formData.append('avatar', avatarFile.value)
        }

        const response = await authAPI.updateProfile(formData)

        if (response.data.success) {
          // Update user in global state
          updateUser(response.data.user)
          
          successMessage.value = 'Profile updated successfully!'
          
          // Redirect to dashboard after a short delay
          setTimeout(() => {
            router.push('/dashboard')
          }, 1500)
        }
      } catch (error) {
        console.error('Error updating profile:', error)
        errorMessage.value = error.response?.data?.message || 'Failed to update profile'
      } finally {
        isLoading.value = false
      }
    }

    const goBack = () => {
      router.push('/dashboard')
    }

    // Avatar upload functions
    const triggerAvatarUpload = () => {
      avatarInput.value?.click()
    }

    const handleAvatarUpload = (event) => {
      const file = event.target.files[0]
      if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
          errorMessage.value = 'Please select a valid image file'
          return
        }

        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
          errorMessage.value = 'Image size must be less than 5MB'
          return
        }

        // Store the file
        avatarFile.value = file

        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
          avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)

        // Here you would typically upload the file to your server
        console.log('Avatar file selected:', file)
      }
    }

    // Profile stats functions
    const loadProfileStats = async () => {
      try {
        // Load cars count
        const carsResponse = await authAPI.getUserCars()
        if (carsResponse.data.success) {
          profileStats.value.carsCount = carsResponse.data.data.length
        }

        // Load diagnoses count
        const diagnosesResponse = await authAPI.getUserDiagnoses()
        if (diagnosesResponse.data.success) {
          profileStats.value.diagnosesCount = diagnosesResponse.data.data.length
        }
      } catch (error) {
        console.error('Error loading profile stats:', error)
        // Set default values
        profileStats.value.carsCount = 0
        profileStats.value.diagnosesCount = 0
      }
    }

    const formatJoinDate = (dateString) => {
      if (!dateString) return 'Unknown'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long' 
      })
    }

    onMounted(() => {
      // Check if user is logged in
      const token = localStorage.getItem('token')
      const storedUser = localStorage.getItem('user')
      
      if (token && storedUser) {
        user.value = JSON.parse(storedUser)
        loadUserData()
        loadProfileStats()
      } else {
        router.push('/login')
      }
    })

    return {
      user,
      userInitials,
      form,
      isLoading,
      errorMessage,
      successMessage,
      showNewPassword,
      showConfirmPassword,
      avatarInput,
      avatarPreview,
      avatarFile,
      profileStats,
      handleSubmit,
      goBack,
      triggerAvatarUpload,
      handleAvatarUpload,
      formatJoinDate
    }
  }
}
</script>

<style scoped>
@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

.animate-float {
  animation: float 6s ease-in-out infinite;
}

.glass {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.gradient-text {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hover-lift {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: rgba(0, 0, 0, 0.1);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: rgba(0, 0, 0, 0.3);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(0, 0, 0, 0.5);
}
</style>
