<template>
  <v-footer app class="bg-grey-lighten-3 justify-center">
    <span class="text-caption text-grey-darken-1">
      Call me in time &copy; {{ year }}
    </span>
  </v-footer>
</template>

<script setup>
import { computed } from 'vue'
import dayjs from 'dayjs'

const year = computed(() => dayjs().format('YYYY'))
</script>
