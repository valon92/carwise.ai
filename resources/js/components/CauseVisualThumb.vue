<template>
  <button
    type="button"
    class="group relative block h-full min-h-[8.5rem] w-full overflow-hidden bg-slate-950 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400"
    :aria-label="`Open fullscreen: ${title}`"
    @click="$emit('open')"
  >
    <img
      :src="src"
      :alt="title"
      class="absolute inset-0 h-full w-full object-cover object-center transition duration-300 group-hover:scale-[1.03]"
      loading="lazy"
    >

    <!-- Mini red focus cue on thumbnail -->
    <span
      class="pointer-events-none absolute rounded border-2 border-red-500/90 shadow-[0_0_0_1px_rgba(127,29,29,0.5),0_0_18px_rgba(239,68,68,0.35)]"
      :style="miniHotspotStyle"
      aria-hidden="true"
    />
    <span
      class="pointer-events-none absolute h-2.5 w-2.5 -translate-x-1/2 -translate-y-1/2 rounded-full bg-red-500 shadow-[0_0_0_6px_rgba(239,68,68,0.25)]"
      :style="{ left: `${hotspot.x + hotspot.w / 2}%`, top: `${hotspot.y + hotspot.h / 2}%` }"
      aria-hidden="true"
    />

    <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/90 via-slate-950/35 to-transparent px-3 pb-2.5 pt-8">
      <span class="inline-flex items-center gap-1.5 rounded-full border border-red-400/40 bg-red-500/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-red-100">
        <span class="h-1.5 w-1.5 rounded-full bg-red-400" />
        Tap · fullscreen
      </span>
    </span>
  </button>
</template>

<script>
import { computed } from 'vue'

const DEFAULT_HOTSPOT = { x: 42, y: 34, w: 28, h: 30 }

export default {
  name: 'CauseVisualThumb',
  props: {
    src: { type: String, required: true },
    title: { type: String, default: 'Cause visual' },
    annotation: { type: Object, default: null },
  },
  emits: ['open'],
  setup(props) {
    const hotspot = computed(() => {
      const h = props.annotation?.hotspot
      if (h && typeof h.x === 'number') return h
      return DEFAULT_HOTSPOT
    })

    const miniHotspotStyle = computed(() => ({
      left: `${hotspot.value.x}%`,
      top: `${hotspot.value.y}%`,
      width: `${hotspot.value.w}%`,
      height: `${hotspot.value.h}%`,
    }))

    return { hotspot, miniHotspotStyle }
  },
}
</script>
