import { test, expect } from '@playwright/test'
import { resetDatabase, createEventType, createBooking, getSlots } from './helpers/api.js'

test.describe('Cross-role scenarios', () => {
  test.beforeEach(async () => {
    await resetDatabase()
  })

  test('full booking cycle: admin creates event → guest books → admin sees booking', async ({ page }) => {
    await page.goto('/')

    await page.getByRole('button', { name: 'Enter' }).first().click()
    await page.getByRole('button', { name: 'Go' }).first().click()

    await page.locator('#event-name').fill('Strategy Session')
    await page.locator('#event-description').fill('Long-term planning')
    await page.locator('#event-duration').fill('60')
    await page.getByRole('button', { name: 'Create' }).click()
    await expect(page.getByText('Event Type Created', { exact: true })).toBeVisible()
    await page.getByRole('button', { name: 'OK' }).click()
    await expect(page).toHaveURL('/admin/bookings')
    await expect(page.getByText('No bookings yet.')).toBeVisible()

    await page.goto('/')
    await page.getByRole('button', { name: 'Enter' }).last().click()
    await expect(page.getByRole('link', { name: 'Book' })).toBeVisible({ timeout: 15000 })
    await page.getByRole('link', { name: 'Book' }).click()

    await expect(page.getByRole('heading', { name: 'Strategy Session' })).toBeVisible({ timeout: 15000 })
    await expect(page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first()).toBeVisible({ timeout: 15000 })

    const firstChip = page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first()
    const slotText = await firstChip.textContent()
    await firstChip.click()

    await page.locator('#booking-name').fill('Bob Smith')
    await page.locator('#booking-email').fill('bob@example.com')
    await page.getByRole('button', { name: 'Confirm' }).click()

    await expect(page.getByText('Booking Confirmed', { exact: true })).toBeVisible()
    await page.getByRole('button', { name: 'OK' }).click()

    await page.goto('/admin/bookings')

    await expect(page.getByText('Bob Smith')).toBeVisible({ timeout: 15000 })
    await expect(page.getByText('bob@example.com')).toBeVisible()
    await expect(page.getByText('Strategy Session')).toBeVisible()
  })

  test('slots update after booking', async ({ page }) => {
    const eventType = await createEventType({
      name: 'Team Standup',
      description: 'Daily sync',
      duration: 30,
    })

    const slots = await getSlots(eventType.id)
    const firstSlot = slots.slots[0]
    await createBooking({
      eventTypeId: eventType.id,
      guestName: 'Test User',
      guestEmail: 'test@example.com',
      startTime: firstSlot.startTime,
    })

    await page.goto('/')
    await page.getByRole('button', { name: 'Enter' }).last().click()
    await page.getByRole('link', { name: 'Book' }).click()

    await expect(page.getByText('Team Standup')).toBeVisible({ timeout: 15000 })
    await expect(page.getByText('Available slots (next 14 days)')).toBeVisible({ timeout: 15000 })

    await expect(page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/)).not.toHaveCount(0)
  })

  test('booking conflict is rejected by API', async () => {
    const eventType = await createEventType({
      name: 'Conflict Test',
      description: 'Testing overlaps',
      duration: 30,
    })

    const slots = await getSlots(eventType.id)
    const firstSlot = slots.slots[0]

    await createBooking({
      eventTypeId: eventType.id,
      guestName: 'First User',
      guestEmail: 'first@example.com',
      startTime: firstSlot.startTime,
    })

    await expect(
      createBooking({
        eventTypeId: eventType.id,
        guestName: 'Second User',
        guestEmail: 'second@example.com',
        startTime: firstSlot.startTime,
      })
    ).rejects.toThrow()
  })
})
