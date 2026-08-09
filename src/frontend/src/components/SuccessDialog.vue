<template>
  <v-dialog :model-value="modelValue" @update:model-value="$emit('update:modelValue', $event)" max-width="480">
    <v-card>
      <v-card-title class="text-success d-flex align-center">
        <v-icon start color="success">mdi-check-circle</v-icon>
        {{ title }}
      </v-card-title>
      <v-card-text>{{ message }}</v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn color="success" variant="flat" @click="$emit('confirm')">OK</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Success' },
  message: { type: String, default: '' },
})
defineEmits(['update:modelValue', 'confirm'])
</script>
