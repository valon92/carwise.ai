<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <div class="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
      <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div class="flex items-start gap-3">
          <button
            type="button"
            class="mt-0.5 rounded-xl border border-slate-200 bg-white p-2.5 text-slate-700 hover:bg-slate-50"
            aria-label="Back to dashboard"
            @click="goBack"
          >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </button>
          <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Your account</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Edit profile</h1>
            <p class="mt-2 max-w-xl text-slate-600">
              Keep contact details current for diagnoses, PDF evidence, and your garage.
            </p>
          </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <router-link
            to="/dashboard"
            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50"
          >
            Dashboard
          </router-link>
          <router-link
            to="/my-cars"
            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50"
          >
            My Garage
          </router-link>
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Sidebar -->
        <aside class="space-y-6">
          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="text-center">
              <div class="relative mx-auto mb-4 inline-block">
                <div class="h-28 w-28 overflow-hidden rounded-2xl bg-slate-900 shadow-md">
                  <span
                    v-if="!avatarPreview && (!user?.avatar || avatarBroken)"
                    class="flex h-full w-full items-center justify-center text-3xl font-bold text-white"
                  >
                    {{ userInitials }}
                  </span>
                  <img
                    v-else
                    :src="avatarPreview || `/storage/${user.avatar}`"
                    alt="Profile"
                    class="h-full w-full object-cover"
                    @error="avatarBroken = true"
                  >
                </div>
                <button
                  type="button"
                  class="absolute -bottom-2 -right-2 flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-800 shadow-sm hover:bg-slate-50"
                  aria-label="Change avatar"
                  @click="triggerAvatarUpload"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                </button>
                <input ref="avatarInput" type="file" accept="image/*" class="hidden" @change="handleAvatarUpload">
              </div>

              <h2 class="text-lg font-semibold text-slate-900">
                {{ form.first_name || user?.first_name || user?.name || 'Your name' }}
                <span v-if="form.last_name"> {{ form.last_name }}</span>
              </h2>
              <p class="mt-1 text-sm text-slate-500">
                {{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }}
              </p>
              <button
                type="button"
                class="mt-4 rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50"
                @click="triggerAvatarUpload"
              >
                Change photo
              </button>
              <p class="mt-2 text-xs text-slate-400">JPG or PNG · max 5MB</p>
            </div>
          </section>

          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Account snapshot</p>
            <dl class="mt-4 space-y-3">
              <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                <dt class="text-sm text-slate-600">Vehicles</dt>
                <dd class="text-lg font-bold text-slate-900">{{ profileStats.carsCount }}</dd>
              </div>
              <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                <dt class="text-sm text-slate-600">Diagnoses</dt>
                <dd class="text-lg font-bold text-slate-900">{{ profileStats.diagnosesCount }}</dd>
              </div>
              <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                <dt class="text-sm text-slate-600">Member since</dt>
                <dd class="text-sm font-semibold text-slate-900">{{ formatJoinDate(user?.created_at) }}</dd>
              </div>
            </dl>
            <div class="mt-4 flex flex-col gap-2">
              <router-link to="/diagnosis-history" class="text-sm font-semibold text-slate-900 underline-offset-2 hover:underline">
                Diagnosis history →
              </router-link>
              <router-link to="/pricing" class="text-sm font-semibold text-slate-900 underline-offset-2 hover:underline">
                Pricing (pay per unlock) →
              </router-link>
            </div>
          </section>
        </aside>

        <!-- Form -->
        <div class="space-y-6 lg:col-span-2">
          <div
            v-if="successMessage"
            class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800"
          >
            {{ successMessage }}
          </div>
          <div
            v-if="errorMessage"
            class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800"
          >
            {{ errorMessage }}
          </div>

          <form class="space-y-6" @submit.prevent="handleSubmit">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
              <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Contact</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Personal information</h3>
                <p class="mt-1 text-sm text-slate-500">Used on your account and diagnosis evidence reports.</p>
              </div>

              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label for="first_name" class="mb-2 block text-sm font-medium text-slate-700">First name *</label>
                  <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                    placeholder="First name"
                  >
                </div>
                <div>
                  <label for="last_name" class="mb-2 block text-sm font-medium text-slate-700">Last name *</label>
                  <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                    placeholder="Last name"
                  >
                </div>
                <div>
                  <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email *</label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                    placeholder="you@email.com"
                  >
                </div>
                <div>
                  <label for="phone" class="mb-2 block text-sm font-medium text-slate-700">Phone</label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                    placeholder="+383 …"
                  >
                </div>
                <div class="sm:col-span-2">
                  <label for="location" class="mb-2 block text-sm font-medium text-slate-700">Location</label>
                  <input
                    id="location"
                    v-model="form.location"
                    type="text"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                    placeholder="City, country"
                  >
                </div>
              </div>
            </section>

            <section
              v-if="user?.role === 'mechanic'"
              class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8"
            >
              <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Professional</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Mechanic details</h3>
                <p class="mt-1 text-sm text-slate-500">Credentials shown with your workshop profile.</p>
              </div>
              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label for="experience_years" class="mb-2 block text-sm font-medium text-slate-700">Years of experience</label>
                  <input
                    id="experience_years"
                    v-model="form.experience_years"
                    type="number"
                    min="0"
                    max="50"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                  >
                </div>
                <div>
                  <label for="hourly_rate" class="mb-2 block text-sm font-medium text-slate-700">
                    Hourly rate ({{ user?.currency_code || 'EUR' }})
                  </label>
                  <input
                    id="hourly_rate"
                    v-model="form.hourly_rate"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                  >
                </div>
                <div class="sm:col-span-2">
                  <label for="bio" class="mb-2 block text-sm font-medium text-slate-700">Professional bio</label>
                  <textarea
                    id="bio"
                    v-model="form.bio"
                    rows="4"
                    class="w-full resize-none rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                    placeholder="Experience and specialties…"
                  />
                </div>
              </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
              <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Preferences</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Language & alerts</h3>
                <p class="mt-1 text-sm text-slate-500">
                  Diagnosis unlocks stay pay-per-use — preferences only affect how we contact you.
                </p>
              </div>
              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label for="language" class="mb-2 block text-sm font-medium text-slate-700">Language</label>
                  <select
                    id="language"
                    v-model="form.language"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                  >
                    <option value="en">English</option>
                    <option value="sq">Albanian</option>
                    <option value="de">German</option>
                    <option value="fr">French</option>
                    <option value="it">Italian</option>
                  </select>
                </div>
                <div>
                  <label for="currency" class="mb-2 block text-sm font-medium text-slate-700">Currency</label>
                  <select
                    id="currency"
                    v-model="form.currency"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                  >
                    <option value="EUR">EUR (€)</option>
                    <option value="USD">USD ($)</option>
                    <option value="GBP">GBP (£)</option>
                    <option value="ALL">ALL (L)</option>
                  </select>
                </div>
                <div class="space-y-2 sm:col-span-2">
                  <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                    <input v-model="form.email_notifications" type="checkbox" class="mt-1 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                    <span>
                      <span class="block text-sm font-medium text-slate-900">Email notifications</span>
                      <span class="block text-xs text-slate-500">Diagnosis status and account updates</span>
                    </span>
                  </label>
                  <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                    <input v-model="form.push_notifications" type="checkbox" class="mt-1 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                    <span>
                      <span class="block text-sm font-medium text-slate-900">Push notifications</span>
                      <span class="block text-xs text-slate-500">Browser alerts when a check finishes</span>
                    </span>
                  </label>
                  <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3">
                    <input v-model="form.maintenance_reminders" type="checkbox" class="mt-1 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                    <span>
                      <span class="block text-sm font-medium text-slate-900">Garage reminders</span>
                      <span class="block text-xs text-slate-500">Occasional tips tied to vehicles in My Garage</span>
                    </span>
                  </label>
                </div>
              </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
              <div class="mb-6">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Security</p>
                <h3 class="mt-1 text-xl font-semibold text-slate-900">Password</h3>
                <p class="mt-1 text-sm text-slate-500">Leave blank to keep your current password.</p>
              </div>
              <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                  <label for="new_password" class="mb-2 block text-sm font-medium text-slate-700">New password</label>
                  <div class="relative">
                    <input
                      id="new_password"
                      v-model="form.new_password"
                      :type="showNewPassword ? 'text' : 'password'"
                      class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-11 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                      placeholder="New password"
                      autocomplete="new-password"
                    >
                    <button
                      type="button"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                      @click="showNewPassword = !showNewPassword"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="showNewPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        <template v-else>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </template>
                      </svg>
                    </button>
                  </div>
                </div>
                <div>
                  <label for="new_password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm password</label>
                  <div class="relative">
                    <input
                      id="new_password_confirmation"
                      v-model="form.new_password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      class="w-full rounded-xl border border-slate-200 px-4 py-2.5 pr-11 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
                      placeholder="Confirm password"
                      autocomplete="new-password"
                    >
                    <button
                      type="button"
                      class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600"
                      @click="showConfirmPassword = !showConfirmPassword"
                    >
                      <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="showConfirmPassword" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                        <template v-else>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </template>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </section>

            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
              <button
                type="button"
                class="rounded-xl border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-800 hover:bg-slate-50"
                @click="goBack"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isLoading"
                class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
              >
                {{ isLoading ? 'Saving…' : 'Save profile' }}
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
    const { user, updateUser } = useAuth()
    const errorMessage = ref('')
    const successMessage = ref('')
    const showNewPassword = ref(false)
    const showConfirmPassword = ref(false)
    const avatarInput = ref(null)
    const avatarPreview = ref(null)
    const avatarFile = ref(null)
    const avatarBroken = ref(false)

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
      currency: 'EUR',
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
      const first = user.value.first_name
        ? user.value.first_name[0]
        : (user.value.name ? user.value.name[0] : 'U')
      const last = user.value.last_name ? user.value.last_name[0] : ''
      return (first + last).toUpperCase()
    })

    const loadUserData = async () => {
      try {
        const response = await authAPI.getUser()
        if (response.data.success) {
          user.value = response.data.user
          avatarBroken.value = false

          form.value.first_name = user.value.first_name || ''
          form.value.last_name = user.value.last_name || ''
          form.value.email = user.value.email || ''
          form.value.phone = user.value.phone || ''
          form.value.location = user.value.location || ''
          form.value.experience_years = user.value.experience_years || ''
          form.value.hourly_rate = user.value.hourly_rate || ''
          form.value.bio = user.value.bio || ''
          if (user.value.language) form.value.language = user.value.language
          if (user.value.currency) form.value.currency = user.value.currency
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
        if (form.value.new_password && form.value.new_password !== form.value.new_password_confirmation) {
          errorMessage.value = 'New passwords do not match'
          return
        }

        const formData = new FormData()
        formData.append('first_name', (form.value.first_name || '').trim())
        formData.append('last_name', (form.value.last_name || '').trim())
        formData.append('email', (form.value.email || '').trim())

        const phone = (form.value.phone || '').trim()
        const location = (form.value.location || '').trim()
        if (phone) formData.append('phone', phone)
        if (location) formData.append('location', location)

        if (form.value.new_password) {
          formData.append('password', form.value.new_password)
          formData.append('password_confirmation', form.value.new_password_confirmation)
        }

        if (user.value?.role === 'mechanic') {
          if (form.value.experience_years !== '' && form.value.experience_years != null) {
            formData.append('experience_years', String(parseInt(form.value.experience_years, 10)))
          }
          if (form.value.hourly_rate !== '' && form.value.hourly_rate != null) {
            formData.append('hourly_rate', String(parseFloat(form.value.hourly_rate)))
          }
          if ((form.value.bio || '').trim()) {
            formData.append('bio', form.value.bio.trim())
          }
        }

        if (avatarFile.value) {
          formData.append('avatar', avatarFile.value)
        }

        const response = await authAPI.updateProfile(formData)

        if (response.data.success) {
          updateUser(response.data.user)
          successMessage.value = 'Profile updated successfully!'
          setTimeout(() => {
            router.push('/dashboard')
          }, 1200)
        }
      } catch (error) {
        console.error('Error updating profile:', error)
        const errors = error.response?.data?.errors
        if (errors && typeof errors === 'object') {
          errorMessage.value = Object.values(errors).flat().join(' ')
        } else {
          errorMessage.value = error.response?.data?.message || 'Failed to update profile'
        }
      } finally {
        isLoading.value = false
      }
    }

    const goBack = () => {
      router.push('/dashboard')
    }

    const triggerAvatarUpload = () => {
      avatarInput.value?.click()
    }

    const handleAvatarUpload = (event) => {
      const file = event.target.files[0]
      if (!file) return

      if (!file.type.startsWith('image/')) {
        errorMessage.value = 'Please select a valid image file'
        return
      }

      if (file.size > 5 * 1024 * 1024) {
        errorMessage.value = 'Image size must be less than 5MB'
        return
      }

      avatarFile.value = file
      avatarBroken.value = false
      const reader = new FileReader()
      reader.onload = (e) => {
        avatarPreview.value = e.target.result
      }
      reader.readAsDataURL(file)
    }

    const loadProfileStats = async () => {
      try {
        const carsResponse = await authAPI.getUserCars()
        if (carsResponse.data.success) {
          const cars = carsResponse.data.data
          profileStats.value.carsCount = Array.isArray(cars) ? cars.length : (cars?.data?.length || 0)
        }

        const diagnosesResponse = await authAPI.getUserDiagnoses()
        if (diagnosesResponse.data.success) {
          const diagnoses = diagnosesResponse.data.data
          profileStats.value.diagnosesCount = Array.isArray(diagnoses)
            ? diagnoses.length
            : (diagnoses?.data?.length || 0)
        }
      } catch (error) {
        console.error('Error loading profile stats:', error)
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
      avatarBroken,
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
