const BACKEND = process.env.BACKEND_URL || 'http://localhost:8000'

export async function resetDatabase() {
  const res = await fetch(`${BACKEND}/api/testing/reset-database`, { method: 'POST' })
  if (!res.ok) {
    throw new Error(`Failed to reset database: ${res.status}`)
  }
}

export async function createEventType(data) {
  const res = await fetch(`${BACKEND}/api/event-types`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify(data),
  })
  if (!res.ok) {
    const body = await res.json().catch(() => ({}))
    throw new Error(body.message || `HTTP ${res.status}`)
  }
  return res.json()
}

export async function createBooking(data) {
  const res = await fetch(`${BACKEND}/api/bookings`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    body: JSON.stringify(data),
  })
  if (!res.ok) {
    const body = await res.json().catch(() => ({}))
    throw new Error(body.message || `HTTP ${res.status}`)
  }
  return res.json()
}

export async function getSlots(eventTypeId) {
  const res = await fetch(`${BACKEND}/api/event-types/${eventTypeId}/slots`)
  if (!res.ok) {
    throw new Error(`Failed to get slots: ${res.status}`)
  }
  return res.json()
}
