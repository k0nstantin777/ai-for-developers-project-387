import { getEventType, createEventType, getBookings, createBooking, getSlots } from './mock.js'

const BASE_URL = '/api'
const USE_MOCK = true

async function request(endpoint, options = {}) {
  const config = {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      ...options.headers,
    },
    ...options,
  }

  const response = await fetch(`${BASE_URL}${endpoint}`, config)

  if (!response.ok) {
    const error = await response.json().catch(() => ({ message: 'Request failed' }))
    throw new Error(error.message || `HTTP ${response.status}`)
  }

  return response.json()
}

export function get(endpoint) {
  if (USE_MOCK && endpoint === '/event-types') {
    return Promise.resolve(getEventType())
  }
  if (USE_MOCK && endpoint.startsWith('/event-types/') && endpoint.endsWith('/slots')) {
    return getSlots()
  }
  if (USE_MOCK && endpoint.startsWith('/event-types/') && !endpoint.endsWith('/slots')) {
    const id = parseInt(endpoint.split('/')[2])
    const items = getEventType()
    const item = items.find((e) => e.id === id)
    if (!item) throw new Error('Not found')
    return Promise.resolve(item)
  }
  if (USE_MOCK && endpoint === '/bookings') {
    return Promise.resolve(getBookings())
  }
  return request(endpoint, { method: 'GET' })
}

export function post(endpoint, data) {
  if (USE_MOCK && endpoint === '/event-types') {
    const item = createEventType(data)
    return Promise.resolve(item)
  }
  if (USE_MOCK && endpoint === '/bookings') {
    const item = createBooking(data)
    return Promise.resolve(item)
  }
  return request(endpoint, {
    method: 'POST',
    body: JSON.stringify(data),
  })
}
