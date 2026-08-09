<template>
  <v-container class="mt-4">
    <h2 class="text-h5 mb-4 text-center">Select Event Type</h2>

    <v-row v-if="loading" justify="center">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-else-if="error" type="error" variant="tonal">{{ error }}</v-alert>

    <v-row>
      <v-col v-for="et in items" :key="et.id" cols="12" md="6" lg="4">
        <v-card elevation="3" rounded="lg">
          <v-card-item>
            <v-card-title>{{ et.name }}</v-card-title>
            <v-card-subtitle>
              <v-icon size="small" class="mr-1">mdi-clock-outline</v-icon>
              {{ et.duration }} min
            </v-card-subtitle>
          </v-card-item>
          <v-card-text class="text-body-2 text-grey-darken-1">
            {{ et.description }}
          </v-card-text>
          <v-card-actions>
            <v-spacer />
            <v-btn color="primary" variant="flat" :to="`/guest/event-types/${et.id}/booking`">
              Book
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script setup>
import { onMounted } from 'vue'
import { useEventTypes } from '@/composables/useEventTypes'

const { items, loading, error, fetchAll } = useEventTypes()

onMounted(() => fetchAll())
</script>
