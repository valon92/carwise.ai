<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
      <div class="absolute -top-24 -right-16 h-72 w-72 rounded-full bg-slate-200/60 blur-3xl" />
      <div class="absolute bottom-0 left-10 h-56 w-56 rounded-full bg-slate-300/40 blur-3xl" />
    </div>

    <div class="relative mx-auto grid min-h-screen max-w-6xl lg:grid-cols-2">
      <!-- Brand / platform logic -->
      <aside class="flex flex-col justify-between px-6 py-10 sm:px-10 lg:px-12 lg:py-16">
        <div>
          <router-link to="/" class="inline-flex items-center gap-3">
            <img src="/icons/icon1.png" alt="" class="h-11 w-11 rounded-2xl shadow-md" />
            <span class="text-2xl font-bold tracking-tight text-slate-900">
              CarWise<span class="text-slate-500">.ai</span>
            </span>
          </router-link>

          <h1 class="mt-10 max-w-md text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
            Your garage, diagnoses, and PDF evidence — in one account
          </h1>
          <p class="mt-4 max-w-md text-base leading-relaxed text-slate-600 sm:text-lg">
            Describe the symptom, get an AI preview, unlock the full result only when it’s useful,
            and keep a dated report with your vehicles.
          </p>

          <ol class="mt-10 max-w-md space-y-5">
            <li
              v-for="(step, index) in journey"
              :key="step.title"
              class="flex gap-4"
            >
              <span
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-900 text-sm font-bold text-white"
              >
                {{ index + 1 }}
              </span>
              <div>
                <p class="font-semibold text-slate-900">{{ step.title }}</p>
                <p class="mt-0.5 text-sm leading-relaxed text-slate-600">{{ step.detail }}</p>
              </div>
            </li>
          </ol>
        </div>

        <p class="mt-12 hidden text-sm text-slate-500 lg:block">
          Preview is free. You pay per unlock — no subscription required.
        </p>
      </aside>

      <!-- Form -->
      <main class="flex items-center px-4 py-10 sm:px-8 lg:px-10 lg:py-16">
        <div class="w-full max-w-md mx-auto lg:mx-0 lg:ml-auto">
          <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">
              Create account
            </p>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">
              Join as a car owner
            </h2>
            <p class="mt-2 text-sm text-slate-600">
              Already registered?
              <router-link
                :to="{ path: '/login', query: $route.query }"
                class="font-semibold text-slate-900 underline-offset-2 hover:underline"
              >
                Sign in
              </router-link>
            </p>

            <form class="mt-8 space-y-5" @submit.prevent="handleRegister">
              <div
                v-if="errorMessage"
                class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                role="alert"
              >
                {{ errorMessage }}
              </div>

              <div
                v-if="successMessage"
                class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                role="status"
              >
                {{ successMessage }}
              </div>

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                  <label for="first_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                    First name
                  </label>
                  <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    autocomplete="given-name"
                    required
                    class="field"
                    placeholder="Arben"
                  >
                </div>
                <div>
                  <label for="last_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                    Last name
                  </label>
                  <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    autocomplete="family-name"
                    required
                    class="field"
                    placeholder="Krasniqi"
                  >
                </div>
              </div>

              <div>
                <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">
                  Email
                </label>
                <input
                  id="email"
                  v-model="form.email"
                  type="email"
                  autocomplete="email"
                  required
                  class="field"
                  placeholder="you@email.com"
                >
              </div>

              <div>
                <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">
                  Phone <span class="font-normal text-slate-400">(optional)</span>
                </label>
                <input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  autocomplete="tel"
                  class="field"
                  placeholder="+383 …"
                >
              </div>

              <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">
                  Password
                </label>
                <div class="relative">
                  <input
                    id="password"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    autocomplete="new-password"
                    required
                    minlength="8"
                    class="field pr-11"
                    placeholder="At least 8 characters"
                  >
                  <button
                    type="button"
                    class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400 hover:text-slate-700"
                    :aria-label="showPassword ? 'Hide password' : 'Show password'"
                    @click="showPassword = !showPassword"
                  >
                    <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </button>
                </div>
                <div v-if="form.password" class="mt-2">
                  <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <div
                      class="h-full rounded-full transition-all duration-300"
                      :class="passwordStrength.bar"
                      :style="{ width: passwordStrength.width }"
                    />
                  </div>
                  <p class="mt-1 text-xs" :class="passwordStrength.textClass">
                    {{ passwordStrength.label }}
                  </p>
                </div>
              </div>

              <div>
                <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">
                  Confirm password
                </label>
                <input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  :type="showPassword ? 'text' : 'password'"
                  autocomplete="new-password"
                  required
                  minlength="8"
                  class="field"
                  placeholder="Repeat password"
                >
              </div>

              <label class="flex items-start gap-3 text-sm text-slate-600">
                <input
                  v-model="form.terms"
                  type="checkbox"
                  required
                  class="mt-0.5 h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900"
                >
                <span>
                  I agree to the
                  <router-link to="/terms-of-service" class="font-semibold text-slate-900 underline-offset-2 hover:underline">
                    Terms
                  </router-link>
                  and
                  <router-link to="/privacy-policy" class="font-semibold text-slate-900 underline-offset-2 hover:underline">
                    Privacy Policy
                  </router-link>
                </span>
              </label>

              <button
                type="submit"
                :disabled="isLoading || !isFormValid"
                class="flex w-full items-center justify-center rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
              >
                <svg
                  v-if="isLoading"
                  class="mr-2 h-5 w-5 animate-spin"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  aria-hidden="true"
                >
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                </svg>
                {{ isLoading ? 'Creating account…' : 'Create account & go to dashboard' }}
              </button>

              <p class="text-center text-xs text-slate-500">
                After signup you can start a diagnosis, save cars to your garage, and unlock PDF reports.
              </p>
            </form>
          </div>

          <p class="mt-6 text-center text-sm text-slate-500 lg:hidden">
            Preview free · pay per unlock · no plan required
          </p>
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { authAPI } from '../services/api'

export default {
  name: 'Register',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const isLoading = ref(false)
    const errorMessage = ref('')
    const successMessage = ref('')
    const showPassword = ref(false)

    const journey = [
      {
        title: 'Diagnose first',
        detail: 'Describe symptoms in text or voice. Get an AI preview before you pay.',
      },
      {
        title: 'Unlock when useful',
        detail: 'Pay only for the full result — clear findings and a dated PDF.',
      },
      {
        title: 'Keep it with your cars',
        detail: 'Garage and diagnosis history stay on your account for next time.',
      },
    ]

    const form = ref({
      first_name: '',
      last_name: '',
      email: '',
      phone: '',
      password: '',
      password_confirmation: '',
      terms: false,
    })

    const passwordStrength = computed(() => {
      const password = form.value.password
      if (!password) {
        return { width: '0%', label: '', bar: '', textClass: '' }
      }

      let score = 0
      if (password.length >= 8) score++
      if (/[a-z]/.test(password)) score++
      if (/[A-Z]/.test(password)) score++
      if (/[0-9]/.test(password)) score++
      if (/[^A-Za-z0-9]/.test(password)) score++

      if (score < 2) {
        return {
          width: '25%',
          label: 'Weak — add length and variety',
          bar: 'bg-red-500',
          textClass: 'text-red-600',
        }
      }
      if (score < 4) {
        return {
          width: '60%',
          label: 'Okay — stronger is better',
          bar: 'bg-amber-500',
          textClass: 'text-amber-700',
        }
      }
      return {
        width: '100%',
        label: 'Strong password',
        bar: 'bg-emerald-500',
        textClass: 'text-emerald-700',
      }
    })

    const isFormValid = computed(() => {
      return (
        form.value.first_name.trim() &&
        form.value.last_name.trim() &&
        form.value.email.trim() &&
        form.value.password.length >= 8 &&
        form.value.password_confirmation &&
        form.value.terms
      )
    })

    const handleRegister = async () => {
      if (!isFormValid.value) return

      if (form.value.password !== form.value.password_confirmation) {
        errorMessage.value = 'Passwords do not match.'
        return
      }

      isLoading.value = true
      errorMessage.value = ''
      successMessage.value = ''

      try {
        const registrationData = {
          name: `${form.value.first_name} ${form.value.last_name}`.trim(),
          first_name: form.value.first_name.trim(),
          last_name: form.value.last_name.trim(),
          email: form.value.email.trim(),
          password: form.value.password,
          password_confirmation: form.value.password_confirmation,
          role: 'customer',
        }

        const phone = (form.value.phone || '').trim()
        if (phone) registrationData.phone = phone

        const response = await authAPI.register(registrationData)

        if (response.data.success) {
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('user', JSON.stringify(response.data.user))

          successMessage.value = 'Account ready — opening your dashboard…'

          if (window.$notify) {
            window.$notify.success(
              'Welcome to CarWise',
              `Hi ${response.data.user.first_name || response.data.user.name} — start with a diagnosis anytime.`
            )
          }

          setTimeout(() => {
            const redirect = route.query.redirect
            const nextPath =
              typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')
                ? redirect
                : '/dashboard'
            router.push(nextPath)
          }, 900)
        }
      } catch (error) {
        const errors = error.response?.data?.errors
        if (errors && typeof errors === 'object') {
          errorMessage.value = Object.values(errors).flat().join(' ')
        } else {
          errorMessage.value =
            error.response?.data?.message || 'Registration failed. Please try again.'
        }

        if (String(errorMessage.value).toLowerCase().includes('already been taken')) {
          errorMessage.value =
            'This email is already registered. Sign in instead, or use a different email.'
        }
      } finally {
        isLoading.value = false
      }
    }

    return {
      form,
      journey,
      isLoading,
      errorMessage,
      successMessage,
      showPassword,
      passwordStrength,
      isFormValid,
      handleRegister,
    }
  },
}
</script>

<style scoped>
.field {
  @apply w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 shadow-sm outline-none transition focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10;
}
</style>
