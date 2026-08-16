import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'
import { fileURLToPath, URL } from 'node:url'

const apiTarget = process.env.VITE_API_TARGET === 'backend'
  ? 'http://backend:8000'
  : 'http://prism:4010'

const apiRewrite = process.env.VITE_API_TARGET === 'backend'
  ? undefined
  : (path) => path.replace(/^\/api/, '')

export default defineConfig({
  plugins: [
    vue(),
    vuetify({ autoImport: true }),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
    },
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    allowedHosts: ['frontend', 'localhost'],
    proxy: {
      '/api': {
        target: apiTarget,
        rewrite: apiRewrite,
      },
    },
  },
})
