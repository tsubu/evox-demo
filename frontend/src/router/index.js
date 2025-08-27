import { createRouter, createWebHistory } from 'vue-router'
import Home from '../pages/Home.vue'
import MyPage from '../pages/MyPage.vue'
import QRScanner from '../pages/QRScanner.vue'
import QrOptionsSelection from '../pages/QrOptionsSelection.vue'
import QrResult from '../pages/QrResult.vue'
import PenlightMode from '../pages/PenlightMode.vue'
import PenlightSync from '../pages/PenlightSync.vue'

import Terms from '../pages/Terms.vue'
import Privacy from '../pages/Privacy.vue'
import PreRegistration from '../pages/PreRegistration.vue'
import Login from '../pages/Login.vue'
import Contact from '../pages/Contact.vue'
import ForgotPassword from '../pages/ForgotPassword.vue'
import About from '../pages/About.vue'
import CharacterSelection from '../pages/CharacterSelection.vue'
import NicknameSetup from '../pages/NicknameSetup.vue'
import Settings from '../pages/Settings.vue'
import logger from '../utils/logger'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/about',
    name: 'About',
    component: About
  },
  {
    path: '/entry',
    name: 'PreRegistration',
    component: PreRegistration
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/contact',
    name: 'Contact',
    component: Contact
  },
  {
    path: '/forgot-password',
    name: 'ForgotPassword',
    component: ForgotPassword
  },
  {
    path: '/mypage',
    name: 'MyPage',
    component: MyPage,
    meta: { requiresAuth: true }
  },
  {
    path: '/character-selection',
    name: 'CharacterSelection',
    component: CharacterSelection,
    meta: { requiresAuth: true }
  },
  {
    path: '/nickname-setup',
    name: 'NicknameSetup',
    component: NicknameSetup,
    meta: { requiresAuth: true }
  },
  {
    path: '/settings',
    name: 'Settings',
    component: Settings,
    meta: { requiresAuth: true }
  },
  {
    path: '/qr-scanner',
    name: 'QRScanner',
    component: QRScanner,
    meta: { requiresAuth: true }
  },
  {
    path: '/qr-options-selection',
    name: 'QrOptionsSelection',
    component: QrOptionsSelection,
    meta: { requiresAuth: true }
  },
  {
    path: '/qr-result',
    name: 'QrResult',
    component: QrResult,
    meta: { requiresAuth: true }
  },
  {
    path: '/penlight-mode',
    name: 'PenlightMode',
    component: PenlightMode,
    meta: { requiresAuth: false, skipAuthCheck: true }
  },
  {
    path: '/penlight-new',
    name: 'PenlightNew',
    component: PenlightMode,
    meta: { requiresAuth: false, skipAuthCheck: true }
  },
  {
    path: '/penlight-app',
    name: 'PenlightApp',
    component: PenlightMode,
    meta: { requiresAuth: false, skipAuthCheck: true }
  },
  {
    path: '/penlight-sync',
    name: 'PenlightSync',
    component: PenlightSync,
    meta: { requiresAuth: true }
  },


  {
    path: '/terms',
    name: 'Terms',
    component: Terms
  },
  {
    path: '/privacy',
    name: 'Privacy',
    component: Privacy
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ナビゲーションガード
router.beforeEach(async (to, from, next) => {
  // 認証状態を確認（複数の方法で）
  const authToken = localStorage.getItem('auth_token')
  const isAuthenticated = !!authToken
  const userRole = localStorage.getItem('user_role')
  
  // セッションストレージからも認証状態を確認
  const sessionAuthToken = sessionStorage.getItem('auth_token')
  const hasSessionAuth = !!sessionAuthToken
  
  const authDetails = {
    localStorage: authToken ? 'EXISTS' : 'NOT FOUND',
    sessionStorage: sessionAuthToken ? 'EXISTS' : 'NOT FOUND',
    isAuthenticated,
    hasSessionAuth
  }
  
  logger.info('Navigation guard - Auth check details', authDetails)
  
  // 認証状態の詳細ログ
  console.log('🔍 認証状態詳細:', {
    localStorage: authToken ? 'EXISTS' : 'NOT FOUND',
    sessionStorage: sessionAuthToken ? 'EXISTS' : 'NOT FOUND',
    isAuthenticated,
    hasSessionAuth,
    toPath: to.path,
    toName: to.name,
    fromPath: from.path,
    fromName: from.name
  })
  
  logger.info('=== Navigation Guard Start ===')
  logger.info('Navigation guard - Auth token', { exists: !!authToken })
  logger.info('Navigation guard - Is authenticated', { isAuthenticated })
  logger.info('Navigation guard - To path', { path: to.path, name: to.name })
  logger.info('Navigation guard - From path', { path: from.path, name: from.name })
  logger.info('Navigation guard - To meta', to.meta)
  logger.info('Navigation guard - From meta', from.meta)
  logger.info('Navigation guard - User role', { userRole })
  logger.info('Navigation guard - Session auth', { hasSessionAuth })
  
  // デバッグ用：ペンライトページ判定
  const isToPenlightPage = to.name === 'PenlightMode' || to.name === 'PenlightNew' || to.name === 'PenlightApp' || to.path === '/penlight-app'
  const isFromPenlightPage = from.name === 'PenlightMode' || from.name === 'PenlightNew' || from.name === 'PenlightApp' || from.path === '/penlight-app'
  
  logger.info('Navigation guard - Penlight page check', {
    isToPenlightPage,
    isFromPenlightPage,
    toName: to.name,
    toPath: to.path,
    fromName: from.name,
    fromPath: from.path
  })
  
  // ペンライトモードページへの遷移（最優先）
  if (to.name === 'PenlightMode' || to.name === 'PenlightNew' || to.name === 'PenlightApp' || to.path === '/penlight-app' || to.meta.skipAuthCheck) {
    logger.info('Navigation guard - Going to penlight page, allowing access', {
      from: from.name,
      fromPath: from.path,
      to: to.name,
      toPath: to.path,
      isAuthenticated: isAuthenticated,
      skipAuthCheck: to.meta.skipAuthCheck
    })
    next()
    return
  }
  
  // ペンライトモードページからの遷移を特別処理（最優先）
  if (from.name === 'PenlightMode' || from.name === 'PenlightNew' || from.name === 'PenlightApp' || from.path === '/penlight-app') {
    logger.info('Navigation guard - Coming from penlight page', { 
      from: from.name, 
      to: to.name, 
      toPath: to.path,
      fromPath: from.path,
      isAuthenticated: isAuthenticated
    })
    logger.info('Navigation guard - To meta', to.meta)
    logger.info('Navigation guard - From meta', from.meta)
    
    // ペンライトモードからの遷移は全て許可（認証チェックを完全にスキップ）
    logger.info('Navigation guard - From penlight page, allowing all transitions without auth check')
    next()
    return
  }
  
  // ログイン済みユーザーがTOPページにアクセスした場合、マイページにリダイレクト
  // ただし、ペンライトモードからの遷移の場合は許可
  if (to.path === '/' && isAuthenticated && from.name !== 'PenlightMode' && from.name !== 'PenlightNew' && from.name !== 'PenlightApp' && from.path !== '/penlight-app') {
    logger.info('Navigation guard - Logged in user accessing TOP page, redirecting to mypage', {
      fromName: from.name,
      fromPath: from.path,
      isFromPenlightMode: from.name === 'PenlightMode' || from.name === 'PenlightNew' || from.name === 'PenlightApp' || from.path === '/penlight-app',
      toPath: to.path,
      isAuthenticated: isAuthenticated
    })
    next('/mypage')
    return
  }
  
  if (to.meta.skipAuthCheck) {
    // 認証チェックをスキップするページ
    logger.info('Navigation guard - Skipping auth check for', { path: to.path, name: to.name })
    next()
  } else if (to.meta.requiresAuth && !isAuthenticated) {
    logger.warn('Navigation guard - Auth required but not authenticated, redirecting to login')
    next('/login')
  } else if (to.meta.requiresAdmin && userRole !== 'admin') {
    logger.warn('Navigation guard - Admin required but not admin, redirecting to /')
    next('/')
  } else if (to.meta.requiresAuth && isAuthenticated) {
    // 認証済みユーザーはマイページにアクセス可能
    logger.info('Navigation guard - Authenticated user, proceeding to', { path: to.path })
    next()
  } else {
    logger.info('Navigation guard - No auth required, proceeding to', { path: to.path })
    next()
  }
  
  logger.info('=== Navigation Guard End ===')
})

// ページ遷移後にページ上部にスクロール
router.afterEach((to, from) => {
  // ページ遷移が完了した後にページ上部にスクロール
  window.scrollTo({
    top: 0,
    behavior: 'smooth'
  })
})

export default router
