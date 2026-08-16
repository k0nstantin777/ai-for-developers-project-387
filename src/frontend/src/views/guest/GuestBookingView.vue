<template>
  <v-container class="mt-4">
    <div class="d-flex align-center mb-4">
      <v-btn variant="text" icon="mdi-arrow-left" @click="router.push('/guest/event-types')" />
      <h2 class="text-h5">{{ eventType?.name || 'Book a Slot' }}</h2>
    </div>

    <v-alert v-if="eventType" type="info" variant="tonal" class="mb-4">
      <strong>{{ eventType.name }}</strong> &mdash; {{ eventType.description }} ({{ eventType.duration }} min)
    </v-alert>

    <v-row v-if="slotsLoading" justify="center">
      <v-progress-circular indeterminate color="primary" />
    </v-row>

    <v-alert v-else-if="slotsError" type="error" variant="tonal">{{ slotsError }}</v-alert>

    <template v-else>
      <v-row v-if="!selectedSlot">
        <v-col cols="12">
          <h3 class="text-subtitle-1 mb-2">Available slots (next 14 days)</h3>
        </v-col>
        <v-col v-for="group in groupedSlots" :key="group.date" cols="12">
          <v-card class="mb-3" elevation="1">
            <v-card-title class="text-subtitle-2 bg-grey-lighten-3 py-2">
              {{ formatDate(group.date) }}
            </v-card-title>
            <v-card-text class="pt-3">
              <div class="d-flex flex-wrap gap-2">
                <v-chip
                  v-for="slot in group.slots"
                  :key="slot.startTime"
                  label
                  color="primary"
                  variant="outlined"
                  class="cursor-pointer"
                  @click="selectSlot(slot)"
                >
                  {{ formatTime(slot.startTime) }}&ndash;{{ formatTime(slot.endTime) }}
                </v-chip>
              </div>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>

      <v-row v-else justify="center">
        <v-col cols="12" md="6">
          <v-card elevation="3" rounded="lg">
            <v-card-title>Confirm Booking</v-card-title>
            <v-card-subtitle>
              {{ formatDateTime(selectedSlot.startTime) }} &ndash; {{ formatTime(selectedSlot.endTime) }}
            </v-card-subtitle>
            <v-card-text>
              <v-form v-model="formValid" @submit.prevent="handleBooking">
                <v-text-field
                  v-model="guestName"
                  label="Your name"
                  aria-label="Your name"
                  :rules="[v => !!v || 'Name is required']"
                  variant="outlined"
                  class="mb-3"
                />
                <v-text-field
                  v-model="guestEmail"
                  label="Your email"
                  aria-label="Your email"
                  type="email"
                  :rules="[
                    v => !!v || 'Email is required',
                    v => /.+@.+\..+/.test(v) || 'Invalid email',
                  ]"
                  variant="outlined"
                  class="mb-3"
                />
                <div class="d-flex justify-end mt-4">
                  <v-btn variant="text" class="mr-2" @click="resetSelection">Back</v-btn>
                  <v-btn color="primary" variant="flat" type="submit" :loading="bookingLoading">
                    Confirm
                  </v-btn>
                </div>
              </v-form>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </template>

    <SuccessDialog
      v-model="showSuccess"
      title="Booking Confirmed"
      :message="successMessage"
      @confirm="onSuccessConfirm"
    />
    <ErrorDialog
      v-model="showError"
      title="Booking Failed"
      :message="errorMessage"
      @confirm="showError = false"
    />
  </v-container>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useRoute } from 'vue-router'
import { useSlots } from '@/composables/useSlots'
import { useBookings } from '@/composables/useBookings'
import { useEventTypes } from '@/composables/useEventTypes'
import { groupSlotsByDate, formatTime, formatDate } from '@/composables/useSlotUtils'
import dayjs from 'dayjs'
import SuccessDialog from '@/components/SuccessDialog.vue'
import ErrorDialog from '@/components/ErrorDialog.vue'

const router = useRouter()
const route = useRoute()
const eventTypeId = Number(route.params.id)

const { slots, loading: slotsLoading, error: slotsError, fetchSlots } = useSlots()
const { create: createBooking, loading: bookingLoading } = useBookings()
const { fetchById } = useEventTypes()

const eventType = ref(null)
const selectedSlot = ref(null)

const guestName = ref('')
const guestEmail = ref('')
const formValid = ref(false)

const showSuccess = ref(false)
const showError = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const groupedSlots = computed(() => groupSlotsByDate(slots.value))

function formatDateTime(iso) {
  return dayjs(iso).format('ddd, D MMM YYYY HH:mm')
}

function selectSlot(slot) {
  selectedSlot.value = slot
}

function resetSelection() {
  selectedSlot.value = null
  guestName.value = ''
  guestEmail.value = ''
}

async function handleBooking() {
  if (!formValid.value) return
  try {
    const result = await createBooking({
      eventTypeId,
      guestName: guestName.value,
      guestEmail: guestEmail.value,
      startTime: selectedSlot.value.startTime,
    })
    successMessage.value = `Booking confirmed for ${dayjs(result.startTime).format('ddd, D MMM YYYY HH:mm')}`
    showSuccess.value = true
  } catch (e) {
    errorMessage.value = e.message || 'Failed to create booking.'
    showError.value = true
  }
}

function onSuccessConfirm() {
  showSuccess.value = false
  router.push('/guest/event-types')
}

onMounted(async () => {
  eventType.value = await fetchById(eventTypeId)
  await fetchSlots(eventTypeId)
})
</script>

<style scoped>
.gap-2 { gap: 8px; }
.cursor-pointer { cursor: pointer; }
</style>
