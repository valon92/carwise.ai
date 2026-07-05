import { ref, readonly } from 'vue'

const cartOpen = ref(false)
const chatOpen = ref(false)

export function useFloatingDock() {
  const setCartOpen = (value) => {
    cartOpen.value = Boolean(value)
  }

  const setChatOpen = (value) => {
    chatOpen.value = Boolean(value)
  }

  return {
    cartOpen: readonly(cartOpen),
    chatOpen: readonly(chatOpen),
    setCartOpen,
    setChatOpen,
  }
}
