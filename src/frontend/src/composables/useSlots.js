import { ref } from 'vue'
import { get } from '../api/client'

export function useSlots() {
  const slots = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchSlots(eventTypeId) {
    loading.value = true
    error.value = null
    try {
      const data = await get(`/event-types/${eventTypeId}/slots`)
      slots.value = data.slots || []
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  return { slots, loading, error, fetchSlots }
}
