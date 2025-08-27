/**
 * EvoX デザインシステム設定
 * カスタマイズ可能なデザイン設定を一元管理
 */

export const DESIGN_CONFIG = {
  // カラーパレット
  colors: {
    primary: {
      black: '#000000',
      gray: '#1a1a1a',
      blue: '#0066cc',
      gold: '#ffd700'
    },
    secondary: {
      white: '#ffffff',
      lightGray: '#f5f5f5',
      darkGray: '#333333',
      red: '#ff4444',
      green: '#44ff44'
    },
    // カスタムカラー（後から追加可能）
    custom: {
      // 例: brandColor: '#custom-color'
    }
  },

  // タイポグラフィ
  typography: {
    fonts: {
      primary: 'Inter, system-ui, sans-serif',
      secondary: 'Noto Sans JP, sans-serif',
      mono: 'JetBrains Mono, monospace'
    },
    sizes: {
      xs: '0.75rem',
      sm: '0.875rem',
      base: '1rem',
      lg: '1.125rem',
      xl: '1.25rem',
      '2xl': '1.5rem',
      '3xl': '1.875rem',
      '4xl': '2.25rem',
      '5xl': '3rem'
    },
    weights: {
      light: 300,
      normal: 400,
      medium: 500,
      semibold: 600,
      bold: 700
    }
  },

  // スペーシング
  spacing: {
    xs: '0.25rem',
    sm: '0.5rem',
    md: '1rem',
    lg: '1.5rem',
    xl: '2rem',
    '2xl': '3rem',
    '3xl': '4rem'
  },

  // ブレークポイント
  breakpoints: {
    sm: '640px',
    md: '768px',
    lg: '1024px',
    xl: '1280px',
    '2xl': '1536px'
  },

  // アニメーション
  animations: {
    duration: {
      fast: '150ms',
      normal: '300ms',
      slow: '500ms'
    },
    easing: {
      ease: 'cubic-bezier(0.4, 0, 0.2, 1)',
      easeIn: 'cubic-bezier(0.4, 0, 1, 1)',
      easeOut: 'cubic-bezier(0, 0, 0.2, 1)',
      easeInOut: 'cubic-bezier(0.4, 0, 0.2, 1)'
    }
  },

  // コンポーネント設定
  components: {
    button: {
      sizes: {
        sm: { padding: '0.5rem 1rem', fontSize: '0.875rem' },
        md: { padding: '0.75rem 1.5rem', fontSize: '1rem' },
        lg: { padding: '1rem 2rem', fontSize: '1.125rem' }
      },
      variants: {
        primary: {
          backgroundColor: '#0066cc',
          color: '#ffffff',
          hover: { backgroundColor: '#0052a3' }
        },
        secondary: {
          backgroundColor: 'transparent',
          color: '#0066cc',
          border: '2px solid #0066cc',
          hover: { backgroundColor: '#0066cc', color: '#ffffff' }
        },
        outline: {
          backgroundColor: 'transparent',
          color: '#ffffff',
          border: '2px solid #ffffff',
          hover: { backgroundColor: '#ffffff', color: '#000000' }
        }
      }
    },
    card: {
      padding: '1.5rem',
      borderRadius: '0.5rem',
      backgroundColor: '#1a1a1a',
      border: '1px solid #333333'
    },
    input: {
      padding: '0.75rem 1rem',
      borderRadius: '0.375rem',
      border: '1px solid #333333',
      backgroundColor: '#ffffff',
      color: '#000000',
      focus: {
        borderColor: '#0066cc',
        boxShadow: '0 0 0 3px rgba(0, 102, 204, 0.1)'
      }
    }
  },

  // 画像パス設定
  images: {
    logo: '/images/logo_evox.png',
    hero: '/images/hero.png',
    catchPre: '/images/chatch_pre.png',
    btnReg: '/images/btn_reg01.png',
    steps: '/images/step1-3.png',
    worldMap: '/images/worldimage.png',
    story: '/images/story.png',
    worldImage2: '/images/worldimage2.png',
    footer: '/images/footerimage.png',
    footerLogo: '/images/footer_logo.png',
    sns: {
      line: '/images/sns_li.png',
      facebook: '/images/sns_fb.png',
      instagram: '/images/sns_ins.png',
      tiktok: '/images/sns_tk.png',
      x: '/images/sns_x.png',
      youtube: '/images/sns_yt.png'
    },
    characters: {
      char1: '/images/cal003.png',
      char2: '/images/cal004.png',
      char3: '/images/cal005.png',
      char4: '/images/cal006.png'
    }
  },

  // コンテンツ設定
  content: {
    releaseDate: '2026-07-10T00:00:00+09:00',
    siteName: 'EvoX',
    description: 'EvoX - 新しい世界への扉',
    keywords: ['EvoX', 'ゲーム', '事前登録', 'RPG'],
    social: {
      title: 'EvoX - 事前登録開始',
      description: 'EvoXの事前登録が開始されました。限定特典をお見逃しなく！',
      image: '/images/hero.png'
    }
  },

  // 機能設定
  features: {
    qrScanner: {
      enabled: true,
      cameraPermission: true,
      fileUpload: true
    },
    notifications: {
      enabled: true,
      position: 'top-right',
      duration: 5000
    },
    analytics: {
      enabled: true,
      provider: 'ga4' // 'ga4' | 'plausible'
    }
  }
}

// デザイン設定の動的更新関数
export function updateDesignConfig(newConfig) {
  Object.assign(DESIGN_CONFIG, newConfig)
}

// カラー取得ヘルパー
export function getColor(path) {
  return path.split('.').reduce((obj, key) => obj?.[key], DESIGN_CONFIG.colors)
}

// コンポーネント設定取得ヘルパー
export function getComponentConfig(component, variant = 'default') {
  return DESIGN_CONFIG.components[component]?.[variant] || DESIGN_CONFIG.components[component]
}
