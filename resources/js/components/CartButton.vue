<template>
  <div class="fixed top-20 right-4 z-50">
    <button 
      @click="$emit('toggle')"
      class="relative bg-white dark:bg-secondary-800 text-gray-900 dark:text-white shadow-lg hover:shadow-xl rounded-full p-3 transition-all duration-200 border border-gray-200 dark:border-secondary-700"
      title="View Cart"
    >
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
      </svg>
      <span v-if="count > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">
        {{ count }}
      </span>
    </button>
  </div>
</template>

<script setup>
defineProps({
  count: {
    type: Number,
    default: 0
  }
})

defineEmits(['toggle'])
</script>

