<template>
  <v-container class="fill-height">
    <v-row justify="center" align="center">
      <v-col cols="12" md="5" lg="4">
        <v-card class="mx-auto" elevation="4" rounded="lg">
          <v-card-item>
            <template #prepend>
              <v-icon size="48" color="primary">mdi-shield-account</v-icon>
            </template>
            <v-card-title>Admin</v-card-title>
            <v-card-subtitle>Calendar Owner</v-card-subtitle>
          </v-card-item>
          <v-card-text>
            Manage event types and view all upcoming bookings.
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" variant="flat" @click="enterAdmin">Enter</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
      <v-col cols="12" md="5" lg="4">
        <v-card class="mx-auto" elevation="4" rounded="lg">
          <v-card-item>
            <template #prepend>
              <v-icon size="48" color="success">mdi-account-clock</v-icon>
            </template>
            <v-card-title>Guest</v-card-title>
            <v-card-subtitle>Book a Slot</v-card-subtitle>
          </v-card-item>
          <v-card-text>
            Browse event types and pick a time that works for you.
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="success" variant="flat" @click="enterGuest">Enter</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'

const router = useRouter()
const store = useAppStore()

function enterAdmin() {
  store.setMode('admin')
  router.push('/admin')
}

function enterGuest() {
  store.setMode('guest')
  router.push('/guest/event-types')
}
</script>
