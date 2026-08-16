import { test, expect } from '@playwright/test'
import { resetDatabase, createEventType, getSlots } from './helpers/api.js'

test.describe('Guest', () => {
  let eventType

  test.beforeEach(async ({ page }) => {
    await resetDatabase()
    eventType = await createEventType({
      name: '30 Minute Meeting',
      description: 'A quick 30-minute sync',
      duration: 30,
    })
    await page.goto('/')
  })

  function enterGuest(page) {
    return page.getByRole('button', { name: 'Enter' }).last().click()
  }

  test('should browse event types', async ({ page }) => {
    await enterGuest(page)

    await expect(page.getByText('Select Event Type')).toBeVisible({ timeout: 15000 })
    await expect(page.getByText('30 Minute Meeting')).toBeVisible()
    await expect(page.getByText('A quick 30-minute sync')).toBeVisible()
    await expect(page.getByText(/30 min/).first()).toBeVisible()
    await expect(page.getByRole('link', { name: 'Book' })).toBeVisible()
  })

  test('should view available slots', async ({ page }) => {
    await enterGuest(page)
    await page.getByRole('link', { name: 'Book' }).click()

    await expect(page.getByText('Available slots (next 14 days)')).toBeVisible({ timeout: 15000 })
    await expect(page.getByText('30 Minute Meeting').first()).toBeVisible()

    const slots = await getSlots(eventType.id)
    expect(slots.slots.length).toBeGreaterThan(0)

    await expect(page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first()).toBeVisible()
  })

  test('should book a slot successfully', async ({ page }) => {
    await enterGuest(page)
    await page.getByRole('link', { name: 'Book' }).click()

    await expect(page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first()).toBeVisible({ timeout: 15000 })

    const firstChip = page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first()
    const slotText = await firstChip.textContent()
    await firstChip.click()

    await expect(page.getByText('Confirm Booking')).toBeVisible()

    await page.locator('#booking-name').fill('John Doe')
    await page.locator('#booking-email').fill('john@example.com')

    await page.getByRole('button', { name: 'Confirm' }).click()

    await expect(page.getByText('Booking Confirmed', { exact: true })).toBeVisible()

    await page.goto('/guest/event-types')
    await expect(page.getByText('Select Event Type')).toBeVisible()
  })

  test('should validate empty name on booking', async ({ page }) => {
    await enterGuest(page)
    await page.getByRole('link', { name: 'Book' }).click()

    await page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first().click()

    await expect(page.getByText('Confirm Booking')).toBeVisible()

    await page.locator('#booking-email').fill('john@example.com')
    await page.getByRole('button', { name: 'Confirm' }).click()

    await expect(page.getByText('Name is required')).toBeVisible()
  })

  test('should validate invalid email on booking', async ({ page }) => {
    await enterGuest(page)
    await page.getByRole('link', { name: 'Book' }).click()

    await page.getByText(/\d{2}:\d{2}–\d{2}:\d{2}/).first().click()

    await expect(page.getByText('Confirm Booking')).toBeVisible()

    await page.locator('#booking-name').fill('John Doe')
    await page.locator('#booking-email').fill('not-an-email')
    await page.getByRole('button', { name: 'Confirm' }).click()

    await expect(page.getByText('Invalid email')).toBeVisible()
  })

  test('should navigate back from booking page', async ({ page }) => {
    await enterGuest(page)
    await page.getByRole('link', { name: 'Book' }).click()

    await expect(page.getByText('Available slots (next 14 days)')).toBeVisible({ timeout: 15000 })

    await page.locator('.mdi-arrow-left').click()

    await expect(page).toHaveURL('/guest/event-types')
    await expect(page.getByText('Select Event Type')).toBeVisible()
  })
})
