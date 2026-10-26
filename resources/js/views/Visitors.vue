<template>
  <div class="min-h-screen bg-white">
    <div class="max-w-6xl mx-auto px-5 py-14 sm:px-8">
      <p class="text-sm font-medium text-slate-500">Admin</p>
      <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 class="text-4xl font-semibold tracking-tight text-slate-950">Visitors</h1>
          <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
            Visitors, registrations, and active users for the period you choose, then what CarWise spent and what users paid.
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
        <div class="mt-10 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <article v-for="card in headline" :key="card.label" class="rounded-3xl border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">{{ card.label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ card.value }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ card.detail }}</p>
          </article>
        </div>

        <p class="mt-6 max-w-3xl text-sm leading-6 text-slate-500">
          Showing {{ report.period.label }}. Visitors are people who opened a page or signed in.
          Page visits are stored from <span v-if="report.visitors.recording_since">{{ when(report.visitors.recording_since) }}.</span><span v-else>the next visit.</span>
          Earlier anonymous browsing was not kept.
        </p>

        <div class="mt-8 grid gap-4 md:grid-cols-3">
          <article v-for="card in money" :key="card.label" class="rounded-3xl border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">{{ card.label }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight" :class="card.negative ? 'text-red-700' : 'text-slate-950'">{{ card.value }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ card.detail }}</p>
          </article>
        </div>

        <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
          <article v-for="card in detail" :key="card.label" class="rounded-3xl bg-slate-50 px-6 py-5">
            <p class="text-sm text-slate-500">{{ card.label }}</p>
            <p class="mt-2 text-xl font-semibold text-slate-950">{{ card.value }}</p>
          </article>
        </div>

        <section class="mt-10 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">Registrations</h2>
            <p class="mt-1 text-sm text-slate-600">Each account and the time it was created.</p>
          </div>
          <p v-if="report.recent_registrations.length === 0" class="px-6 py-8 text-sm text-slate-500">No one registered in this period.</p>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">Account</th>
                  <th class="px-4 py-3 font-medium">Role</th>
                  <th class="px-4 py-3 font-medium">Registered</th>
                  <th class="px-6 py-3 font-medium">Last sign-in</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in report.recent_registrations" :key="row.user_id" class="border-t border-slate-100">
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-950">user_id {{ row.user_id }}</div>
                    <div class="text-slate-500">{{ row.name }} · {{ row.email }}</div>
                  </td>
                  <td class="px-4 py-4 capitalize text-slate-700">{{ row.role }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ when(row.registered_at) }}</td>
                  <td class="px-6 py-4 text-slate-700">{{ when(row.last_login_at) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="mt-8 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">Diagnosis requests</h2>
            <p class="mt-1 text-sm text-slate-600">What each account asked, and the API cost stored for that request.</p>
          </div>
          <p v-if="report.recent_requests.length === 0" class="px-6 py-8 text-sm text-slate-500">No diagnosis was requested in this period.</p>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">Account</th>
                  <th class="px-4 py-3 font-medium">Vehicle</th>
                  <th class="px-4 py-3 font-medium">Request</th>
                  <th class="px-4 py-3 font-medium">Status</th>
                  <th class="px-4 py-3 font-medium">Asked</th>
                  <th class="px-4 py-3 text-right font-medium">API cost</th>
                  <th class="px-6 py-3 text-right font-medium">From user</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, index) in report.recent_requests" :key="index" class="border-t border-slate-100">
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-950">{{ row.user_id === null ? 'Guest' : `user_id ${row.user_id}` }}</div>
                    <div class="text-slate-500">{{ row.name }}</div>
                  </td>
                  <td class="px-4 py-4 text-slate-700">{{ row.vehicle }}</td>
                  <td class="px-4 py-4 max-w-xs text-slate-700">{{ row.request || '—' }}</td>
                  <td class="px-4 py-4 capitalize text-slate-700">{{ row.status }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-slate-700">{{ when(row.requested_at) }}</td>
                  <td class="px-4 py-4 text-right font-medium text-slate-950">{{ row.spent }} USD</td>
                  <td class="px-6 py-4 text-right font-medium text-slate-950">{{ row.collected }} USD</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

        <div class="mt-8 grid gap-8 lg:grid-cols-2">
          <section class="overflow-hidden rounded-3xl border border-slate-200">
            <div class="border-b border-slate-200 px-6 py-5">
              <h2 class="text-lg font-semibold text-slate-950">Recent visits</h2>
            </div>
            <p v-if="report.recent_visits.length === 0" class="px-6 py-8 text-sm text-slate-500">
              No page visit is stored in this period.
            </p>
            <ul v-else class="divide-y divide-slate-100">
              <li v-for="(visit, index) in report.recent_visits" :key="index" class="px-6 py-4">
                <div class="flex items-baseline justify-between gap-4">
                  <p class="font-medium text-slate-950">{{ visit.path }}</p>
                  <p class="shrink-0 text-sm text-slate-500">{{ when(visit.visited_at) }}</p>
                </div>
                <p class="mt-1 text-sm text-slate-600">
                  {{ visit.user_id === null ? 'Guest' : `user_id ${visit.user_id} · ${visit.name}` }}
                  <span v-if="visit.device"> · {{ visit.device }}</span>
                </p>
              </li>
            </ul>
          </section>

          <section class="overflow-hidden rounded-3xl border border-slate-200">
            <div class="border-b border-slate-200 px-6 py-5">
              <h2 class="text-lg font-semibold text-slate-950">By account</h2>
            </div>
            <p v-if="report.spend_by_user.length === 0" class="px-6 py-8 text-sm text-slate-500">No request or payment in this period.</p>
            <div v-else class="overflow-x-auto">
              <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                  <tr>
                    <th class="px-6 py-3 font-medium">Account</th>
                    <th class="px-4 py-3 font-medium">Requests</th>
                    <th class="px-4 py-3 text-right font-medium">API cost</th>
                    <th class="px-4 py-3 text-right font-medium">From user</th>
                    <th class="px-6 py-3 text-right font-medium">Left</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in report.spend_by_user" :key="row.user_id ?? 'guest'" class="border-t border-slate-100">
                    <td class="px-6 py-4">
                      <div class="font-medium text-slate-950">{{ row.user_id === null ? 'Guest' : `user_id ${row.user_id}` }}</div>
                      <div class="text-slate-500">{{ row.name }}</div>
                    </td>
                    <td class="px-4 py-4 text-slate-700">{{ row.requests }}</td>
                    <td class="px-4 py-4 text-right text-slate-700">{{ row.expense }}</td>
                    <td class="px-4 py-4 text-right text-slate-700">{{ row.collected }}</td>
                    <td class="px-6 py-4 text-right font-medium" :class="String(row.left).startsWith('-') ? 'text-red-700' : 'text-slate-950'">{{ row.left }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>

        <section v-if="report.pages.length" class="mt-8 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">Pages opened</h2>
          </div>
          <ul class="divide-y divide-slate-100">
            <li v-for="page in report.pages" :key="page.path" class="flex items-center justify-between px-6 py-4 text-sm">
              <span class="font-medium text-slate-950">{{ page.path }}</span>
              <span class="text-slate-600">{{ page.views }}</span>
            </li>
          </ul>
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

const when = (value) => {
  if (!value) return 'Never'
  return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

const headline = computed(() => {
  if (!report.value) return []
  const data = report.value
  return [
    {
      label: 'Visitors',
      value: data.visitors.known,
      detail: `${data.visitors.page_views} page views · ${data.visitors.anonymous} anonymous`,
    },
    {
      label: 'Registrations',
      value: data.registrations.total,
      detail: `${data.registrations.customers} owners · ${data.registrations.mechanics} mechanics · ${data.registrations.admins} admins`,
    },
    {
      label: 'Users',
      value: data.users.total,
      detail: 'Accounts that signed in, browsed, or requested a diagnosis',
    },
    {
      label: 'Diagnosis requests',
      value: data.requests.total,
      detail: `${data.requests.completed} completed · ${data.requests.failed} failed · ${data.requests.open} still open`,
    },
  ]
})

const money = computed(() => {
  if (!report.value) return []
  const ledger = report.value.ledger
  return [
    {
      label: 'Platform spend',
      value: `${ledger.expense} USD`,
      detail: `${ledger.billed_calls} billed API calls · ${ledger.unbilled_calls} not billed`,
      negative: false,
    },
    {
      label: 'From users',
      value: `${ledger.collected} USD`,
      detail: ledger.paid_results === 0 ? 'No result has been paid for in this period' : `${ledger.paid_results} paid results`,
      negative: false,
    },
    {
      label: 'Left',
      value: `${ledger.left} USD`,
      detail: 'What users paid, minus what the APIs cost',
      negative: String(ledger.left).startsWith('-'),
    },
  ]
})

const detail = computed(() => {
  if (!report.value) return []
  const data = report.value
  return [
    { label: 'Sign-ins', value: `${data.sign_ins.total} by ${data.sign_ins.people} people` },
    { label: 'Average sign-in', value: data.sign_ins.average_minutes === null ? 'Not recorded' : `${data.sign_ins.average_minutes} min` },
    { label: 'Page-visit length', value: data.visitors.average_minutes === null ? 'Needs a second page' : `${data.visitors.average_minutes} min` },
    { label: 'Vehicles saved', value: data.vehicles },
    { label: 'Active right now', value: data.sign_ins.active_now },
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
    const response = await api.get('/admin/visitors', { params: periodParams() })
    report.value = response.data.data
  } catch (err) {
    report.value = null
    error.value = err.response?.status === 403
      ? 'This page is only available to an admin.'
      : 'The visitor report could not be loaded.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>
