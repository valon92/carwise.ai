<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <!-- Header -->
    <div class="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
      <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
        <div class="flex items-start gap-4">
          <div class="relative hidden h-14 w-14 shrink-0 overflow-hidden rounded-2xl bg-slate-900 shadow-md sm:block">
            <span
              v-if="!user?.avatar || avatarBroken"
              class="flex h-full w-full items-center justify-center text-lg font-bold text-white"
            >
              {{ userInitials }}
            </span>
            <img
              v-else
              :src="`/storage/${user.avatar}`"
              :alt="user?.first_name || user?.name"
              class="h-full w-full object-cover"
              @error="avatarBroken = true"
            >
          </div>
          <div class="min-w-0 flex-1">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Your account</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
              Welcome back, {{ user?.first_name || user?.name || 'there' }}
            </h1>
            <p class="mt-1 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
              Diagnose → unlock when useful → keep a dated PDF. Garage and history stay with your account.
            </p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <router-link
            to="/diagnose"
            class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800"
          >
            New diagnosis
          </router-link>
          <router-link
            to="/my-cars"
            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50"
          >
            My Garage
          </router-link>
          <router-link
            to="/diagnosis-history"
            class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50"
          >
            History
          </router-link>
          <button
            type="button"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            @click="handleEditProfile"
          >
            Profile
          </button>
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <!-- Stats -->
      <div class="mb-8 grid grid-cols-2 gap-3 rounded-2xl border border-slate-200 bg-white p-4 lg:grid-cols-4">
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Diagnoses</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ stats.totalDiagnoses }}</p>
          <p class="mt-0.5 text-xs text-slate-500">+{{ stats.diagnosesThisMonth }} this month</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">PDF ready</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ unlockedCount }}</p>
          <p class="mt-0.5 text-xs text-slate-500">Unlocked evidence reports</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Vehicles</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ stats.carsRegistered }}</p>
          <p class="mt-0.5 text-xs text-slate-500">{{ stats.activeCars }} active in garage</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">How you pay</p>
          <p class="mt-1 text-lg font-bold text-slate-900">Per unlock</p>
          <p class="mt-0.5 text-xs text-slate-500">Preview free · no plan required</p>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <div class="space-y-8 lg:col-span-2">
          <!-- Quick actions -->
          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5">
              <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Start here</p>
              <h2 class="mt-1 text-xl font-semibold text-slate-900">What do you need?</h2>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
              <router-link
                to="/diagnose"
                class="group rounded-xl border border-slate-200 bg-slate-50/80 p-5 transition hover:border-slate-300 hover:bg-white"
              >
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                  </svg>
                </div>
                <h3 class="font-semibold text-slate-900">AI Diagnosis</h3>
                <p class="mt-1 text-sm text-slate-500">Describe symptoms, get a preview, unlock full result + PDF.</p>
              </router-link>

              <router-link
                to="/my-cars"
                class="group rounded-xl border border-slate-200 bg-slate-50/80 p-5 transition hover:border-slate-300 hover:bg-white"
              >
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                </div>
                <h3 class="font-semibold text-slate-900">My Garage</h3>
                <p class="mt-1 text-sm text-slate-500">Keep vehicles ready for the next check.</p>
              </router-link>

              <router-link
                to="/diagnosis-history"
                class="group rounded-xl border border-slate-200 bg-slate-50/80 p-5 transition hover:border-slate-300 hover:bg-white"
              >
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 class="font-semibold text-slate-900">Diagnosis history</h3>
                <p class="mt-1 text-sm text-slate-500">Open results and download dated PDF evidence.</p>
              </router-link>

              <router-link
                to="/vehicle-twin"
                class="group rounded-xl border border-slate-200 bg-slate-50/80 p-5 transition hover:border-slate-300 hover:bg-white"
              >
                <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white">
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                  </svg>
                </div>
                <h3 class="font-semibold text-slate-900">Vehicle Twin</h3>
                <p class="mt-1 text-sm text-slate-500">VIN-backed profile for deeper context.</p>
              </router-link>
            </div>
          </section>

          <!-- Recent diagnoses -->
          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
              <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Evidence</p>
                <h2 class="mt-1 text-xl font-semibold text-slate-900">Recent diagnoses</h2>
              </div>
              <div class="flex items-center gap-3">
                <router-link to="/diagnose" class="text-sm font-semibold text-slate-700 underline-offset-2 hover:underline">
                  New diagnosis
                </router-link>
                <router-link
                  to="/diagnosis-history"
                  class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
                >
                  Full history
                </router-link>
              </div>
            </div>

            <div v-if="historyLoading" class="py-10 text-center text-sm text-slate-500">
              Loading diagnoses…
            </div>

            <div v-else-if="recentDiagnoses.length === 0" class="rounded-xl bg-slate-50 px-4 py-12 text-center">
              <h3 class="text-sm font-medium text-slate-900">No diagnoses yet</h3>
              <p class="mt-1 text-sm text-slate-500">
                Run an AI diagnosis to build downloadable evidence reports here.
              </p>
              <router-link
                to="/diagnose"
                class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800"
              >
                Start Diagnosis
              </router-link>
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="diagnosis in recentDiagnoses.slice(0, 5)"
                :key="diagnosis.session_id || diagnosis.id"
                class="flex flex-col gap-3 rounded-xl border border-slate-100 bg-slate-50/80 p-4 sm:flex-row sm:items-center sm:justify-between"
              >
                <div class="min-w-0 flex-1">
                  <div class="flex flex-wrap items-center gap-2">
                    <h4 class="text-sm font-semibold text-slate-900">
                      {{ diagnosis.make || diagnosis.vehicle_info?.make }}
                      {{ diagnosis.model || diagnosis.vehicle_info?.model }}
                      <span
                        v-if="diagnosis.year || diagnosis.vehicle_info?.year"
                        class="font-normal text-slate-500"
                      >
                        ({{ diagnosis.year || diagnosis.vehicle_info?.year }})
                      </span>
                    </h4>
                    <span
                      class="rounded-full px-2 py-0.5 text-[11px] font-medium"
                      :class="getDiagnosisStatusColor(diagnosis.status)"
                    >
                      {{ diagnosis.status }}
                    </span>
                    <span
                      class="rounded-full px-2 py-0.5 text-[11px] font-medium"
                      :class="(diagnosis.can_download_pdf || diagnosis.unlocked)
                        ? 'bg-emerald-50 text-emerald-800'
                        : 'bg-amber-50 text-amber-800'"
                    >
                      {{ (diagnosis.can_download_pdf || diagnosis.unlocked) ? 'PDF ready' : 'Preview' }}
                    </span>
                  </div>
                  <p class="mt-1 truncate text-sm text-slate-600">
                    {{ diagnosis.result?.problem_title || diagnosis.description || 'Diagnosis' }}
                  </p>
                  <p class="mt-1 text-xs text-slate-400">{{ formatDate(diagnosis.created_at) }}</p>
                </div>
                <div class="flex shrink-0 flex-wrap gap-2">
                  <router-link
                    to="/diagnosis-history"
                    class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 hover:bg-slate-50"
                  >
                    Open
                  </router-link>
                  <button
                    v-if="diagnosis.can_download_pdf || diagnosis.unlocked"
                    type="button"
                    :disabled="downloadingId === (diagnosis.session_id || diagnosis.id)"
                    class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white hover:bg-slate-800 disabled:opacity-50"
                    @click="downloadDiagnosisPdf(diagnosis)"
                  >
                    {{ downloadingId === (diagnosis.session_id || diagnosis.id) ? 'Preparing…' : 'Download PDF' }}
                  </button>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Profile</p>
            <div class="mt-4 flex items-center gap-3">
              <div class="h-12 w-12 shrink-0 overflow-hidden rounded-xl bg-slate-900">
                <span
                  v-if="!user?.avatar || avatarBroken"
                  class="flex h-full w-full items-center justify-center text-sm font-bold text-white"
                >
                  {{ userInitials }}
                </span>
                <img
                  v-else
                  :src="`/storage/${user.avatar}`"
                  :alt="user?.first_name || user?.name"
                  class="h-full w-full object-cover"
                  @error="avatarBroken = true"
                >
              </div>
              <div class="min-w-0">
                <h3 class="truncate font-semibold text-slate-900">
                  {{ user?.first_name || user?.full_name || user?.name }}
                </h3>
                <p class="truncate text-sm text-slate-500">{{ user?.email }}</p>
              </div>
            </div>

            <dl class="mt-5 space-y-2 text-sm">
              <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Role</dt>
                <dd class="font-medium text-slate-900">
                  {{ user?.role === 'mechanic' ? 'Certified Mechanic' : 'Car Owner' }}
                </dd>
              </div>
              <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Location</dt>
                <dd class="truncate font-medium text-slate-900">{{ user?.location || 'Not set' }}</dd>
              </div>
              <div class="flex justify-between gap-3">
                <dt class="text-slate-500">Member since</dt>
                <dd class="font-medium text-slate-900">{{ formatDate(user?.created_at) }}</dd>
              </div>
            </dl>

            <button
              type="button"
              class="mt-5 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50"
              @click="handleEditProfile"
            >
              Edit profile
            </button>
          </section>

          <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Pricing logic</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">Preview free, pay to unlock</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-600">
              <li class="flex gap-2">
                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-slate-900"></span>
                Free preview of the likely problem
              </li>
              <li class="flex gap-2">
                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-slate-900"></span>
                Unlock once for steps, costs, AI visual & PDF
              </li>
              <li class="flex gap-2">
                <span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-slate-900"></span>
                No monthly subscription required
              </li>
            </ul>
            <router-link
              to="/pricing"
              class="mt-5 inline-flex text-sm font-semibold text-slate-900 underline-offset-2 hover:underline"
            >
              See pricing →
            </router-link>
          </section>

          <section class="rounded-2xl border border-slate-200 bg-slate-900 p-6 text-white shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Next step</p>
            <h2 class="mt-1 text-lg font-semibold">Something wrong with the car?</h2>
            <p class="mt-2 text-sm leading-relaxed text-slate-300">
              Start with a free AI preview. Unlock only if the full diagnosis is useful — then download the dated report.
            </p>
            <router-link
              to="/diagnose"
              class="mt-5 inline-flex rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-slate-900 hover:bg-slate-100"
            >
              Start AI Diagnosis
            </router-link>
          </section>

          <section v-if="stats.mostCommonIssues.length" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Your patterns</p>
            <h2 class="mt-1 text-lg font-semibold text-slate-900">Common issues</h2>
            <div class="mt-4 space-y-3">
              <div
                v-for="(issue, index) in stats.mostCommonIssues.slice(0, 5)"
                :key="index"
                class="flex items-center justify-between gap-3"
              >
                <span class="truncate text-sm text-slate-700">{{ issue.name }}</span>
                <span class="shrink-0 text-xs font-medium text-slate-500">{{ issue.count }}</span>
              </div>
            </div>
          </section>
        </div>
      </div>
    </div>

    <LiveChatWidget :is-cart-open="false" />
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api, { diagnosisAPI, dashboardAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'
import LiveChatWidget from '../components/LiveChatWidget.vue'

export default {
  name: 'Dashboard',
  components: {
    LiveChatWidget
  },
  setup() {
    const router = useRouter()
    const isLoading = ref(true)
    const avatarBroken = ref(false)
    const { user, isAuthenticated, checkAuth } = useAuth()

    const userInitials = computed(() => {
      if (!user.value) return 'U'
      const first = user.value.first_name
        ? user.value.first_name[0]
        : (user.value.name ? user.value.name[0] : 'U')
      const last = user.value.last_name ? user.value.last_name[0] : ''
      return (first + last).toUpperCase()
    })

    const stats = ref({
      totalDiagnoses: 0,
      diagnosesThisMonth: 0,
      diagnosesThisWeek: 0,
      carsRegistered: 0,
      activeCars: 0,
      successRate: 0,
      averageResponseTime: 0,
      mostCommonIssues: [],
      monthlyDiagnoses: []
    })

    const recentDiagnoses = ref([])
    const historyLoading = ref(false)
    const downloadingId = ref(null)

    const unlockedCount = computed(() =>
      recentDiagnoses.value.filter(d => d.can_download_pdf || d.unlocked).length
    )

    const loadDiagnosisHistory = async () => {
      historyLoading.value = true
      try {
        const response = await diagnosisAPI.getHistory({ per_page: 8, page: 1 })
        const page = response.data?.data
        const rows = Array.isArray(page?.data) ? page.data : (Array.isArray(page) ? page : [])
        if (rows.length) {
          recentDiagnoses.value = rows
        }
      } catch (error) {
        console.error('Error loading diagnosis history:', error)
      } finally {
        historyLoading.value = false
      }
    }

    const downloadDiagnosisPdf = async (diagnosis) => {
      if (!(diagnosis.can_download_pdf || diagnosis.unlocked) || !diagnosis.result) {
        return
      }
      const key = diagnosis.session_id || diagnosis.id
      downloadingId.value = key
      try {
        const stamp = new Date()
        const response = await api.post('/diagnosis/export-pdf', {
          title: 'CarWise.ai — Diagnosis Evidence Report',
          session_id: diagnosis.session_id,
          vehicle: {
            make: diagnosis.make || diagnosis.vehicle_info?.make || 'Vehicle',
            model: diagnosis.model || diagnosis.vehicle_info?.model || 'Unknown',
            year: Number(diagnosis.year || diagnosis.vehicle_info?.year) || new Date().getFullYear(),
            mileage: diagnosis.mileage ? Number(diagnosis.mileage) : null,
            engine_type: diagnosis.engine_type,
            engine_size: diagnosis.engine_size,
          },
          symptoms: diagnosis.symptoms || [],
          description: diagnosis.description || '',
          result: diagnosis.result,
          generated_at: stamp.toISOString(),
        }, { responseType: 'blob' })

        const blob = new Blob([response.data], { type: 'application/pdf' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        link.download = `CarWise_Diagnosis_${diagnosis.make || 'Car'}_${diagnosis.model || 'Report'}_${stamp.toISOString().slice(0, 10)}.pdf`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
      } catch (error) {
        console.error('PDF download failed', error)
        if (window.$notify) {
          window.$notify.error('PDF export failed', 'Could not download this diagnosis report.')
        } else {
          alert('Failed to download PDF. Please try again.')
        }
      } finally {
        downloadingId.value = null
      }
    }

    const loadUserData = async () => {
      try {
        await checkAuth()
        if (isAuthenticated.value && user.value) {
          await loadStatistics()
        } else {
          router.push('/login')
        }
      } catch (error) {
        console.error('Error loading user data:', error)
        router.push('/login')
      } finally {
        isLoading.value = false
      }
    }

    const loadStatistics = async () => {
      try {
        const dashboardResponse = await dashboardAPI.getStatistics()
        if (dashboardResponse.data.success) {
          const data = dashboardResponse.data.data

          stats.value.totalDiagnoses = data.basic_stats.total_diagnoses
          stats.value.diagnosesThisMonth = data.time_stats.diagnoses_this_month
          stats.value.diagnosesThisWeek = data.time_stats.diagnoses_this_week
          stats.value.carsRegistered = data.basic_stats.total_cars
          stats.value.activeCars = data.basic_stats.active_cars
          stats.value.successRate = data.basic_stats.success_rate
          stats.value.averageResponseTime = data.time_stats.average_response_time
          stats.value.monthlyDiagnoses = data.analytics.monthly_diagnoses
          stats.value.mostCommonIssues = data.analytics.common_issues || []

          if (!recentDiagnoses.value.length && Array.isArray(data.recent_diagnoses)) {
            recentDiagnoses.value = data.recent_diagnoses
          }
        }

        await loadDiagnosisHistory()
      } catch (error) {
        console.error('Error loading statistics:', error)
        await loadDiagnosisHistory()
      }
    }

    const handleEditProfile = () => {
      router.push('/profile/edit')
    }

    const formatDate = (dateString) => {
      if (!dateString) return 'Unknown'
      const date = new Date(dateString)
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const getDiagnosisStatusColor = (status) => {
      switch (status?.toLowerCase()) {
        case 'completed': return 'bg-emerald-50 text-emerald-800'
        case 'processing': return 'bg-amber-50 text-amber-800'
        case 'pending': return 'bg-sky-50 text-sky-800'
        case 'failed': return 'bg-red-50 text-red-800'
        default: return 'bg-slate-100 text-slate-600'
      }
    }

    onMounted(() => {
      const token = localStorage.getItem('token')
      const storedUser = localStorage.getItem('user')

      if (token && storedUser) {
        user.value = JSON.parse(storedUser)
        loadUserData()
      } else {
        router.push('/login')
      }
    })

    return {
      user,
      userInitials,
      avatarBroken,
      stats,
      recentDiagnoses,
      historyLoading,
      downloadingId,
      unlockedCount,
      isLoading,
      downloadDiagnosisPdf,
      handleEditProfile,
      formatDate,
      getDiagnosisStatusColor
    }
  }
}
</script>
