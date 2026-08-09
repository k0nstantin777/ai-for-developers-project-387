import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAppStore = defineStore('app', () => {
  const mode = ref(localStorage.getItem('app-mode') || 'guest')

  function setMode(newMode) {
    mode.value = newMode
    localStorage.setItem('app-mode', newMode)
  }

  return { mode, setMode }
})
