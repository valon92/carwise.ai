<template>
  <div
    id="floating-action-dock"
    class="fixed z-50 flex flex-col-reverse items-center gap-3 transition-all duration-300 pointer-events-none"
    :class="dockPositionClass"
    aria-label="Quick actions"
  >
    <BackToTop :hidden="chatOpen" class="pointer-events-auto" />
    <div id="floating-action-dock-slot" class="pointer-events-auto" />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import BackToTop from './BackToTop.vue'
import { useFloatingDock } from '../composables/useFloatingDock'

const { cartOpen, chatOpen } = useFloatingDock()

const dockPositionClass = computed(() => {
  if (cartOpen.value) {
    return 'bottom-[max(1.25rem,env(safe-area-inset-bottom))] right-[max(1rem,env(safe-area-inset-right))] sm:right-80 md:right-96'
  }

  return 'bottom-[max(1.25rem,env(safe-area-inset-bottom))] right-[max(1rem,env(safe-area-inset-right))]'
})
</script>
