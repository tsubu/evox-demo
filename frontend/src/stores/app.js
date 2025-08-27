import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { DESIGN_CONFIG } from '@/config/design'

export const useAppStore = defineStore('app', () => {
  // アプリケーション状態
  const isLoading = ref(false)
  const isAuthenticated = ref(false)
  const currentUser = ref(null)
  const notifications = ref([])
  const theme = ref('dark') // 'dark' | 'light'
  const language = ref('ja') // 'ja' | 'en'
  
  // デザイン設定
  const designConfig = ref(DESIGN_CONFIG)
  
  // 機能フラグ
  const featureFlags = ref({
    qrScanner: true,
    notifications: true,
    analytics: true,
    socialLogin: false,
    pwa: false
  })

  // 計算プロパティ
  const isAdmin = computed(() => {
    return currentUser.value?.role === 'admin'
  })

  const userPoints = computed(() => {
    return currentUser.value?.points || 0
  })

  const hasNotifications = computed(() => {
    return notifications.value.length > 0
  })

  // アクション
  const setLoading = (loading) => {
    isLoading.value = loading
  }

  const setAuthenticated = (authenticated, user = null) => {
    isAuthenticated.value = authenticated
    currentUser.value = user
  }

  const updateUser = (userData) => {
    if (currentUser.value) {
      currentUser.value = { ...currentUser.value, ...userData }
    }
  }

  const addNotification = (notification) => {
    const id = Date.now().toString()
    const newNotification = {
      id,
      type: 'info',
      title: '',
      message: '',
      duration: 5000,
      ...notification
    }
    
    notifications.value.push(newNotification)
    
    // 自動削除
    if (newNotification.duration > 0) {
      setTimeout(() => {
        removeNotification(id)
      }, newNotification.duration)
    }
    
    return id
  }

  const removeNotification = (id) => {
    const index = notifications.value.findIndex(n => n.id === id)
    if (index > -1) {
      notifications.value.splice(index, 1)
    }
  }

  const clearNotifications = () => {
    notifications.value = []
  }

  const setTheme = (newTheme) => {
    theme.value = newTheme
    // テーマ変更時の処理（CSS変数の更新など）
    document.documentElement.setAttribute('data-theme', newTheme)
  }

  const setLanguage = (newLanguage) => {
    language.value = newLanguage
    // 言語変更時の処理
  }

  const updateDesignConfig = (newConfig) => {
    designConfig.value = { ...designConfig.value, ...newConfig }
  }

  const toggleFeatureFlag = (feature) => {
    if (featureFlags.value.hasOwnProperty(feature)) {
      featureFlags.value[feature] = !featureFlags.value[feature]
    }
  }

  const logout = () => {
    setAuthenticated(false, null)
    clearNotifications()
    // その他のクリーンアップ処理
  }

  // ユーティリティ関数
  const showSuccess = (message, title = '成功') => {
    return addNotification({
      type: 'success',
      title,
      message
    })
  }

  const showError = (message, title = 'エラー') => {
    return addNotification({
      type: 'error',
      title,
      message
    })
  }

  const showWarning = (message, title = '警告') => {
    return addNotification({
      type: 'warning',
      title,
      message
    })
  }

  const showInfo = (message, title = 'お知らせ') => {
    return addNotification({
      type: 'info',
      title,
      message
    })
  }

  return {
    // 状態
    isLoading,
    isAuthenticated,
    currentUser,
    notifications,
    theme,
    language,
    designConfig,
    featureFlags,
    
    // 計算プロパティ
    isAdmin,
    userPoints,
    hasNotifications,
    
    // アクション
    setLoading,
    setAuthenticated,
    updateUser,
    addNotification,
    removeNotification,
    clearNotifications,
    setTheme,
    setLanguage,
    updateDesignConfig,
    toggleFeatureFlag,
    logout,
    
    // ユーティリティ
    showSuccess,
    showError,
    showWarning,
    showInfo
  }
})
