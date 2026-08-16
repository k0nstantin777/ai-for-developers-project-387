import { test, expect } from '@playwright/test'
import { resetDatabase, createEventType, createBooking } from './helpers/api.js'

test.describe('Admin', () => {
  test.beforeEach(async ({ page }) => {
    await resetDatabase()
    await page.goto('/')
  })

  function enterAdmin(page) {
    return page.getByRole('button', { name: 'Enter' }).first().click()
  }

  test('should create event type successfully', async ({ page }) => {
    await enterAdmin(page)
    await page.getByRole('button', { name: 'Go' }).first().click()

    await expect(page.getByText('Create Event Type')).toBeVisible()

    await page.getByLabel('Name').fill('Code Review Session')
    await page.getByLabel('Description').fill('Weekly code review meeting')
    await page.getByLabel('Duration (minutes)').fill('45')

    await page.getByRole('button', { name: 'Create' }).click()

    await expect(page.getByText('Event Type Created', { exact: true })).toBeVisible()

    await page.getByRole('button', { name: 'OK' }).click()
    await expect(page).toHaveURL(/\/admin\/bookings/)
  })

  test('should validate empty name on create', async ({ page }) => {
    await enterAdmin(page)
    await page.getByRole('button', { name: 'Go' }).first().click()

    await page.getByLabel('Description').fill('Some description')
    await page.getByLabel('Duration (minutes)').fill('30')

    await page.getByRole('button', { name: 'Create' }).click()

    await expect(page.getByText('Name is required')).toBeVisible()
  })

  test('should validate duration below minimum', async ({ page }) => {
    await enterAdmin(page)
    await page.getByRole('button', { name: 'Go' }).first().click()

    await page.getByLabel('Name').fill('Quick Chat')
    await page.getByLabel('Description').fill('Quick sync')
    await page.getByLabel('Duration (minutes)').fill('3')

    await page.getByRole('button', { name: 'Create' }).click()

    await expect(page.getByText('Min 5 minutes')).toBeVisible()
  })

  test('should validate duration above maximum', async ({ page }) => {
    await enterAdmin(page)
    await page.getByRole('button', { name: 'Go' }).first().click()

    await page.getByLabel('Name').fill('Marathon')
    await page.getByLabel('Description').fill('Too long')
    await page.getByLabel('Duration (minutes)').fill('500')

    await page.getByRole('button', { name: 'Create' }).click()

    await expect(page.getByText('Max 480 minutes')).toBeVisible()
  })

  test('should show empty bookings list', async ({ page }) => {
    await page.goto('/admin/bookings')

    await expect(page.getByText('No bookings yet.')).toBeVisible()
  })

  test('should show bookings with data', async ({ page }) => {
    const eventType = await createEventType({
      name: 'Admin View Test',
      description: 'For admin booking view test',
      duration: 30,
    })

    const booking = await createBooking({
      eventTypeId: eventType.id,
      guestName: 'Alice Johnson',
      guestEmail: 'alice@example.com',
      startTime: '2027-03-15T10:00:00Z',
    })

    await page.goto('/admin/bookings')

    await expect(page.getByText('Alice Johnson')).toBeVisible()
    await expect(page.getByText('alice@example.com')).toBeVisible()
    await expect(page.getByText('Admin View Test')).toBeVisible()
  })
})
