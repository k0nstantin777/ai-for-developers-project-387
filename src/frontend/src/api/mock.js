import dayjs from 'dayjs'

let _id = 0

function makeItem(type, payload = {}) {
  _id++
  const ts = new Date().toISOString()
  return { id: _id, ...payload, createdAt: ts, updatedAt: ts, ...(type === 'booking' ? {} : {}) }
}

const eventTypes = [
  { name: '30 Minute Meeting', description: 'Quick sync or introduction call', duration: 30 },
  { name: '60 Minute Consultation', description: 'Detailed project discussion', duration: 60 },
  { name: '15 Minute Check-in', description: 'Brief status update', duration: 15 },
].map((e) => makeItem('eventType', e))

const bookings = []

export function getEventType() {
  return eventTypes.map((e) => ({ ...e }))
}

export function createEventType(data) {
  const item = makeItem('eventType', { name: data.name, description: data.description, duration: data.duration })
  eventTypes.push(item)
  return { ...item }
}

export function getBookings() {
  return bookings.map((b) => ({
    ...b,
    eventType: eventTypes.find((e) => e.id === b.eventTypeId) || null,
  }))
}

export function createBooking(data) {
  const eventType = eventTypes.find((e) => e.id === data.eventTypeId)
  if (!eventType) {
    throw new Error('Event type not found')
  }

  const startTime = new Date(data.startTime)
  const endTime = new Date(startTime.getTime() + eventType.duration * 60000)

  const conflict = bookings.some((b) => {
    const bStart = new Date(b.startTime)
    const bEnd = new Date(b.endTime)
    return startTime < bEnd && endTime > bStart
  })

  if (conflict) {
    throw new Error('Slot is already booked')
  }

  const item = makeItem('booking', {
    eventTypeId: data.eventTypeId,
    guestName: data.guestName,
    guestEmail: data.guestEmail,
    startTime: data.startTime,
    endTime: endTime.toISOString(),
  })

  bookings.push(item)
  return { ...item, eventType: { ...eventType } }
}

export function getSlots() {
  const now = dayjs()
  const slots = []

  for (let d = 0; d < 14; d++) {
    const date = now.add(d, 'day')
    for (let h = 9; h < 18; h++) {
      const start = date.hour(h).minute(0).second(0)
      const end = start.add(30, 'minute')

      const conflict = bookings.some((b) => {
        const bStart = dayjs(b.startTime)
        const bEnd = dayjs(b.endTime)
        return start.isBefore(bEnd) && end.isAfter(bStart)
      })

      if (!conflict && start.isAfter(now)) {
        slots.push({
          startTime: start.toISOString(),
          endTime: end.toISOString(),
        })
      }
    }
  }

  return { slots }
}
