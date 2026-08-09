import dayjs from 'dayjs'

export function groupSlotsByDate(slots) {
  const grouped = {}
  for (const slot of slots) {
    const dateKey = dayjs(slot.startTime).format('YYYY-MM-DD')
    if (!grouped[dateKey]) grouped[dateKey] = []
    grouped[dateKey].push(slot)
  }
  return Object.entries(grouped).map(([date, slots]) => ({ date, slots }))
}

export function formatTime(iso) {
  return dayjs(iso).format('HH:mm')
}

export function formatDate(iso) {
  return dayjs(iso).format('ddd, D MMM')
}
