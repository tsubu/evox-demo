import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    },
  },
  server: {
    port: 3000,
    proxy: {
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin-ai8edq64p2i5': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/dashboard': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/users': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/news': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/qrcodes': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/stats': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/logout': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/users/': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/news/': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
      '/admin/qrcodes/': {
        target: 'http://localhost:8000',
        changeOrigin: true,
      },
    },
  },
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
  },
})
