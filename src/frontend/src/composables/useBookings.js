import { ref } from 'vue'
import { get, post } from '../api/client'

export function useBookings() {
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function fetchAll() {
    loading.value = true
    error.value = null
    try {
      items.value = await get('/bookings')
    } catch (e) {
      error.value = e.message
    } finally {
      loading.value = false
    }
  }

  async function create(data) {
    loading.value = true
    error.value = null
    try {
      const result = await post('/bookings', data)
      return result
    } catch (e) {
      error.value = e.message
      throw e
    } finally {
      loading.value = false
    }
  }

  return { items, loading, error, fetchAll, create }
}
