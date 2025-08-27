/**
 * Cookie管理ユーティリティ
 */

/**
 * Cookieを設定
 * @param {string} name - Cookie名
 * @param {string} value - Cookie値
 * @param {number} days - 有効期限（日数）
 */
export const setCookie = (name, value, days = 7) => {
  const expires = new Date()
  expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000))
  
  document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Strict`
}

/**
 * Cookieを取得
 * @param {string} name - Cookie名
 * @returns {string|null} Cookie値
 */
export const getCookie = (name) => {
  const nameEQ = name + "="
  const ca = document.cookie.split(';')
  
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i]
    while (c.charAt(0) === ' ') c = c.substring(1, c.length)
    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length)
  }
  
  return null
}

/**
 * Cookieを削除
 * @param {string} name - Cookie名
 */
export const deleteCookie = (name) => {
  document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`
}

/**
 * 認証トークンを保存
 * @param {string} token - 認証トークン
 */
export const setAuthToken = (token) => {
  setCookie('auth_token', token, 30) // 30日間有効
  localStorage.setItem('auth_token', token)
}

/**
 * 認証トークンを取得
 * @returns {string|null} 認証トークン
 */
export const getAuthToken = () => {
  return getCookie('auth_token') || localStorage.getItem('auth_token')
}

/**
 * 認証トークンを削除
 */
export const clearAuthToken = () => {
  deleteCookie('auth_token')
  localStorage.removeItem('auth_token')
}

/**
 * 一時IDを生成または取得
 * @returns {string} 一時ID
 */
export const getOrGenerateTempId = () => {
  let tempId = getCookie('temp_id')
  
  if (!tempId) {
    tempId = generateTempId()
    setCookie('temp_id', tempId, 1) // 1日間有効
  }
  
  return tempId
}

/**
 * 一時IDをクリア
 */
export const clearTempId = () => {
  deleteCookie('temp_id')
}

/**
 * 13桁の一時IDを生成
 * @returns {string} 一時ID
 */
const generateTempId = () => {
  const timestamp = Date.now().toString()
  const random = Math.random().toString(36).substring(2, 8)
  return (timestamp + random).substring(0, 13)
}
