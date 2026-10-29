<template>
  <div class="space-y-6">
    <div v-if="!(diagnosis.unlocked || diagnosis.can_download_pdf)" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
      This check is still a preview. Unlock it from Diagnose to open full steps, visuals, and PDF download.
    </div>

    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
      <h4 class="mb-4 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Summary</h4>
      <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
          <p class="text-xs text-slate-500">Problem</p>
          <p class="font-medium text-slate-900">{{ diagnosis.result?.problem_title || 'N/A' }}</p>
        </div>
        <div>
          <p class="text-xs text-slate-500">Severity</p>
          <p class="font-medium capitalize text-slate-900">{{ diagnosis.result?.severity || 'N/A' }}</p>
        </div>
        <div>
          <p class="text-xs text-slate-500">Match</p>
          <p class="font-medium text-slate-900">{{ diagnosis.result?.confidence_score ?? 'N/A' }}%</p>
        </div>
        <div>
          <p class="text-xs text-slate-500">Access</p>
          <p class="font-medium text-slate-900">{{ diagnosis.unlocked || diagnosis.can_download_pdf ? 'Unlocked' : 'Preview' }}</p>
        </div>
      </div>
      <p v-if="diagnosis.result?.evidence" class="mt-4 text-xs text-slate-500">
        Evidence ·
        <span v-if="diagnosis.result.evidence.diagnosed_at">Started {{ formatDate(diagnosis.result.evidence.diagnosed_at) }}</span>
        <span v-if="diagnosis.result.evidence.unlocked_at"> · Unlocked {{ formatDate(diagnosis.result.evidence.unlocked_at) }}</span>
        <span v-if="diagnosis.result.evidence.report_id"> · ID {{ diagnosis.result.evidence.report_id }}</span>
      </p>
    </div>

    <div v-if="diagnosis.description">
      <h4 class="mb-2 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Reported description</h4>
      <p class="rounded-xl border border-slate-200 bg-white p-4 text-sm leading-relaxed text-slate-700">
        {{ diagnosis.description }}
      </p>
    </div>

    <div v-if="diagnosis.result?.problem_description">
      <h4 class="mb-2 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">AI explanation</h4>
      <p class="text-sm leading-relaxed text-slate-700">{{ diagnosis.result.problem_description }}</p>
    </div>

    <div v-if="(diagnosis.result?.illustrations || []).length" class="space-y-3">
      <h4 class="text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Problem visual</h4>
      <figure
        v-for="(image, index) in diagnosis.result.illustrations"
        :key="image.path || image.url || index"
        class="overflow-hidden rounded-xl border border-slate-200"
      >
        <img :src="image.url" :alt="image.caption || diagnosis.result.problem_title" class="max-h-72 w-full object-cover" loading="lazy">
        <figcaption class="px-4 py-3 text-sm text-slate-600">
          {{ image.caption || diagnosis.result.problem_title }}
        </figcaption>
      </figure>
    </div>

    <div v-if="causes.length">
      <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Why this is likely</h4>
      <div class="divide-y divide-slate-100 rounded-xl border border-slate-200">
        <div v-for="(cause, index) in causes" :key="index" class="px-4 py-3">
          <div class="flex items-baseline justify-between gap-3">
            <p class="font-medium text-slate-900">{{ cause.title || cause.cause }}</p>
            <span v-if="cause.probability" class="text-xs text-slate-500">{{ cause.probability }}%</span>
          </div>
          <p v-if="cause.description || cause.explanation" class="mt-1 text-sm text-slate-600">
            {{ cause.description || cause.explanation }}
          </p>
        </div>
      </div>
    </div>

    <div v-if="actions.length">
      <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">What to do</h4>
      <ol class="space-y-3">
        <li v-for="(action, index) in actions" :key="index" class="flex gap-3">
          <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-900 text-xs font-semibold text-white">{{ index + 1 }}</span>
          <div>
            <p class="font-medium text-slate-900">{{ action.title || action.action }}</p>
            <p v-if="action.description || action.explanation" class="mt-1 text-sm text-slate-600">
              {{ action.description || action.explanation }}
            </p>
          </div>
        </li>
      </ol>
    </div>

    <div v-if="costs.length">
      <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Guide costs</h4>
      <div class="divide-y divide-slate-100 rounded-xl border border-slate-200">
        <div v-for="(cost, index) in costs" :key="index" class="flex items-baseline justify-between gap-4 px-4 py-3">
          <div>
            <p class="font-medium text-slate-900">{{ cost.service }}</p>
            <p v-if="cost.description" class="text-sm text-slate-500">{{ cost.description }}</p>
          </div>
          <p class="shrink-0 text-sm font-semibold text-slate-900">
            <template v-if="cost.min != null && cost.max != null">€{{ cost.min }} – €{{ cost.max }}</template>
            <template v-else>{{ cost.cost || '—' }}</template>
          </p>
        </div>
      </div>
    </div>

    <div v-if="insights.length">
      <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Also worth knowing</h4>
      <ul class="space-y-2">
        <li v-for="(insight, index) in insights" :key="index" class="text-sm leading-relaxed text-slate-600">
          {{ typeof insight === 'string' ? insight : (insight.text || JSON.stringify(insight)) }}
        </li>
      </ul>
    </div>

    <div v-if="(diagnosis.symptoms || []).length">
      <h4 class="mb-3 text-sm font-semibold uppercase tracking-[0.14em] text-slate-400">Symptoms</h4>
      <div class="flex flex-wrap gap-2">
        <span
          v-for="symptom in diagnosis.symptoms"
          :key="typeof symptom === 'string' ? symptom : JSON.stringify(symptom)"
          class="rounded-full bg-slate-100 px-3 py-1 text-xs text-slate-700"
        >
          {{ typeof symptom === 'string' ? symptom : (symptom.label || symptom.name || 'Symptom') }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'DiagnosisDetail',
  props: {
    diagnosis: {
      type: Object,
      required: true,
    },
  },
  setup(props) {
    const causes = computed(() => {
      const raw = props.diagnosis.result?.likely_causes
      return Array.isArray(raw) ? raw : []
    })

    const actions = computed(() => {
      const raw = props.diagnosis.result?.recommended_actions
      return Array.isArray(raw) ? raw : []
    })

    const costs = computed(() => {
      const raw = props.diagnosis.result?.estimated_costs
      return Array.isArray(raw) ? raw : []
    })

    const insights = computed(() => {
      const raw = props.diagnosis.result?.ai_insights
      if (Array.isArray(raw)) return raw
      if (typeof raw === 'string' && raw) return [raw]
      return []
    })

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

    return { causes, actions, costs, insights, formatDate }
  },
}
</script>
