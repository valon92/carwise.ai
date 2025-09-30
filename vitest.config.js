/// <reference types="vitest" />
import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  test: {
    environment: 'jsdom',
    include: [
      'tests/frontend/**/*.test.js',
      'tests/frontend/**/*.spec.js'
    ],
    setupFiles: ['tests/frontend/setup.js']
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js')
    }
  }
})
