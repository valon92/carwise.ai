<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <div class="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
      <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-8 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Your account</p>
          <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-900">Diagnosis history</h1>
          <p class="mt-2 max-w-2xl text-slate-600">
            Every check you ran — open the full result, download the dated PDF evidence report, and jump back into Diagnose.
          </p>
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
        </div>
      </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <div class="mb-6 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 sm:grid-cols-3">
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Total checks</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ totalCount }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Unlocked / downloadable</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ unlockedCount }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-[0.14em] text-slate-400">Preview only</p>
          <p class="mt-1 text-2xl font-bold text-slate-900">{{ previewCount }}</p>
        </div>
      </div>

      <div class="mb-6 rounded-2xl border border-slate-200 bg-white p-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
          <input
            v-model="searchQuery"
            type="search"
            placeholder="Search make, model, problem…"
            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 outline-none ring-slate-900 focus:ring-2"
          >
          <select v-model="statusFilter" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
            <option value="">All status</option>
            <option value="completed">Completed</option>
            <option value="processing">Processing</option>
            <option value="failed">Failed</option>
          </select>
          <select v-model="accessFilter" class="rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-800">
            <option value="">All access</option>
            <option value="unlocked">Unlocked + PDF</option>
            <option value="preview">Preview only</option>
          </select>
          <button type="button" @click="clearFilters" class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Clear
          </button>
        </div>
      </div>

      <div v-if="isLoading" class="py-20 text-center">
        <div class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-2 border-slate-200 border-t-slate-900"></div>
        <p class="text-sm text-slate-500">Loading your diagnosis history…</p>
      </div>

      <div v-else-if="loadError" class="rounded-2xl border border-red-200 bg-red-50 px-6 py-10 text-center">
        <p class="text-sm font-medium text-red-800">{{ loadError }}</p>
        <button type="button" class="mt-4 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white" @click="loadDiagnoses()">
          Try again
        </button>
      </div>

      <div v-else-if="filteredDiagnoses.length === 0" class="rounded-2xl border border-slate-200 bg-white px-6 py-16 text-center">
        <h3 class="text-lg font-semibold text-slate-900">No diagnoses yet</h3>
        <p class="mx-auto mt-2 max-w-md text-sm text-slate-500">
          {{ diagnoses.length === 0
            ? 'Run an AI diagnosis to build your evidence history here.'
            : 'Nothing matches these filters.' }}
        </p>
        <router-link to="/diagnose" class="mt-6 inline-flex rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">
          Start AI Diagnosis
        </router-link>
      </div>

      <div v-else class="space-y-4">
        <article
          v-for="diagnosis in filteredDiagnoses"
          :key="diagnosis.session_id || diagnosis.id"
          class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm"
        >
          <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-2">
                <h2 class="text-lg font-semibold text-slate-900">
                  {{ diagnosis.make }} {{ diagnosis.model }}
                  <span v-if="diagnosis.year" class="font-normal text-slate-500">({{ diagnosis.year }})</span>
                </h2>
                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusClass(diagnosis.status)">
                  {{ statusLabel(diagnosis.status) }}
                </span>
                <span
                  class="rounded-full px-2.5 py-1 text-xs font-medium"
                  :class="diagnosis.unlocked || diagnosis.can_download_pdf ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-800'"
                >
                  {{ diagnosis.unlocked || diagnosis.can_download_pdf ? 'Unlocked' : 'Preview' }}
                </span>
                <span
                  v-if="diagnosis.result?.severity"
                  class="rounded-full px-2.5 py-1 text-xs font-medium"
                  :class="severityClass(diagnosis.result.severity)"
                >
                  {{ diagnosis.result.severity }}
                </span>
              </div>

              <p class="mt-2 text-xs text-slate-500">
                {{ formatDate(diagnosis.created_at) }}
                <span v-if="diagnosis.result?.evidence?.report_id"> · ID {{ diagnosis.result.evidence.report_id }}</span>
                <span v-if="diagnosis.session_id"> · {{ diagnosis.session_id }}</span>
              </p>

              <h3 class="mt-3 text-base font-medium text-slate-900">
                {{ diagnosis.result?.problem_title || 'Diagnosis in progress' }}
              </h3>
              <p v-if="diagnosis.result?.problem_description || diagnosis.description" class="mt-1 line-clamp-2 text-sm text-slate-600">
                {{ diagnosis.result?.problem_description || diagnosis.description }}
              </p>

              <div v-if="(diagnosis.result?.illustrations || []).length" class="mt-4 overflow-hidden rounded-xl border border-slate-200">
                <img
                  :src="diagnosis.result.illustrations[0].url"
                  :alt="diagnosis.result.illustrations[0].caption || diagnosis.result.problem_title"
                  class="h-40 w-full object-cover"
                  loading="lazy"
                >
              </div>
            </div>

            <div class="flex shrink-0 flex-wrap gap-2 lg:flex-col">
              <button
                type="button"
                @click="viewDiagnosis(diagnosis)"
                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50"
              >
                View details
              </button>
              <button
                v-if="diagnosis.can_download_pdf || diagnosis.unlocked"
                type="button"
                :disabled="downloadingId === (diagnosis.session_id || diagnosis.id)"
                @click="downloadReport(diagnosis)"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-50"
              >
                {{ downloadingId === (diagnosis.session_id || diagnosis.id) ? 'Preparing PDF…' : 'Download PDF' }}
              </button>
              <router-link
                v-else
                :to="{ path: '/diagnose', query: { session: diagnosis.session_id } }"
                class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-center text-sm font-semibold text-amber-900 hover:bg-amber-100"
              >
                Unlock to download
              </router-link>
            </div>
          </div>
        </article>
      </div>

      <div v-if="hasMore" class="mt-8 text-center">
        <button
          type="button"
          :disabled="loadingMore"
          @click="loadMore"
          class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-800 hover:bg-slate-50 disabled:opacity-50"
        >
          {{ loadingMore ? 'Loading…' : 'Load more' }}
        </button>
      </div>
    </div>

    <div v-if="selectedDiagnosis" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 p-4" @click.self="closeModal">
      <div class="max-h-[90vh] w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
          <div>
            <h3 class="text-lg font-semibold text-slate-900">
              {{ selectedDiagnosis.make }} {{ selectedDiagnosis.model }}
            </h3>
            <p class="text-xs text-slate-500">{{ formatDate(selectedDiagnosis.created_at) }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button
              v-if="selectedDiagnosis.can_download_pdf || selectedDiagnosis.unlocked"
              type="button"
              @click="downloadReport(selectedDiagnosis)"
              class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white"
            >
              Download PDF
            </button>
            <button type="button" @click="closeModal" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100">✕</button>
          </div>
        </div>
        <div class="max-h-[calc(90vh-80px)] overflow-y-auto p-6">
          <DiagnosisDetail :diagnosis="selectedDiagnosis" />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api, { diagnosisAPI } from '../services/api'
import { useAuth } from '../composables/useAuth'
import DiagnosisDetail from '../components/DiagnosisDetail.vue'

export default {
  name: 'DiagnosisHistory',
  components: { DiagnosisDetail },
  setup() {
    const router = useRouter()
    const { isAuthenticated, checkAuth } = useAuth()
    const diagnoses = ref([])
    const isLoading = ref(true)
    const loadingMore = ref(false)
    const selectedDiagnosis = ref(null)
    const searchQuery = ref('')
    const statusFilter = ref('')
    const accessFilter = ref('')
    const currentPage = ref(1)
    const lastPage = ref(1)
    const downloadingId = ref(null)
    const loadError = ref('')

    const hasMore = computed(() => currentPage.value < lastPage.value)
    const totalCount = computed(() => diagnoses.value.length)
    const unlockedCount = computed(() => diagnoses.value.filter(d => d.unlocked || d.can_download_pdf).length)
    const previewCount = computed(() => diagnoses.value.filter(d => !(d.unlocked || d.can_download_pdf)).length)

    const filteredDiagnoses = computed(() => {
      let filtered = diagnoses.value
      if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase()
        filtered = filtered.filter(d =>
          `${d.make} ${d.model} ${d.description || ''} ${d.result?.problem_title || ''}`.toLowerCase().includes(q)
        )
      }
      if (statusFilter.value) {
        filtered = filtered.filter(d => d.status === statusFilter.value)
      }
      if (accessFilter.value === 'unlocked') {
        filtered = filtered.filter(d => d.unlocked || d.can_download_pdf)
      }
      if (accessFilter.value === 'preview') {
        filtered = filtered.filter(d => !(d.unlocked || d.can_download_pdf))
      }
      return filtered
    })

    const normalizeList = (payload) => {
      if (Array.isArray(payload)) return payload
      if (Array.isArray(payload?.data)) return payload.data
      return []
    }

    const loadDiagnoses = async (page = 1, append = false) => {
      try {
        if (append) loadingMore.value = true
        else isLoading.value = true
        loadError.value = ''

        const response = await diagnosisAPI.getHistory({ per_page: 12, page })
        const body = response.data
        if (!body?.success) {
          loadError.value = body?.message || 'Could not load diagnosis history.'
          return
        }

        const pageData = body.data
        const rows = normalizeList(pageData)
        diagnoses.value = append ? [...diagnoses.value, ...rows] : rows
        currentPage.value = pageData?.current_page || page
        lastPage.value = pageData?.last_page || page
      } catch (error) {
        loadError.value = error.response?.data?.message || 'Could not load diagnosis history.'
        if (error.response?.status === 401) router.push('/login')
      } finally {
        isLoading.value = false
        loadingMore.value = false
      }
    }

    const loadMore = () => {
      if (!hasMore.value || loadingMore.value) return
      loadDiagnoses(currentPage.value + 1, true)
    }

    const viewDiagnosis = (diagnosis) => {
      selectedDiagnosis.value = diagnosis
    }

    const closeModal = () => {
      selectedDiagnosis.value = null
    }

    const downloadReport = async (diagnosis) => {
      if (!(diagnosis.can_download_pdf || diagnosis.unlocked) || !diagnosis.result) {
        return
      }

      const key = diagnosis.session_id || diagnosis.id
      downloadingId.value = key
      try {
        const stamp = new Date()
        const pdfContent = {
          title: 'CarWise.ai — Diagnosis Evidence Report',
          session_id: diagnosis.session_id,
          vehicle: {
            make: diagnosis.make,
            model: diagnosis.model,
            year: Number(diagnosis.year) || new Date().getFullYear(),
            mileage: diagnosis.mileage ? Number(diagnosis.mileage) : null,
            engine_type: diagnosis.engine_type,
            engine_size: diagnosis.engine_size,
          },
          symptoms: diagnosis.symptoms || [],
          description: diagnosis.description || '',
          result: diagnosis.result,
          generated_at: stamp.toISOString(),
        }

        const response = await api.post('/diagnosis/export-pdf', pdfContent, { responseType: 'blob' })
        const blob = new Blob([response.data], { type: 'application/pdf' })
        const url = window.URL.createObjectURL(blob)
        const link = document.createElement('a')
        link.href = url
        const day = stamp.toISOString().slice(0, 10)
        link.download = `CarWise_Diagnosis_${diagnosis.make}_${diagnosis.model}_${day}.pdf`
        document.body.appendChild(link)
        link.click()
        document.body.removeChild(link)
        window.URL.revokeObjectURL(url)
      } catch (error) {
        console.error('PDF download failed', error)
        if (window.$notify) {
          window.$notify.error('PDF export failed', 'Could not download this diagnosis report.')
        } else {
          alert('Failed to download report. Please try again.')
        }
      } finally {
        downloadingId.value = null
      }
    }

    const clearFilters = () => {
      searchQuery.value = ''
      statusFilter.value = ''
      accessFilter.value = ''
    }

    const formatDate = (value) => {
      if (!value) return '—'
      try {
        return new Date(value).toLocaleString(undefined, {
          year: 'numeric',
          month: 'short',
          day: '2-digit',
          hour: '2-digit',
          minute: '2-digit',
        })
      } catch (_) {
        return String(value)
      }
    }

    const statusClass = (status) => {
      const map = {
        completed: 'bg-emerald-50 text-emerald-800',
        processing: 'bg-amber-50 text-amber-800',
        failed: 'bg-red-50 text-red-800',
      }
      return map[status?.toLowerCase()] || 'bg-slate-100 text-slate-700'
    }

    const statusLabel = (status) => {
      const map = { completed: 'Completed', processing: 'Processing', failed: 'Failed' }
      return map[status?.toLowerCase()] || 'Unknown'
    }

    const severityClass = (severity) => {
      const map = {
        low: 'bg-emerald-50 text-emerald-800',
        medium: 'bg-amber-50 text-amber-800',
        high: 'bg-orange-50 text-orange-800',
        critical: 'bg-red-50 text-red-800',
      }
      return map[severity?.toLowerCase()] || 'bg-slate-100 text-slate-700'
    }

    onMounted(async () => {
      document.title = 'Diagnosis History - CarWise.ai'
      if (!localStorage.getItem('token') && !isAuthenticated.value) {
        router.push({ path: '/login', query: { redirect: '/diagnosis-history' } })
        return
      }
      // Load history immediately; auth verify can run in parallel without blocking the list.
      const loadPromise = loadDiagnoses()
      checkAuth().catch(() => {})
      await loadPromise
    })

    return {
      diagnoses,
      isLoading,
      loadingMore,
      loadError,
      selectedDiagnosis,
      searchQuery,
      statusFilter,
      accessFilter,
      hasMore,
      downloadingId,
      totalCount,
      unlockedCount,
      previewCount,
      filteredDiagnoses,
      loadDiagnoses,
      loadMore,
      viewDiagnosis,
      closeModal,
      downloadReport,
      clearFilters,
      formatDate,
      statusClass,
      statusLabel,
      severityClass,
    }
  },
}
</script>
