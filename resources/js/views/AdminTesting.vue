<template>
  <div class="min-h-screen bg-white">
    <div class="max-w-5xl mx-auto px-5 py-14 sm:px-8">
      <p class="text-sm font-medium text-slate-500">Admin</p>
      <h1 class="mt-2 text-4xl font-semibold tracking-tight text-slate-950">Testing costs</h1>
      <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
        An admin does not subscribe. These are the API costs of the diagnoses you run while testing CarWise.
      </p>

      <p v-if="error" class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ error }}
      </p>
      <div v-else-if="loading" class="mt-10 text-sm text-slate-500">Reading your tests…</div>

      <template v-else-if="report">
        <div class="mt-10 grid gap-4 sm:grid-cols-3">
          <article class="rounded-3xl border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Tests</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ report.tests }}</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Diagnoses started from this admin account.</p>
          </article>
          <article class="rounded-3xl border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">API cost</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">{{ report.expense }} USD</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ report.billed_calls }} billed · {{ report.unbilled_calls }} not billed.</p>
          </article>
          <article class="rounded-3xl border border-slate-200 p-6">
            <p class="text-sm font-medium text-slate-500">Subscription</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">None</p>
            <p class="mt-3 text-sm leading-6 text-slate-600">Plans stay with customer accounts.</p>
          </article>
        </div>

        <div class="mt-8">
          <router-link to="/diagnose" class="inline-flex rounded-full bg-slate-950 px-5 py-2.5 text-sm font-medium text-white">
            Run a test diagnosis
          </router-link>
        </div>

        <section class="mt-10 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">Your tests</h2>
          </div>
          <p v-if="report.recent.length === 0" class="px-6 py-8 text-sm text-slate-500">
            No test diagnosis is stored yet.
          </p>
          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">Vehicle</th>
                  <th class="px-4 py-3 font-medium">Request</th>
                  <th class="px-4 py-3 font-medium">Status</th>
                  <th class="px-4 py-3 font-medium">When</th>
                  <th class="px-6 py-3 text-right font-medium">API cost</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(row, index) in report.recent" :key="index" class="border-t border-slate-100">
                  <td class="px-6 py-4 font-medium text-slate-950">{{ row.vehicle }}</td>
                  <td class="px-4 py-4 max-w-xs text-slate-700">{{ row.request || '—' }}</td>
                  <td class="px-4 py-4 capitalize text-slate-700">{{ row.status }}</td>
                  <td class="px-4 py-4 whitespace-nowrap text-slate-700">{{ when(row.tested_at) }}</td>
                  <td class="px-6 py-4 text-right font-medium text-slate-950">{{ row.cost }} USD</td>
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
import { onMounted, ref } from 'vue'
import api from '../services/api'

const report = ref(null)
const loading = ref(true)
const error = ref('')

const when = (value) => {
  if (!value) return '—'
  return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(value))
}

onMounted(async () => {
  document.title = 'Testing costs - CarWise AI'
  try {
    const response = await api.get('/admin/testing-costs')
    report.value = response.data.data
  } catch (err) {
    error.value = err.response?.status === 403
      ? 'Testing costs are only available to an admin.'
      : 'The testing costs could not be loaded.'
  } finally {
    loading.value = false
  }
})
</script>
