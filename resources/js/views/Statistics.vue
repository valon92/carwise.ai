<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-100">
    <div class="border-b border-slate-200/80 bg-white/90 backdrop-blur-sm">
      <div class="mx-auto flex max-w-6xl flex-col gap-4 px-5 py-10 sm:flex-row sm:items-end sm:justify-between sm:px-8">
        <div>
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">CEO · Admin</p>
          <h1 class="mt-2 text-4xl font-bold tracking-tight text-slate-900">Platform profit</h1>
          <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
            Fitimi yt nga userat: pagesa e tyre minus kostoja e API. Markup i platformës:
            <span class="font-semibold text-slate-900">+{{ report?.profit?.margin_percent ?? 30 }}%</span>
            mbi shpenzimin e API (nuk i shfaqet userit).
          </p>
        </div>
        <button
          type="button"
          class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 disabled:opacity-50"
          :disabled="loading"
          @click="load"
        >
          {{ loading ? 'Loading…' : 'Refresh' }}
        </button>
      </div>
    </div>

    <div class="mx-auto max-w-6xl px-5 py-8 sm:px-8">
      <div class="flex flex-wrap items-center gap-2">
        <button
          v-for="option in ranges"
          :key="option.id"
          type="button"
          class="rounded-full border px-4 py-2 text-sm font-medium"
          :class="range === option.id ? 'border-slate-900 bg-slate-900 text-white' : 'border-slate-200 bg-white text-slate-700 hover:bg-slate-50'"
          @click="choose(option.id)"
        >
          {{ option.label }}
        </button>
      </div>

      <form v-if="range === 'custom'" class="mt-4 flex flex-wrap items-end gap-3" @submit.prevent="load">
        <label class="text-sm text-slate-600">
          From
          <input v-model="customFrom" type="date" required class="mt-1 block rounded-xl border border-slate-200 px-3 py-2 text-slate-900">
        </label>
        <label class="text-sm text-slate-600">
          To
          <input v-model="customTo" type="date" required class="mt-1 block rounded-xl border border-slate-200 px-3 py-2 text-slate-900">
        </label>
        <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Apply</button>
      </form>

      <p v-if="error" class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ error }}
      </p>
      <div v-else-if="loading && !report" class="mt-10 text-sm text-slate-500">Reading platform records…</div>

      <template v-else-if="report">
        <p class="mt-8 text-sm text-slate-500">Period: {{ report.period.label }}</p>

        <!-- Hero profit -->
        <section class="mt-6 rounded-3xl border border-slate-900 bg-slate-900 px-6 py-8 text-white shadow-sm sm:px-8">
          <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Your profit to date</p>
          <p class="mt-3 text-5xl font-bold tracking-tight sm:text-6xl">
            €{{ report.profit.eur }}
          </p>
          <p class="mt-4 max-w-2xl text-sm leading-relaxed text-slate-300">
            {{ report.profit.detail }}
            Nga diagnozat e paguara: users paguan €{{ report.profit.diagnosis_paid }},
            API kushtoi €{{ report.profit.diagnosis_api_cost }},
            fitimi yt €{{ report.profit.from_diagnosis }}
            ({{ report.profit.paid_unlocks }} unlock{{ report.profit.paid_unlocks === 1 ? '' : 's' }}).
          </p>
        </section>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
          <article v-for="card in money" :key="card.label" class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">{{ card.label }}</p>
            <p class="mt-3 text-3xl font-bold tracking-tight" :class="card.negative ? 'text-red-700' : 'text-slate-900'">
              {{ card.value }}
            </p>
            <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ card.detail }}</p>
          </article>
        </div>

        <p class="mt-6 max-w-3xl text-sm leading-relaxed text-slate-500">
          Userat nuk e shohin formulën. Ata shohin vetëm totalin e faturës.
          Partner commissions: €{{ report.platform.partner_eur }}.
          Hosting / domain / Stripe fees nuk janë në këtë numër.
          <span v-if="report.left.rate_date">Kursi USD→EUR: {{ report.left.rate_date }}.</span>
        </p>

        <section class="mt-10 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-900">Services</h2>
            <p class="mt-1 text-sm text-slate-600">Çfarë kanë paguar userat, sipas shërbimit.</p>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">Service</th>
                  <th class="px-4 py-3 font-medium">Records</th>
                  <th class="px-6 py-3 font-medium">Paid by users</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="service in report.services" :key="service.name" class="border-t border-slate-100">
                  <td class="px-6 py-4 font-medium text-slate-900">{{ service.name }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ service.count }}</td>
                  <td class="px-6 py-4 text-slate-700">
                    €{{ service.from_users }}
                    <span v-if="service.other"> · {{ service.other }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-900">By account</h2>
            <p class="mt-1 text-sm text-slate-600">Fitimi yt nga secili llogari (markup 30%).</p>
          </div>
          <p v-if="report.accounts.length === 0" class="px-6 py-8 text-sm text-slate-500">
            Nuk ka unlock të paguar në këtë periudhë.
          </p>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">Account</th>
                  <th class="px-4 py-3 font-medium">Requests</th>
                  <th class="px-4 py-3 font-medium">API cost</th>
                  <th class="px-4 py-3 font-medium">Paid</th>
                  <th class="px-6 py-3 font-medium">Your profit</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in report.accounts" :key="row.user_id || row.name" class="border-t border-slate-100">
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-900">{{ row.user_id ? `user_id ${row.user_id}` : 'Guest' }}</div>
                    <div class="text-slate-500">{{ row.name }}</div>
                  </td>
                  <td class="px-4 py-4 text-slate-700">{{ row.requests }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.api_usd }} USD</td>
                  <td class="px-4 py-4 text-slate-700">€{{ row.paid }}</td>
                  <td class="px-6 py-4 font-semibold text-slate-900">€{{ row.profit }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import api from '../services/api'

const report = ref(null)
const loading = ref(false)
const error = ref('')
const range = ref('all')
const customFrom = ref('')
const customTo = ref('')

const ranges = [
  { id: 'all', label: 'All time' },
  { id: 'today', label: 'Today' },
  { id: '7', label: '7 days' },
  { id: '30', label: '30 days' },
  { id: 'custom', label: 'Custom' },
]

const isoDate = (date) => {
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${date.getFullYear()}-${month}-${day}`
}

const money = computed(() => {
  if (!report.value) return []
  const data = report.value
  return [
    {
      label: 'Users paid',
      value: `€${data.users.eur}`,
      detail: data.users.paid_results === 0
        ? `${data.users.unpaid_requests} requests not paid yet`
        : `${data.users.paid_results} paid unlocks · ${data.users.unpaid_requests} unpaid`,
      negative: false,
    },
    {
      label: 'API spend',
      value: data.platform.api_eur === null ? `${data.platform.api_usd} USD` : `€${data.platform.api_eur}`,
      detail: `${data.platform.billed_calls} billed API calls · ${data.platform.unbilled_calls} without usage`,
      negative: false,
    },
    {
      label: 'Net left',
      value: data.left.eur === null ? '—' : `€${data.left.eur}`,
      detail: 'Paid by users (− partner commissions) minus API cost',
      negative: String(data.left.eur || '').startsWith('-'),
    },
  ]
})

const periodParams = () => {
  const today = new Date()
  if (range.value === 'today') {
    const day = isoDate(today)
    return { from: day, to: day }
  }
  if (range.value === '7' || range.value === '30') {
    const start = new Date(today)
    start.setDate(today.getDate() - (range.value === '7' ? 6 : 29))
    return { from: isoDate(start), to: isoDate(today) }
  }
  if (range.value === 'custom' && customFrom.value && customTo.value) {
    return { from: customFrom.value, to: customTo.value }
  }
  return {}
}

const choose = (id) => {
  range.value = id
  if (id !== 'custom') load()
}

const load = async () => {
  if (range.value === 'custom' && (!customFrom.value || !customTo.value)) return
  loading.value = true
  error.value = ''
  try {
    const response = await api.get('/admin/statistics', { params: periodParams() })
    report.value = response.data.data
  } catch (err) {
    report.value = null
    error.value = err.response?.status === 403
      ? 'This page is only available to an admin.'
      : 'The statistics could not be loaded.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  document.title = 'Platform profit - CarWise AI'
  load()
})
</script>
