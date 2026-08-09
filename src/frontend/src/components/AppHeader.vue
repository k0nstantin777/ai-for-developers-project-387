<template>
  <v-app-bar color="primary" density="compact" elevation="2">
    <v-app-bar-title class="cursor-pointer" @click="goHome">
      Call me in time
    </v-app-bar-title>
    <v-spacer />
    <span class="text-body-2 mr-4">{{ today }}</span>
    <v-chip :color="modeColor" size="small" class="mr-2" variant="flat">
      {{ modeLabel }}
    </v-chip>
    <v-btn variant="outlined" size="small" @click="goToModeDash">
      Dashboard
    </v-btn>
  </v-app-bar>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import dayjs from 'dayjs'

const router = useRouter()
const store = useAppStore()
const today = computed(() => dayjs().format('D MMMM YYYY'))
const modeLabel = computed(() => store.mode === 'admin' ? 'Admin' : 'Guest')
const modeColor = computed(() => store.mode === 'admin' ? 'primary' : 'success')

function goHome() { router.push('/') }
function goToModeDash() {
  router.push(store.mode === 'admin' ? '/admin' : '/guest/event-types')
}
</script>

<style scoped>
.cursor-pointer { cursor: pointer; }
</style>
