<template>
  <div class="min-h-screen bg-white">
    <div class="max-w-5xl mx-auto px-5 py-14 sm:px-8">
      <p class="text-sm font-medium text-slate-500">Admin</p>
      <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
        <div>
          <h1 class="text-4xl font-semibold tracking-tight text-slate-950">API credits</h1>
          <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
            The balance each connected API reports, then the credits each account used on CarWise.
          </p>
        </div>
        <button
          type="button"
          class="rounded-full bg-slate-950 px-5 py-2.5 text-sm font-medium text-white disabled:opacity-50"
          :disabled="loading"
          @click="load"
        >
          {{ loading ? 'Checking…' : 'Refresh' }}
        </button>
      </div>

      <p v-if="error" class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
        {{ error }}
      </p>

      <div v-else-if="loading && !report" class="mt-10 text-sm text-slate-500">Checking the APIs…</div>

      <template v-else-if="report">
        <section class="mt-10 rounded-3xl border border-slate-200 p-6">
          <h2 class="text-lg font-semibold text-slate-950">Add credits</h2>
          <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">
            Type the euros to add. CarWise converts them and opens the provider’s billing page.
            The API key cannot charge the card. The balance above changes after the provider reports the payment.
          </p>
          <form
            v-for="provider in report.providers"
            :key="`pay-${provider.name}`"
            class="mt-5 flex flex-wrap items-end gap-3"
            @submit.prevent="pay(provider.name)"
          >
            <label class="text-sm font-medium text-slate-700">
              {{ provider.name }}
              <input
                v-model="amounts[provider.name]"
                type="number"
                min="5"
                max="5000"
                step="0.01"
                required
                placeholder="100"
                class="mt-1 block w-36 rounded-xl border border-slate-200 px-3 py-2 text-slate-950"
              >
            </label>
            <span class="pb-2 text-sm text-slate-500">EUR</span>
            <button
              type="submit"
              class="rounded-full bg-slate-950 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
              :disabled="paying === provider.name"
            >
              {{ paying === provider.name ? 'Opening…' : 'Pay' }}
            </button>
          </form>
          <p v-if="payError" class="mt-4 text-sm text-red-700">{{ payError }}</p>
          <p v-if="checkout" class="mt-4 text-sm leading-6 text-slate-700">
            On {{ checkout.provider }}, choose Buy credits and enter
            <strong>{{ checkout.charge_amount }} {{ checkout.charge_currency }}</strong>
            <span v-if="checkout.rate_date"> ({{ checkout.amount_eur }} EUR on {{ checkout.rate_date }}).</span>
            The billing page is open in a new tab. Refresh this page after the payment appears.
          </p>
        </section>

        <div class="mt-10 grid gap-4 md:grid-cols-3">
          <article
            v-for="provider in report.providers"
            :key="provider.name"
            class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
          >
            <p class="text-sm font-medium text-slate-500">{{ provider.name }}</p>
            <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-950">
              {{ balance(provider) }}
            </p>
            <p class="mt-3 text-sm leading-6 text-slate-600">{{ provider.status }}</p>
            <dl class="mt-6 space-y-2 border-t border-slate-100 pt-4 text-sm">
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Spent on CarWise</dt>
                <dd class="font-medium text-slate-950">{{ money(provider.measured_spend) }}</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Billed calls</dt>
                <dd class="font-medium text-slate-950">{{ provider.billed_calls }}</dd>
              </div>
              <div class="flex justify-between gap-4">
                <dt class="text-slate-500">Not billed</dt>
                <dd class="font-medium text-slate-950">{{ provider.unbilled_calls }}</dd>
              </div>
            </dl>
          </article>
        </div>

        <section class="mt-10 overflow-hidden rounded-3xl border border-slate-200">
          <div class="border-b border-slate-200 px-6 py-5">
            <h2 class="text-lg font-semibold text-slate-950">Spent by account</h2>
            <p class="mt-1 text-sm leading-6 text-slate-600">
              Spent is the token cost stored for each diagnosis. Calls with no token record are not billed.
            </p>
          </div>

          <p v-if="report.users.length === 0" class="px-6 py-8 text-sm text-slate-500">
            No diagnosis calls are stored yet.
          </p>

          <div v-else class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
              <thead class="bg-slate-50 text-slate-500">
                <tr>
                  <th class="px-6 py-3 font-medium">User</th>
                  <th class="px-4 py-3 font-medium">API</th>
                  <th class="px-4 py-3 font-medium">Billed</th>
                  <th class="px-4 py-3 font-medium">Not billed</th>
                  <th class="px-4 py-3 font-medium">Tokens in</th>
                  <th class="px-4 py-3 font-medium">Tokens out</th>
                  <th class="px-6 py-3 text-right font-medium">Spent</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in report.users" :key="`${row.user_id}-${row.provider}`" class="border-t border-slate-100">
                  <td class="px-6 py-4">
                    <div class="font-medium text-slate-950">{{ row.user_id === null ? 'Guest' : `user_id ${row.user_id}` }}</div>
                    <div class="text-slate-500">{{ row.name }}<span v-if="row.email"> · {{ row.email }}</span></div>
                  </td>
                  <td class="px-4 py-4 text-slate-700">{{ row.provider }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.billed_calls }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.unbilled_calls }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.input_tokens }}</td>
                  <td class="px-4 py-4 text-slate-700">{{ row.output_tokens }}</td>
                  <td class="px-6 py-4 text-right font-medium text-slate-950">{{ money(row.spent) }}</td>
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
const loading = ref(false)
const error = ref('')
const amounts = ref({})
const paying = ref('')
const payError = ref('')
const checkout = ref(null)

const balance = (provider) => {
  if (!provider.remaining_known || provider.remaining === null) {
    return 'Not reported'
  }

  return `${provider.remaining} ${provider.currency}`
}

const money = (value) => `${value} USD`

const load = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await api.get('/admin/api-credits')
    report.value = response.data.data
  } catch (err) {
    report.value = null
    error.value = err.response?.status === 403
      ? 'This page is only available to an admin.'
      : 'The credit report could not be loaded.'
  } finally {
    loading.value = false
  }
}

const pay = async (provider) => {
  paying.value = provider
  payError.value = ''
  checkout.value = null

  try {
    const response = await api.post('/admin/api-credits', {
      provider,
      amount: Number(amounts.value[provider]),
    })
    checkout.value = response.data.data
    window.open(checkout.value.billing_url, '_blank', 'noopener')
  } catch (err) {
    payError.value = err.response?.data?.message || 'The payment page could not be opened.'
  } finally {
    paying.value = ''
  }
}

onMounted(load)
</script>
