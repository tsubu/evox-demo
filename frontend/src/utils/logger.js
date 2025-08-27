// ログ記録ユーティリティ
class Logger {
  constructor() {
    this.logQueue = []
    this.flushInterval = null
    this.enabled = true // ログ送信を有効/無効にするフラグ
    this.startFlushInterval()
  }

  log(level, message, data = {}) {
    const logEntry = {
      timestamp: new Date().toISOString(),
      level,
      message,
      data,
      pathname: window.location.pathname,
      url: window.location.href,
      userAgent: navigator.userAgent
    }
    
    this.logQueue.push(logEntry)
    
    // コンソールにも出力（開発時のみ）
    if (process.env.NODE_ENV === 'development') {
      console.log(`[${level.toUpperCase()}] ${message}`, data)
    }
  }

  debug(message, data = {}) {
    this.log('debug', message, data)
  }

  info(message, data = {}) {
    this.log('info', message, data)
  }

  warn(message, data = {}) {
    this.log('warn', message, data)
  }

  error(message, data = {}) {
    this.log('error', message, data)
  }

  // ナビゲーション専用ログ
  navigation(message, data = {}) {
    const navigationData = {
      ...data,
      currentPath: window.location.pathname,
      currentUrl: window.location.href,
      timestamp: new Date().toISOString()
    }
    this.log('navigation', message, navigationData)
  }

  // ページアクセス専用ログ
  pageAccess(pageName, data = {}) {
    const pageData = {
      pageName,
      ...data,
      currentPath: window.location.pathname,
      currentUrl: window.location.href,
      timestamp: new Date().toISOString()
    }
    this.log('page_access', `Page accessed: ${pageName}`, pageData)
  }

  // エラー専用ログ
  pageError(error, context = {}) {
    const errorData = {
      error: error.message,
      stack: error.stack,
      ...context,
      currentPath: window.location.pathname,
      currentUrl: window.location.href,
      timestamp: new Date().toISOString()
    }
    this.log('error', `Page error: ${error.message}`, errorData)
  }

  startFlushInterval() {
    this.flushInterval = setInterval(() => {
      this.flush()
    }, 10000) // 10秒ごとにフラッシュ
  }

  stopFlushInterval() {
    if (this.flushInterval) {
      clearInterval(this.flushInterval)
      this.flushInterval = null
    }
  }

  async flush() {
    if (this.logQueue.length === 0 || !this.enabled) return

    const logsToSend = [...this.logQueue]
    this.logQueue = []

    try {
      const response = await fetch('/api/logs/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
          logs: logsToSend
        })
      })

      if (!response.ok) {
        console.error('Failed to send logs to server:', response.status, response.statusText)
        // エラーの場合はログを再キューに追加
        this.logQueue.unshift(...logsToSend)
      }
    } catch (error) {
      console.error('Error sending logs:', error)
      // エラーの場合はログを再キューに追加
      this.logQueue.unshift(...logsToSend)
    }
  }

  // 強制フラッシュ
  forceFlush() {
    return this.flush()
  }

  // ログ送信を無効にする
  disable() {
    this.enabled = false
    console.log('Logger disabled')
  }

  // ログ送信を有効にする
  enable() {
    this.enabled = true
    console.log('Logger enabled')
  }
}

const logger = new Logger()

// ページ離脱時にログをフラッシュ
window.addEventListener('beforeunload', () => {
  logger.forceFlush()
})

// エラーハンドリング
window.addEventListener('error', (event) => {
  logger.pageError(event.error, {
    filename: event.filename,
    lineno: event.lineno,
    colno: event.colno
  })
})

// Promise rejection ハンドリング
window.addEventListener('unhandledrejection', (event) => {
  logger.pageError(new Error(event.reason), {
    type: 'unhandledrejection'
  })
})

export default logger

