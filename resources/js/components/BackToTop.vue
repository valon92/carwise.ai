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
      v-if="visible && !hidden"
      type="button"
      class="inline-flex items-center justify-center w-12 h-12 rounded-2xl border border-white/30 dark:border-secondary-600/60 bg-white/90 dark:bg-secondary-800/90 backdrop-blur-xl shadow-lg shadow-secondary-900/10 hover:shadow-xl text-secondary-700 dark:text-secondary-100 hover:text-primary-600 dark:hover:text-primary-400 hover:border-primary-200 dark:hover:border-primary-700/50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 focus:ring-offset-transparent"
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
  threshold: { type: Number, default: 300 },
  label: { type: String, default: 'Scroll to top' },
  hidden: { type: Boolean, default: false },
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
  poll = window.setInterval(onScroll, 500)
})

onBeforeUnmount(() => {
  window.removeEventListener('scroll', onScroll)
  window.removeEventListener('resize', onScroll)
  if (raf) window.cancelAnimationFrame(raf)
  if (poll) window.clearInterval(poll)
})
</script>
