import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    name: 'home',
    component: () => import('../views/HomeView.vue'),
  },
  {
    path: '/admin',
    name: 'admin-dashboard',
    component: () => import('../views/admin/AdminDashboardView.vue'),
  },
  {
    path: '/admin/event-types/create',
    name: 'admin-event-types-create',
    component: () => import('../views/admin/CreateEventTypeView.vue'),
  },
  {
    path: '/admin/bookings',
    name: 'admin-bookings',
    component: () => import('../views/admin/AdminBookingsView.vue'),
  },
  {
    path: '/guest/event-types',
    name: 'guest-event-types',
    component: () => import('../views/guest/GuestEventTypesView.vue'),
  },
  {
    path: '/guest/event-types/:id/booking',
    name: 'guest-booking',
    component: () => import('../views/guest/GuestBookingView.vue'),
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
