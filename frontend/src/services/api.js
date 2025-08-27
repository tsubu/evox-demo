import axios from 'axios'
import { useAppStore } from '@/stores/app'

// API設定
const API_CONFIG = {
  baseURL: '/api', // プロキシを使用
  timeout: 10000,
  retryAttempts: 3,
  retryDelay: 1000
}

// Axiosインスタンス作成
const apiClient = axios.create({
  baseURL: API_CONFIG.baseURL,
  timeout: API_CONFIG.timeout,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  withCredentials: true // Sanctum認証用
})

// リクエストインターセプター
apiClient.interceptors.request.use(
  (config) => {
    const appStore = useAppStore()
    
    // ローディング状態の管理
    if (!config.skipLoading) {
      appStore.setLoading(true)
    }
    
    // 認証トークンの追加（必要に応じて）
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    
    // リクエストログ（開発環境のみ）
    if (import.meta.env.DEV) {
      console.log('API Request:', config.method?.toUpperCase(), config.url, config.data)
    }
    
    return config
  },
  (error) => {
    const appStore = useAppStore()
    appStore.setLoading(false)
    return Promise.reject(error)
  }
)

// レスポンスインターセプター
apiClient.interceptors.response.use(
  (response) => {
    const appStore = useAppStore()
    appStore.setLoading(false)
    
    // レスポンスログ（開発環境のみ）
    if (import.meta.env.DEV) {
      console.log('API Response:', response.status, response.data)
    }
    
    return response
  },
  async (error) => {
    const appStore = useAppStore()
    appStore.setLoading(false)
    
    // エラーログ（登録済みエラーは除外）
    if (error.response?.data?.code !== 'ALREADY_REGISTERED') {
      console.error('API Error:', {
        status: error.response?.status,
        statusText: error.response?.statusText,
        data: error.response?.data,
        url: error.config?.url,
        method: error.config?.method
      })
    }
    
    // 認証エラーの処理
    if (error.response?.status === 401) {
      appStore.setAuthenticated(false, null)
      localStorage.removeItem('auth_token')
      // TOPページへのリダイレクト（ページリロードを避ける）
      if (window.location.pathname !== '/') {
        window.location.href = '/'
      }
    }
    
    // 登録済みエラーの処理（コンポーネントレベルで処理するため、ここでは何もしない）
    if (error.response?.data?.code === 'ALREADY_REGISTERED') {
      return Promise.reject(error)
    }
    
    // サーバーエラーの処理
    if (error.response?.status >= 500) {
      appStore.showError('サーバーエラーが発生しました。しばらく時間をおいて再度お試しください。')
    }
    
    return Promise.reject(error)
  }
)

// リトライ機能付きAPI呼び出し
const apiCallWithRetry = async (apiCall, retryCount = 0) => {
  try {
    return await apiCall()
  } catch (error) {
    if (retryCount < API_CONFIG.retryAttempts && 
        (error.response?.status >= 500 || !error.response)) {
      await new Promise(resolve => setTimeout(resolve, API_CONFIG.retryDelay * (retryCount + 1)))
      return apiCallWithRetry(apiCall, retryCount + 1)
    }
    throw error
  }
}

// API メソッド
export const api = {
  // 認証関連
  auth: {
    login: (data) => apiCallWithRetry(() => apiClient.post('/login', data)),
    verifyLoginOtp: (data) => apiCallWithRetry(() => apiClient.post('/login/verify-otp', data)),
    logout: () => apiCallWithRetry(() => apiClient.post('/logout')),
    me: () => apiCallWithRetry(() => apiClient.get('/auth/me')),
    verifyOtp: (data) => apiCallWithRetry(() => apiClient.post('/verify-otp', data)),
    forgotPassword: (data) => apiCallWithRetry(() => apiClient.post('/auth/forgot-password', data)),
    resetPassword: (data) => apiCallWithRetry(() => apiClient.post('/auth/reset-password', data))
  },
  
  // 国情報関連
  country: {
    getCountries: () => apiCallWithRetry(() => apiClient.get('/countries')),
    detectCountry: () => apiCallWithRetry(() => apiClient.get('/detect-country'))
  },
  
  // ユーザー登録関連
  entry: {
    store: (data) => apiCallWithRetry(() => apiClient.post('/entry', data)),
    verify: (data) => apiCallWithRetry(() => apiClient.post('/entry/verify', data)),
    setPassword: (data) => apiCallWithRetry(() => apiClient.post('/entry/set-password', data)),
    complete: (data) => apiCallWithRetry(() => apiClient.post('/entry/complete', data))
  },
  
  // 登録者数
  registrations: {
    count: () => apiCallWithRetry(() => apiClient.get('/registrations'))
  },
  
  // ニュース
  news: {
    latest: () => apiCallWithRetry(() => apiClient.get('/news/latest')),
    list: () => apiCallWithRetry(() => apiClient.get('/news'))
  },
  
  // マイページ
  mypage: {
    profile: () => apiCallWithRetry(() => apiClient.get('/mypage/profile')),
    points: () => apiCallWithRetry(() => apiClient.get('/mypage/points')),
    activities: () => apiCallWithRetry(() => apiClient.get('/mypage/activities')),
    updateProfile: (data) => apiCallWithRetry(() => apiClient.patch('/mypage/profile', data))
  },

  // プロフィール設定関連
  profile: {
    check: () => apiCallWithRetry(() => apiClient.get('/profile/check')),
    setAvatar: (data) => apiCallWithRetry(() => apiClient.post('/profile/avatar', data)),
    setNickname: (data) => apiCallWithRetry(() => apiClient.post('/profile/nickname', data)),
    updatePassword: (data) => apiCallWithRetry(() => apiClient.post('/profile/update-password', data)),
    updateProfile: (data) => apiCallWithRetry(() => apiClient.post('/profile/update-profile', data))
  },
  
  // QRコード
  qr: {
    claim: (data) => apiCallWithRetry(() => apiClient.post('/qr/claim', data))
  },
  
  // イベント
  events: {
    list: () => apiCallWithRetry(() => apiClient.get('/events'))
  },
  
  // ギフト
  gift: {
    items: () => apiCallWithRetry(() => apiClient.get('/gift/items')),
    history: () => apiCallWithRetry(() => apiClient.get('/gift/history')),
    exchange: (data) => apiCallWithRetry(() => apiClient.post('/gift/exchange', data))
  },
  
  // お問い合わせ
  contact: {
    submit: (data) => apiCallWithRetry(() => apiClient.post('/contact', data))
  },
  
  // アバター関連
  avatars: {
    list: () => apiCallWithRetry(() => apiClient.get('/avatars')),
    show: (id) => apiCallWithRetry(() => apiClient.get(`/avatars/${id}`))
  },
  
  // 管理者API
  admin: {
    stats: () => apiCallWithRetry(() => apiClient.get('/admin/stats')),
    users: () => apiCallWithRetry(() => apiClient.get('/admin/users')),
    news: {
      list: () => apiCallWithRetry(() => apiClient.get('/admin/news')),
      store: (data) => apiCallWithRetry(() => apiClient.post('/admin/news', data)),
      update: (id, data) => apiCallWithRetry(() => apiClient.patch(`/admin/news/${id}`, data)),
      destroy: (id) => apiCallWithRetry(() => apiClient.delete(`/admin/news/${id}`))
    },
    qrcodes: {
      list: () => apiCallWithRetry(() => apiClient.get('/admin/qrcodes')),
      store: (data) => apiCallWithRetry(() => apiClient.post('/admin/qrcodes', data)),
      update: (id, data) => apiCallWithRetry(() => apiClient.patch(`/admin/qrcodes/${id}`, data)),
      destroy: (id) => apiCallWithRetry(() => apiClient.delete(`/admin/qrcodes/${id}`))
    },
    // ペンライト制御API
    artists: (params = {}) => apiCallWithRetry(() => apiClient.get('/admin/penlight/artists', { params })),
    songs: (params = {}) => apiCallWithRetry(() => apiClient.get('/admin/penlight/songs', { params })),
    presets: (params = {}) => apiCallWithRetry(() => apiClient.get('/admin/penlight/presets', { params })),
    preset: (id) => apiCallWithRetry(() => apiClient.get(`/admin/penlight/presets/${id}`)),
    storePreset: (data) => apiCallWithRetry(() => apiClient.post('/admin/penlight/presets', data)),
    updatePreset: (id, data) => apiCallWithRetry(() => apiClient.put(`/admin/penlight/presets/${id}`, data)),
    destroyPreset: (id) => apiCallWithRetry(() => apiClient.delete(`/admin/penlight/presets/${id}`)),
    activatePreset: (id) => apiCallWithRetry(() => apiClient.post(`/admin/penlight/presets/${id}/activate`)),
    deactivatePreset: (id) => apiCallWithRetry(() => apiClient.post(`/admin/penlight/presets/${id}/deactivate`)),
    reorderPresets: (data) => apiCallWithRetry(() => apiClient.post('/admin/penlight/presets/reorder', data)),
    websocketStats: () => apiCallWithRetry(() => apiClient.get('/admin/penlight/websocket-stats'))
  }
}

// エラーハンドリングユーティリティ
export const handleApiError = (error, customMessage = null) => {
  const appStore = useAppStore()
  
  let message = customMessage
  
  if (!message) {
    if (error.response?.data?.code === 'ALREADY_REGISTERED') {
      message = 'この電話番号は既に登録済みです。ログインページからログインしてください。'
    } else if (error.response?.data?.message) {
      message = error.response.data.message
    } else if (error.response?.status === 404) {
      message = 'リソースが見つかりません'
    } else if (error.response?.status === 422) {
      // バリデーションエラーの詳細メッセージを処理
      if (error.response?.data?.errors) {
        const errors = error.response.data.errors
        if (errors.phone && errors.phone.includes('already been taken')) {
          message = 'この電話番号は既に登録済みです。ログインページからログインしてください。'
        } else {
          message = '入力内容に誤りがあります'
        }
      } else {
        message = '入力内容に誤りがあります'
      }
    } else if (error.response?.status >= 500) {
      message = 'サーバーエラーが発生しました'
    } else if (error.code === 'ECONNABORTED') {
      message = 'リクエストがタイムアウトしました'
    } else {
      message = '予期しないエラーが発生しました'
    }
  }
  
  appStore.showError(message)
  return Promise.reject(error)
}

// 成功メッセージユーティリティ
export const handleApiSuccess = (message) => {
  const appStore = useAppStore()
  appStore.showSuccess(message)
}

export default api
