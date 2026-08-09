<template>
  <v-container class="mt-4">
    <h2 class="text-h4 mb-6 text-center">Admin Panel</h2>
    <v-row justify="center">
      <v-col cols="12" md="5">
        <v-card elevation="3" rounded="lg" @click="goToCreate">
          <v-card-item>
            <template #prepend>
              <v-icon size="40" color="primary">mdi-plus-box</v-icon>
            </template>
            <v-card-title>Create Event Type</v-card-title>
            <v-card-subtitle>Add a new meeting type</v-card-subtitle>
          </v-card-item>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" variant="text">Go</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="5">
        <v-card elevation="3" rounded="lg" @click="goToBookings">
          <v-card-item>
            <template #prepend>
              <v-icon size="40" color="secondary">mdi-calendar-check</v-icon>
            </template>
            <v-card-title>Upcoming Bookings</v-card-title>
            <v-card-subtitle>View all scheduled meetings</v-card-subtitle>
          </v-card-item>
          <v-card-actions>
            <v-spacer />
            <v-btn color="secondary" variant="text">Go</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

function goToCreate() { router.push('/admin/event-types/create') }
function goToBookings() { router.push('/admin/bookings') }
</script>
