import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import App from './App.vue'
import './assets/main.css'
import { useAppStore } from './stores/app'

const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
app.use(router)

// 認証状態の初期化
const appStore = useAppStore()
const token = localStorage.getItem('auth_token')
if (token) {
  appStore.setAuthenticated(true, null) // ユーザー情報は後で取得
} else {
  appStore.setAuthenticated(false, null)
}

app.mount('#app')
