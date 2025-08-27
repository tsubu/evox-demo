<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 to-blue-700 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">EvoX 管理画面</h1>
        <p class="text-gray-600">管理者ログイン</p>
      </div>

      <!-- ステップ1: 電話番号・パスワード入力 -->
      <div v-if="currentStep === 1" class="space-y-6">
        <div class="bg-gray-100 p-3 rounded text-center text-sm font-mono text-gray-600">
          一時ID: {{ tempId }}
        </div>
        
        <div>
          <label for="adminPhone" class="block text-sm font-medium text-gray-700 mb-2">
            電話番号
          </label>
          <input
            id="adminPhone"
            v-model="form.phone"
            type="tel"
            placeholder="+81xxxxxxxxxx"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>

        <div>
          <label for="adminPassword" class="block text-sm font-medium text-gray-700 mb-2">
            パスワード
          </label>
          <input
            id="adminPassword"
            v-model="form.password"
            type="password"
            placeholder="パスワードを入力"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            required
          />
        </div>

        <button
          @click="login"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
        >
          {{ loading ? '送信中...' : '認証コードを発行' }}
        </button>
        
        <div v-if="errorMessage" class="text-red-600 text-sm text-center">
          {{ errorMessage }}
        </div>
      </div>

      <!-- ステップ2: OTP認証 -->
      <div v-if="currentStep === 2" class="space-y-6">
        <div class="bg-gray-100 p-3 rounded text-center text-sm font-mono text-gray-600">
          一時ID: {{ tempId }}
        </div>
        
        <p class="text-center text-gray-600 mb-4">
          携帯番号に届いたコードを入力しましょう
        </p>

        <div class="flex justify-center gap-2 mb-4">
          <input
            v-for="(digit, index) in 6"
            :key="index"
            v-model="otpCode[index]"
            type="text"
            maxlength="1"
            class="w-12 h-12 text-center text-xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            @input="handleOtpInput($event, index)"
            @keydown="handleOtpKeydown($event, index)"
            ref="otpInputs"
          />
        </div>

        <button
          @click="verifyOtp"
          :disabled="loading || otpCode.join('').length !== 6"
          class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
        >
          {{ loading ? '認証中...' : '認証する' }}
        </button>
        
        <div v-if="errorMessage" class="text-red-600 text-sm text-center">
          {{ errorMessage }}
        </div>
        <div v-if="successMessage" class="text-green-600 text-sm text-center">
          {{ successMessage }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'AdminLogin',
  setup() {
    const router = useRouter()
    const currentStep = ref(1)
    const loading = ref(false)
    const errorMessage = ref('')
    const successMessage = ref('')
    const tempId = ref('')
    const otpCode = ref(['', '', '', '', '', ''])
    const otpInputs = ref([])

    const form = reactive({
      phone: '',
      password: ''
    })

    // 13文字の一時IDを生成
    const generateTempId = () => {
      const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'
      let result = ''
      for (let i = 0; i < 13; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length))
      }
      return result
    }

    // ログイン処理
    const login = async () => {
      if (!form.phone || !form.password) {
        errorMessage.value = '電話番号とパスワードを入力してください。'
        return
      }

      loading.value = true
      errorMessage.value = ''

      try {
        const response = await fetch('/api/admin/auth/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            admin_phone: form.phone,
            admin_password: form.password,
            temp_id: tempId.value
          })
        })

        const data = await response.json()

        if (response.ok) {
          successMessage.value = '認証コードを送信しました。'
          currentStep.value = 2
          setTimeout(() => {
            if (otpInputs.value[0]) {
              otpInputs.value[0].focus()
            }
          }, 100)
        } else {
          errorMessage.value = data.message || 'ログインに失敗しました。'
        }
      } catch (error) {
        errorMessage.value = '通信エラーが発生しました。'
        console.error('Login error:', error)
      } finally {
        loading.value = false
      }
    }

    // OTP入力処理
    const handleOtpInput = (event, index) => {
      const value = event.target.value
      if (value.length === 1) {
        otpCode.value[index] = value
        if (index < 5 && otpInputs.value[index + 1]) {
          otpInputs.value[index + 1].focus()
        }
      }
    }

    // OTPキーダウン処理
    const handleOtpKeydown = (event, index) => {
      if (event.key === 'Backspace' && otpCode.value[index] === '') {
        if (index > 0 && otpInputs.value[index - 1]) {
          otpInputs.value[index - 1].focus()
        }
      }
    }

    // OTP認証
    const verifyOtp = async () => {
      const code = otpCode.value.join('')
      if (code.length !== 6) {
        errorMessage.value = '6桁のコードを入力してください。'
        return
      }

      loading.value = true
      errorMessage.value = ''
      successMessage.value = ''

      try {
        const response = await fetch('/api/admin/auth/verify-otp', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            temp_id: tempId.value,
            otp_code: code,
            admin_phone: form.phone
          })
        })

        const data = await response.json()

        if (response.ok) {
          successMessage.value = '認証が完了しました。'
          // 管理者としてログイン状態を保存
          localStorage.setItem('auth_token', data.token || 'admin_token')
          localStorage.setItem('user_role', 'admin')
          
          setTimeout(() => {
            router.push(data.redirect_url || '/admin')
          }, 1000)
        } else {
          errorMessage.value = data.message || '認証に失敗しました。'
        }
      } catch (error) {
        errorMessage.value = '通信エラーが発生しました。'
        console.error('OTP verification error:', error)
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      tempId.value = generateTempId()
    })

    return {
      currentStep,
      loading,
      errorMessage,
      successMessage,
      tempId,
      otpCode,
      otpInputs,
      form,
      login,
      handleOtpInput,
      handleOtpKeydown,
      verifyOtp
    }
  }
}
</script>
