<template>
  <div class="min-h-screen bg-gray-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-white">
          {{ currentStep === 1 ? 'ログイン' : '認証コード入力' }}
        </h2>
        <p class="mt-2 text-center text-sm text-gray-400">
          {{ currentStep === 1 ? 'アカウントにログインしてください' : 'SMSで送信された6桁のコードを入力してください' }}
        </p>
      </div>
      
      <!-- ステップ1: 電話番号とパスワード入力 -->
      <form v-if="currentStep === 1" class="mt-8 space-y-6" @submit.prevent="handleLogin">
        <div class="space-y-4">
          <div>
            <InternationalPhoneInput
              v-model="form.phone"
              label="電話番号"
              placeholder="電話番号を入力"
              required
            />
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-300">
              パスワード
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              required
              @focus="handlePasswordFocus"
              @mousedown="handlePasswordMouseDown"
              class="mt-1 appearance-none relative block w-full px-3 py-2 border border-gray-600 placeholder-gray-400 text-white bg-gray-800 rounded-md focus:outline-none focus:ring-evox-blue focus:border-evox-blue focus:z-10 sm:text-sm"
              placeholder="パスワードを入力"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="isLoading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-evox-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-evox-blue disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ isLoading ? '認証コード送信中...' : '認証コードを送信' }}
          </button>
        </div>

        <div class="text-center">
          <router-link
            to="/entry"
            class="font-medium text-evox-blue hover:text-blue-400"
          >
            新規登録はこちら
          </router-link>
        </div>
      </form>

      <!-- ステップ2: OTPコード入力 -->
      <form v-if="currentStep === 2" class="mt-8 space-y-6" @submit.prevent="handleOtpVerification">
        <div class="space-y-4">
          <div>
            <div class="flex justify-center space-x-2">
              <input
                v-for="(digit, index) in form.otp"
                :key="index"
                v-model="form.otp[index]"
                type="tel"
                maxlength="1"
                required
                inputmode="numeric"
                pattern="[0-9]*"
                autocomplete="one-time-code"
                @focus="handleOtpFocus"
                @mousedown="handleOtpMouseDown"
                @input="handleOtpInput($event, index)"
                @keydown="handleOtpKeydown($event, index)"
                @compositionstart="handleCompositionStart"
                @compositionend="handleCompositionEnd"
                class="w-12 h-12 text-center text-lg font-mono border border-gray-600 bg-gray-800 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-evox-blue focus:border-evox-blue"
                placeholder=""
                style="ime-mode: disabled;"
              />
            </div>
          </div>
          
          <!-- 開発環境でのOTPコード表示 -->
          <div v-if="otpCode" class="text-center">
            <p class="text-sm text-gray-400">開発環境用コード:</p>
            <p class="text-lg font-mono text-evox-blue">{{ otpCode }}</p>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="isLoading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-evox-blue hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-evox-blue disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
              <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </span>
            {{ isLoading ? '認証中...' : 'ログイン' }}
          </button>
        </div>

        <div class="text-center">
          <button
            type="button"
            @click="currentStep = 1"
            class="font-medium text-evox-blue hover:text-blue-400"
          >
            戻る
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import api from '@/services/api'
import InternationalPhoneInput from '@/components/InternationalPhoneInput.vue'

const router = useRouter()
const appStore = useAppStore()

const currentStep = ref(1)
const tempId = ref('')
const otpCode = ref('')

const form = ref({
  phone: '',
  password: '',
  otp: ['', '', '', '', '', '']
})

const isLoading = ref(false)

const handlePasswordFocus = (event) => {
  // パスワード入力時にIMEを無効化して半角英数字入力に強制切り替え
  forceAlphanumericInput(event.target)
}

const handlePasswordMouseDown = (event) => {
  // マウスクリック時にもIMEを無効化
  forceAlphanumericInput(event.target)
}

const handleOtpFocus = (event) => {
  // OTP入力時にIMEを無効化して半角数字入力に強制切り替え
  forceNumericInput(event.target)
}

const handleOtpMouseDown = (event) => {
  // マウスクリック時にもIMEを無効化
  forceNumericInput(event.target)
}

const forceAlphanumericInput = (input) => {
  // 半角英数字入力に最適化
  input.setAttribute('inputmode', 'text')
  input.setAttribute('pattern', '[a-zA-Z0-9]*')
  
  // 日本語IMEを無効化
  input.style.imeMode = 'disabled'
}

const forceNumericInput = (input) => {
  // 半角数字入力に最適化
  input.setAttribute('inputmode', 'numeric')
  input.setAttribute('pattern', '[0-9]*')
  input.setAttribute('type', 'tel') // 電話番号タイプで数字キーパッドを強制
  
  // 日本語IMEを無効化
  input.style.imeMode = 'disabled'
  input.style.imeMode = 'off'
  
  // モバイルデバイスで数字キーパッドを表示
  if ('virtualKeyboard' in navigator) {
    input.setAttribute('inputmode', 'numeric')
  }
  
  // 全角入力を防ぐためのイベントリスナー
  input.addEventListener('compositionstart', (e) => {
    e.preventDefault()
    return false
  })
  
  input.addEventListener('compositionend', (e) => {
    e.preventDefault()
    return false
  })
}

const handleOtpInput = (event, index) => {
  let value = event.target.value
  
  // 全角数字を半角数字に変換
  value = value.replace(/[０-９]/g, (s) => String.fromCharCode(s.charCodeAt(0) - 0xFEE0))
  
  // 数字以外の文字を除去
  value = value.replace(/[^0-9]/g, '')
  
  // 最初の1文字のみを取得
  value = value.charAt(0)
  
  // 値を設定
  form.value.otp[index] = value
  
  // 次のフィールドにフォーカスを移動
  if (value && index < 5) {
    const nextInput = event.target.parentNode.children[index + 1]
    if (nextInput) {
      nextInput.focus()
    }
  }
}

const handleOtpKeydown = (event, index) => {
  // バックスペースキーで前のフィールドに移動
  if (event.key === 'Backspace' && !form.value.otp[index] && index > 0) {
    const prevInput = event.target.parentNode.children[index - 1]
    if (prevInput) {
      prevInput.focus()
    }
  }
}

const handleCompositionStart = (event) => {
  // IME入力開始を防ぐ
  event.preventDefault()
  return false
}

const handleCompositionEnd = (event) => {
  // IME入力終了を防ぐ
  event.preventDefault()
  return false
}

const handleLogin = async () => {
  if (isLoading.value) return
  
  isLoading.value = true
  
  try {
    const loginData = {
      phone: form.value.phone,
      password: form.value.password
    }
    
    const response = await api.auth.login(loginData)
    
    if (response.data.success) {
      // 一時IDとOTPコードを保存
      tempId.value = response.data.data.temp_id
      otpCode.value = response.data.data.otp_code || ''
      
      // 成功メッセージを表示
      appStore.showSuccess('認証コードを送信しました')
      
      // ステップ2に進む
      currentStep.value = 2
    }
  } catch (error) {
    console.error('Login error:', error)
    
    if (error.response?.data?.message) {
      appStore.showError(error.response.data.message)
    } else {
      appStore.showError('認証コードの送信に失敗しました')
    }
  } finally {
    isLoading.value = false
  }
}

const handleOtpVerification = async () => {
  if (isLoading.value) return
  
  isLoading.value = true
  
  try {
    const otpData = {
      temp_id: tempId.value,
      code: form.value.otp.join('')
    }
    
    const response = await api.auth.verifyLoginOtp(otpData)
    
    if (response.data.success) {
      // トークンを保存
      localStorage.setItem('auth_token', response.data.data.token)
      
      // 認証状態を更新
      appStore.setAuthenticated(true, response.data.data.user)
      
      // 成功メッセージを表示
      appStore.showSuccess('ログインに成功しました')
      
      // マイページにリダイレクト
      router.push('/mypage')
    }
  } catch (error) {
    console.error('OTP verification error:', error)
    
    if (error.response?.data?.message) {
      appStore.showError(error.response.data.message)
    } else {
      appStore.showError('認証に失敗しました')
    }
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
/* パスワード入力欄のIME制御 */
input[type="password"] {
  /* IMEモードを無効化 */
  ime-mode: disabled;
  /* 半角英数字入力に最適化 */
  -webkit-text-security: none;
}

/* OTP入力欄のIME制御 */
input[inputmode="numeric"],
input[type="tel"] {
  /* IMEモードを無効化 */
  ime-mode: disabled !important;
  /* 数字入力に最適化 */
  -webkit-text-security: none;
  /* モバイルで数字キーパッドを表示 */
  -webkit-appearance: none;
  appearance: none;
  /* 全角入力を防ぐ */
  text-transform: none;
}

/* 数字以外の入力を防ぐ */
input[inputmode="numeric"]::-webkit-outer-spin-button,
input[inputmode="numeric"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[inputmode="numeric"][type=number] {
  -moz-appearance: textfield;
}
</style>
