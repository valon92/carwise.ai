<template>
  <div class="min-h-screen bg-white">
    <div class="max-w-6xl mx-auto px-5 py-14 sm:px-8">
      <p class="text-sm font-medium text-slate-500">Admin</p>
      <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 class="text-4xl font-semibold tracking-tight text-slate-950">Statistics</h1>
          <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
            What CarWise spent on the APIs, what users paid for the services, and what is left.
          </p>
        </div>
        <button
          type="button"
          class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-medium text-white disabled:opacity-50"
          :disabled="loading"
          @click="load"
        >
          {{ loading ? 'Loading…' : 'Refresh' }}
        </button>
      </div>

      <div class="mt-8 flex flex-wrap items-center gap-2">
        <button
          v-for="option in ranges"
          :key="option.id"
          type="button"
          class="rounded-full border px-4 py-2 text-sm font-medium"
          :class="range === option.id ? 'border-slate-950 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-700'"
          @click="choose(option.id)"
        >
          {{ option.label }}
        </button>
      </div>

      <form v-if="range === 'custom'" class="mt-4 flex flex-wrap items-end gap-3" @submit.prevent="load">
        <label class="text-sm text-slate-600">
          From
          <input v-model="customFrom" type="date" required class="mt-1 block rounded-xl border border-slate-200 px-3 py-2 text-slate-950">
        </label>
        <label class="text-sm text-slate-600">
          To
          <input v-model="customTo" type="date" required class="mt-1 block rounded-xl border border-slate-200 px-3 py-2 text-slate-950">
        </label>
        <button type="submit" class="rounded-full bg-slate-950 px-4 py-2 text-sm font-medium text-white">Apply</button>
      </form>

      <p v-if="error" class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ error }}
      </p>
      <div v-else-if="loading && !report" class="mt-10 text-sm text-slate-500">Reading the platform records…</div>

      <template v-else-if="report">
        <p class="mt-8 text-sm text-slate-500">Showing {{ report.period.label }}.</p>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
          <article v-for="card in money" :key="card.label" class="rounded-3xl border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">{{ card.label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight" :class="card.negative ? 'text-red-700' : 'text-slate-950'">{{ card.value }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ card.detail }}</p>
          </article>
        </div>

        <p class="mt-6 max-w-3xl text-sm leading-6 text-slate-500">
          The remainder is what users paid, minus the API cost.
          Partner commissions recorded in this period: {{ report.platform.partner_eur }} EUR.
          Hosting, domain, and card fees are not stored, so they are not part of this number.
          <span v-if="report.left.rate_date">The API cost was converted with the rate from {{ report.left.rate_date }}.</span>
        </p>

        <section class="mt-10 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">Services</h2>
            <p class="mt-1 text-sm text-slate-600">Each service, and the money users actually paid for it.</p>
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
                  <td class="px-6 py-4 font-medium text-slate-950">{{ service.name }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ service.count }}</td>
                  <td class="px-6 py-4 text-slate-700">
                    {{ service.from_users }} EUR
                    <span v-if="service.other"> · {{ service.other }}</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="mt-8 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">By account</h2>
            <p class="mt-1 text-sm text-slate-600">API cost against what that account paid.</p>
          </div>
          <p v-if="report.accounts.length === 0" class="px-6 py-8 text-sm text-slate-500">No account used a service in this period.</p>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">Account</th>
                  <th class="px-4 py-3 font-medium">Requests</th>
                  <th class="px-4 py-3 font-medium">API cost</th>
                  <th class="px-4 py-3 font-medium">Paid</th>
                  <th class="px-6 py-3 font-medium">Left</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in report.accounts" :key="row.user_id || row.name" class="border-t border-slate-100">
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-950">{{ row.user_id ? `user_id ${row.user_id}` : 'Guest' }}</div>
                    <div class="text-slate-500">{{ row.name }}</div>
                  </td>
                  <td class="px-4 py-4 text-slate-700">{{ row.requests }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.api_usd }} USD</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.paid }} EUR</td>
                  <td class="px-6 py-4" :class="String(row.left || '').startsWith('-') ? 'text-red-700' : 'text-slate-950'">
                    {{ row.left === null ? 'Not available' : `${row.left} EUR` }}
                  </td>
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
      label: 'Platform spend',
      value: `${data.platform.api_usd} USD`,
      detail: data.platform.api_eur === null
        ? `${data.platform.billed_calls} billed API calls. The euro value is not available without a rate.`
        : `${data.platform.api_eur} EUR · ${data.platform.billed_calls} billed · ${data.platform.unbilled_calls} not billed`,
      negative: false,
    },
    {
      label: 'Paid by users',
      value: `${data.users.eur} EUR`,
      detail: data.users.paid_results === 0
        ? `${data.users.unpaid_requests} requests were not paid`
        : `${data.users.paid_results} paid results · ${data.users.unpaid_requests} not paid`,
      negative: false,
    },
    {
      label: 'Left',
      value: data.left.eur === null ? 'Not available' : `${data.left.eur} EUR`,
      detail: 'Paid by users and partner commissions, minus the API cost',
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
  document.title = 'Statistics - CarWise AI'
  load()
})
</script>
