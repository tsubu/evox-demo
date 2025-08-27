<template>
  <!-- このコンポーネントは見た目を表示しません -->
  <div style="display: none;"></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// 認証状態の監視
const authToken = ref(null)
const isAuthenticated = ref(false)
const sessionCheckInterval = ref(null)

// 認証状態を確認
const checkAuthStatus = async () => {
  const token = localStorage.getItem('auth_token')
  authToken.value = token
  isAuthenticated.value = !!token
  
  console.log('AuthMaintainer - Auth status check:', {
    token: token ? 'EXISTS' : 'NOT FOUND',
    isAuthenticated: isAuthenticated.value
  })
  
  // トークンが存在する場合は有効性を確認
  if (token) {
    try {
      const response = await fetch('/api/profile/check', {
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      })
      
      if (!response.ok) {
        console.log('AuthMaintainer - Token validation failed, clearing token')
        localStorage.removeItem('auth_token')
        authToken.value = null
        isAuthenticated.value = false
        return false
      }
      
      console.log('AuthMaintainer - Token validation successful')
    } catch (error) {
      console.log('AuthMaintainer - Token validation error:', error)
      // エラーの場合はトークンを削除しない（ネットワークエラーの可能性）
    }
  }
  
  return isAuthenticated.value
}

// セッション維持のための定期的なチェック
const startSessionMaintenance = () => {
  console.log('AuthMaintainer - Starting session maintenance')
  
  sessionCheckInterval.value = setInterval(() => {
    const currentRoute = router.currentRoute.value
    const currentToken = localStorage.getItem('auth_token')
    
    // トークンが変更された場合の処理
    if (currentToken !== authToken.value) {
      console.log('AuthMaintainer - Token changed, updating status')
      checkAuthStatus()
    }
    
    // トークンが失われた場合の処理
    if (!currentToken && isAuthenticated.value) {
      console.log('AuthMaintainer - Token lost, updating status')
      isAuthenticated.value = false
      authToken.value = null
      
      // 現在のページが認証が必要なページの場合のみ、ログインページにリダイレクト
      if (currentRoute.meta.requiresAuth) {
        console.log('AuthMaintainer - Redirecting to login from auth required page')
        router.push('/login')
      } else {
        console.log('AuthMaintainer - Not redirecting, current page does not require auth')
      }
    }
  }, 30000) // 30秒ごとにチェック
}

// セッション維持を停止
const stopSessionMaintenance = () => {
  if (sessionCheckInterval.value) {
    clearInterval(sessionCheckInterval.value)
    sessionCheckInterval.value = null
    console.log('AuthMaintainer - Session maintenance stopped')
  }
}

// コンポーネントマウント時
onMounted(() => {
  console.log('AuthMaintainer - Component mounted')
  checkAuthStatus()
  startSessionMaintenance()
})

// コンポーネントアンマウント時
onUnmounted(() => {
  console.log('AuthMaintainer - Component unmounted')
  stopSessionMaintenance()
})

// 外部からアクセス可能な関数をエクスポート
defineExpose({
  checkAuthStatus,
  isAuthenticated,
  authToken
})
</script>

<style scoped>
/* このコンポーネントは見た目を表示しないため、スタイルは不要 */
</style>
