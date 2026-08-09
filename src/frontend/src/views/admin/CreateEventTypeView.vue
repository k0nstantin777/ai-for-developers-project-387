<template>
  <v-container class="mt-4">
    <v-row justify="center">
      <v-col cols="12" md="8" lg="6">
        <v-card elevation="3" rounded="lg">
          <v-card-title class="text-h5">Create Event Type</v-card-title>
          <v-card-text>
            <v-form ref="formRef" v-model="valid" @submit.prevent="handleSubmit">
              <v-text-field
                v-model="name"
                label="Name"
                :rules="[v => !!v || 'Name is required']"
                variant="outlined"
                class="mb-3"
              />
              <v-textarea
                v-model="description"
                label="Description"
                :rules="[v => !!v || 'Description is required']"
                variant="outlined"
                rows="3"
                class="mb-3"
              />
              <v-text-field
                v-model.number="duration"
                label="Duration (minutes)"
                type="number"
                :rules="[
                  v => !!v || 'Duration is required',
                  v => v >= 5 || 'Min 5 minutes',
                  v => v <= 480 || 'Max 480 minutes',
                ]"
                variant="outlined"
                class="mb-3"
              />
              <div class="d-flex justify-end mt-4">
                <v-btn variant="text" class="mr-2" @click="router.push('/admin')">Cancel</v-btn>
                <v-btn color="primary" variant="flat" :loading="loading" type="submit">Create</v-btn>
              </div>
            </v-form>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <SuccessDialog
      v-model="showSuccess"
      title="Event Type Created"
      :message="successMessage"
      @confirm="onSuccessConfirm"
    />
    <ErrorDialog
      v-model="showError"
      title="Creation Failed"
      :message="errorMessage"
      @confirm="showError = false"
    />
  </v-container>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useEventTypes } from '@/composables/useEventTypes'
import SuccessDialog from '@/components/SuccessDialog.vue'
import ErrorDialog from '@/components/ErrorDialog.vue'

const router = useRouter()
const { create, loading } = useEventTypes()

const valid = ref(false)
const name = ref('')
const description = ref('')
const duration = ref(30)

const showSuccess = ref(false)
const showError = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

async function handleSubmit() {
  if (!valid.value) return
  try {
    const result = await create({
      name: name.value,
      description: description.value,
      duration: duration.value,
    })
    successMessage.value = `Event type "${result.name}" created successfully.`
    showSuccess.value = true
  } catch (e) {
    errorMessage.value = e.message || 'Failed to create event type.'
    showError.value = true
  }
}

function onSuccessConfirm() {
  showSuccess.value = false
  router.push('/admin/bookings')
}
</script>
