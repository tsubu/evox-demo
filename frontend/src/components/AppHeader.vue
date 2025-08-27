<template>
  <header class="bg-evox-black border-b border-gray-800">
    <div class="max-w-7xl mx-auto px-6 py-4">
      <div class="flex justify-between items-center">
        <!-- ロゴ -->
        <router-link to="/" class="flex items-center">
          <img src="/images/logo_evox.png" alt="EvoX" class="h-12 w-auto" />
        </router-link>
        
        <!-- ナビゲーション -->
        <nav class="hidden md:flex space-x-8">
          <button @click="goToMyPage" class="text-white hover:text-evox-blue transition-all duration-300 transform hover:scale-110 link-slide" v-if="isAuthenticated">
            マイページ
          </button>
          <router-link to="/qr-scanner" class="text-white hover:text-evox-blue transition-all duration-300 transform hover:scale-110 link-slide" v-if="isAuthenticated">
            QR読み取り
          </router-link>
          <router-link to="/admin" class="text-white hover:text-evox-blue transition-all duration-300 transform hover:scale-110 link-slide" v-if="isAdmin">
            管理画面
          </router-link>
        </nav>
        
        <!-- アクションボタン -->
        <div class="flex items-center space-x-4">
          <router-link 
            v-if="!isAuthenticated && !isAuthPage"
            to="/login" 
            class="btn-secondary inline-block link-hover"
          >
            ログイン
          </router-link>
          <router-link 
            v-if="!isAuthenticated && !isAuthPage"
            to="/entry" 
            class="btn-primary inline-block link-hover"
          >
            事前登録
          </router-link>
          <button 
            v-if="isAuthenticated"
            @click="logout" 
            class="btn-secondary"
          >
            ログアウト
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import { ref, onMounted, watch, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export default {
  name: 'AppHeader',
  setup() {
    const router = useRouter()
    const route = useRoute()
    const isAuthenticated = ref(false)
    const isAdmin = ref(false)
    
    const checkAuth = () => {
      isAuthenticated.value = !!localStorage.getItem('auth_token')
      isAdmin.value = localStorage.getItem('user_role') === 'admin'
    }
    
    // 認証ページかどうかを判定
    const isAuthPage = computed(() => {
      const authPages = ['/login', '/entry', '/character-selection', '/nickname-setup']
      return authPages.includes(route.path)
    })
    
    // 認証状態をリアルタイムで監視
    const updateAuthStatus = () => {
      checkAuth()
    }
    
    // localStorageの変更を監視
    const handleStorageChange = (e) => {
      if (e.key === 'auth_token') {
        updateAuthStatus()
      }
    }
    
    const goToMyPage = () => {
      const authToken = localStorage.getItem('auth_token')
      if (authToken) {
        console.log('Going to mypage from header...')
        router.push('/mypage')
      } else {
        console.log('No auth token found, redirecting to login...')
        router.push('/login')
      }
    }
    
    const logout = () => {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user_role')
      localStorage.removeItem('user_id')
      isAuthenticated.value = false
      router.push('/')
    }
    
    onMounted(() => {
      checkAuth()
      
      // localStorageの変更を監視
      window.addEventListener('storage', handleStorageChange)
      
      // 定期的に認証状態をチェック（フォールバック）
      setInterval(checkAuth, 1000)
    })
    
    return {
      isAuthenticated,
      isAdmin,
      isAuthPage,
      goToMyPage,
      logout
    }
  }
}
</script>
