<template>
  <v-container class="mt-4">
    <div class="d-flex align-center mb-4">
      <v-btn variant="text" icon="mdi-arrow-left" @click="router.push('/admin')" />
      <h2 class="text-h5">Upcoming Bookings</h2>
    </div>

    <v-row v-if="loading" justify="center">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-else-if="error" type="error" variant="tonal">{{ error }}</v-alert>

    <v-row v-else-if="items.length === 0" justify="center">
      <v-col cols="12" md="8">
        <v-card class="text-center py-8">
          <v-card-text class="text-grey">No bookings yet.</v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <v-row v-else>
      <v-col v-for="booking in sortedBookings" :key="booking.id" cols="12" md="6" lg="4">
        <v-card elevation="2" rounded="lg">
          <v-card-item>
            <v-card-title class="text-body-1">
              {{ booking.eventType?.name || 'Unknown event' }}
            </v-card-title>
            <v-card-subtitle>
              {{ dayjs(booking.startTime).format('ddd, D MMM YYYY') }}
              &mdash;
              {{ dayjs(booking.startTime).format('HH:mm') }}–{{ dayjs(booking.endTime).format('HH:mm') }}
            </v-card-subtitle>
          </v-card-item>
          <v-card-text>
            <div class="text-body-2">
              <v-icon size="small" class="mr-1">mdi-account</v-icon>
              {{ booking.guestName }}
            </div>
            <div class="text-body-2 text-grey">
              <v-icon size="small" class="mr-1">mdi-email</v-icon>
              {{ booking.guestEmail }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useBookings } from '@/composables/useBookings'
import dayjs from 'dayjs'

const router = useRouter()
const { items, loading, error, fetchAll } = useBookings()

const sortedBookings = computed(() =>
  [...items.value].sort((a, b) => new Date(a.startTime) - new Date(b.startTime))
)

onMounted(() => fetchAll())
</script>
