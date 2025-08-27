/**
 * アプリケーション定数
 * 拡張性とカスタマイズ性を考慮した定数管理
 */

// アプリケーション基本情報
export const APP_CONFIG = {
  name: 'EvoX',
  version: '1.0.0',
  description: 'EvoX - 新しい世界への扉',
  releaseDate: '2026-07-10T00:00:00+09:00',
  supportEmail: 'support@evox.com',
  privacyPolicyUrl: '/privacy',
  termsOfServiceUrl: '/terms'
}

// API設定
export const API_CONFIG = {
  baseURL: import.meta.env.VITE_API_BASE_URL || '/api',
  timeout: 10000,
  retryAttempts: 3,
  retryDelay: 1000,
  endpoints: {
    auth: {
      login: '/auth/login',
      logout: '/auth/logout',
      me: '/auth/me',
      verifyOtp: '/verify-otp'
    },
    prereg: {
      store: '/prereg',
      complete: '/prereg/complete',
      character: '/prereg/character'
    },
    registrations: '/registrations',
    news: {
      latest: '/news/latest',
      list: '/news'
    },
    mypage: {
      profile: '/mypage/profile',
      points: '/mypage/points',
      updateProfile: '/mypage/profile'
    },
    qr: {
      claim: '/qr/claim'
    },
    events: '/events',
    gift: {
      items: '/gift/items',
      history: '/gift/history',
      exchange: '/gift/exchange'
    },
    admin: {
      stats: '/admin/stats',
      users: '/admin/users',
      news: '/admin/news',
      qrcodes: '/admin/qrcodes'
    }
  }
}

// 認証設定
export const AUTH_CONFIG = {
  tokenKey: 'auth_token',
  refreshTokenKey: 'refresh_token',
  sessionTimeout: 24 * 60 * 60 * 1000, // 24時間
  otpExpiry: 10 * 60 * 1000, // 10分
  maxLoginAttempts: 5,
  lockoutDuration: 15 * 60 * 1000 // 15分
}

// ポイントシステム設定
export const POINTS_CONFIG = {
  initialPoints: 100,
  qrRewardPoints: 50,
  registrationBonus: 100,
  dailyLoginBonus: 10,
  maxPointsPerDay: 1000,
  minExchangePoints: 10
}

// QRコード設定
export const QR_CONFIG = {
  maxUses: 100,
  expiryDays: 30,
  rewardTypes: {
    points: 'points',
    avatar: 'avatar',
    video: 'video',
    music: 'music'
  }
}

// 通知設定
export const NOTIFICATION_CONFIG = {
  position: 'top-right',
  duration: 5000,
  maxNotifications: 5,
  types: {
    success: {
      icon: '✅',
      color: 'green'
    },
    error: {
      icon: '❌',
      color: 'red'
    },
    warning: {
      icon: '⚠️',
      color: 'yellow'
    },
    info: {
      icon: 'ℹ️',
      color: 'blue'
    }
  }
}

// アニメーション設定
export const ANIMATION_CONFIG = {
  duration: {
    fast: 150,
    normal: 300,
    slow: 500
  },
  easing: {
    ease: 'cubic-bezier(0.4, 0, 0.2, 1)',
    easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
    easeOut: 'cubic-bezier(0, 0, 0.2, 1)',
    easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)'
  }
}

// バリデーション設定
export const VALIDATION_CONFIG = {
  phone: {
    pattern: /^[0-9\-\s]+$/,
    minLength: 8,
    maxLength: 15
  },
  password: {
    minLength: 8,
    maxLength: 255,
    requireUppercase: true,
    requireLowercase: true,
    requireNumbers: true,
    requireSymbols: true
  },
  nickname: {
    minLength: 1,
    maxLength: 20,
    pattern: /^[a-zA-Z0-9\u3040-\u309F\u30A0-\u30FF\u4E00-\u9FAF\s]+$/
  },
  otp: {
    length: 6,
    pattern: /^[0-9]{6}$/
  }
}

// ローカライゼーション設定
export const I18N_CONFIG = {
  defaultLocale: 'ja',
  fallbackLocale: 'en',
  supportedLocales: ['ja', 'en'],
  dateFormat: {
    ja: 'YYYY年MM月DD日',
    en: 'YYYY-MM-DD'
  },
  timeFormat: {
    ja: 'HH:mm',
    en: 'HH:mm'
  }
}

// 機能フラグ
export const FEATURE_FLAGS = {
  qrScanner: true,
  notifications: true,
  analytics: true,
  socialLogin: false,
  pwa: false,
  darkMode: true,
  multiLanguage: false
}

// エラーメッセージ
export const ERROR_MESSAGES = {
  network: {
    timeout: 'リクエストがタイムアウトしました',
    connection: 'ネットワーク接続エラーが発生しました',
    server: 'サーバーエラーが発生しました'
  },
  auth: {
    invalidCredentials: '電話番号またはパスワードが正しくありません',
    invalidOtp: '無効な認証コードです',
    sessionExpired: 'セッションが期限切れです',
    unauthorized: '認証が必要です'
  },
  validation: {
    required: 'この項目は必須です',
    invalidFormat: '形式が正しくありません',
    tooShort: '文字数が不足しています',
    tooLong: '文字数が多すぎます',
    unique: '既に使用されています'
  },
  qr: {
    invalidCode: '無効なQRコードです',
    alreadyUsed: 'このQRコードは既に使用されています',
    expired: 'このQRコードは期限切れです'
  }
}

// 成功メッセージ
export const SUCCESS_MESSAGES = {
  registration: '登録が完了しました',
  login: 'ログインしました',
  logout: 'ログアウトしました',
  otpSent: '認証コードを送信しました',
  otpVerified: '認証が完了しました',
  qrClaimed: 'QRコードを読み取りました',
  pointsEarned: 'ポイントを獲得しました',
  profileUpdated: 'プロフィールを更新しました'
}

// ルーティング設定
export const ROUTES = {
  home: '/',
  prereg: '/prereg',
  mypage: '/mypage',
  qr: '/qr',
  admin: '/admin',
  terms: '/terms',
  privacy: '/privacy',
  contact: '/contact'
}

// 管理者権限
export const ADMIN_PERMISSIONS = {
  viewUsers: 'view_users',
  editUsers: 'edit_users',
  deleteUsers: 'delete_users',
  viewNews: 'view_news',
  createNews: 'create_news',
  editNews: 'edit_news',
  deleteNews: 'delete_news',
  viewQrCodes: 'view_qr_codes',
  createQrCodes: 'create_qr_codes',
  editQrCodes: 'edit_qr_codes',
  deleteQrCodes: 'delete_qr_codes',
  viewStats: 'view_stats'
}
