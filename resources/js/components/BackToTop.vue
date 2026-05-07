<template>
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0 translate-y-2 scale-95"
    enter-to-class="opacity-100 translate-y-0 scale-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100 translate-y-0 scale-100"
    leave-to-class="opacity-0 translate-y-2 scale-95"
  >
    <button
      v-if="visible"
      type="button"
      class="fixed bottom-6 right-6 z-40 inline-flex items-center justify-center w-12 h-12 rounded-2xl border border-white/20 dark:border-secondary-700/50 bg-white/80 dark:bg-secondary-800/80 backdrop-blur-xl shadow-lg hover:shadow-xl text-secondary-800 dark:text-white hover:bg-white/90 dark:hover:bg-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-secondary-900"
      :aria-label="label"
      @click="scrollToTop"
    >
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
      </svg>
    </button>
  </Transition>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

const props = defineProps({
  /**
   * Show button after scrolling this many pixels.
   */
  threshold: { type: Number, default: 300 },
  /**
   * Accessible label
   */
  label: { type: String, default: 'Scroll to top' },
})

const visible = ref(false)

const prefersReducedMotion = computed(() => {
  if (typeof window === 'undefined') return false
  return window.matchMedia?.('(prefers-reduced-motion: reduce)')?.matches || false
})

let raf = 0
let poll = 0

const getScrollTop = () => {
  const el = document.scrollingElement || document.documentElement || document.body
  return el?.scrollTop || 0
}

const onScroll = () => {
  if (raf) return
  raf = window.requestAnimationFrame(() => {
    raf = 0
    visible.value = getScrollTop() > props.threshold
  })
}

const scrollToTop = () => {
  const behavior = prefersReducedMotion.value ? 'auto' : 'smooth'
  const el = document.scrollingElement || document.documentElement || document.body
  if (el && el !== document.body) {
    el.scrollTo?.({ top: 0, left: 0, behavior })
  } else {
    window.scrollTo({ top: 0, left: 0, behavior })
  }
}

onMounted(() => {
  onScroll()
  window.addEventListener('scroll', onScroll, { passive: true })
  window.addEventListener('resize', onScroll, { passive: true })
  // Fallback for environments where scroll event is unreliable (some iOS cases).
  poll = window.setInterval(onScroll, 500)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', onScroll)
  if (raf) window.cancelAnimationFrame(raf)
  if (poll) window.clearInterval(poll)
})
</script>

