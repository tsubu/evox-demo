<template>
  <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
      <!-- 背景オーバーレイ -->
      <div class="fixed inset-0 transition-opacity bg-black bg-opacity-75" @click="close"></div>

      <!-- モーダルコンテンツ -->
      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-evox-gray shadow-xl rounded-lg">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-white">事前登録</h3>
          <button @click="close" class="text-gray-400 hover:text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- ステップ1: 電話番号入力 -->
        <div v-if="currentStep === 1">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-300 mb-2">電話番号</label>
            <InternationalPhoneInput v-model="form.phone" />
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-300 mb-2">パスワード</label>
            <input
              v-model="form.password"
              type="password"
              class="w-full px-3 py-2 bg-evox-black border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-evox-blue"
              placeholder="8文字以上で入力"
              minlength="8"
            />
          </div>

          <div class="flex justify-end space-x-3">
            <button @click="close" class="btn-secondary">キャンセル</button>
            <button @click="submitStep1" :disabled="loading" class="btn-primary">
              <span v-if="loading">送信中...</span>
              <span v-else>認証コードを送信</span>
            </button>
          </div>
        </div>

        <!-- ステップ2: OTP入力 -->
        <div v-if="currentStep === 2">
          <div class="mb-4">
            <p class="text-sm text-gray-300 mb-4">
              {{ form.phone }} に送信された6桁の認証コードを入力してください
            </p>
            <OtpInput v-model="form.otp" />
          </div>

          <div class="flex justify-end space-x-3">
            <button @click="currentStep = 1" class="btn-secondary">戻る</button>
            <button @click="submitStep2" :disabled="loading" class="btn-primary">
              <span v-if="loading">認証中...</span>
              <span v-else>認証する</span>
            </button>
          </div>
        </div>

        <!-- ステップ3: パスワード確認 -->
        <div v-if="currentStep === 3">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-300 mb-2">パスワード確認</label>
            <input
              v-model="form.passwordConfirm"
              type="password"
              class="w-full px-3 py-2 bg-evox-black border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-evox-blue"
              placeholder="パスワードを再入力"
            />
          </div>

          <div class="mb-4">
            <label class="flex items-center">
              <input
                v-model="form.rememberMe"
                type="checkbox"
                class="mr-2"
              />
              <span class="text-sm text-gray-300">パスワードを記憶する</span>
            </label>
          </div>

          <div class="flex justify-end space-x-3">
            <button @click="currentStep = 2" class="btn-secondary">戻る</button>
            <button @click="submitStep3" :disabled="loading" class="btn-primary">
              <span v-if="loading">登録中...</span>
              <span v-else>登録完了</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useAppStore } from '@/stores/app'
import { api } from '@/services/api'
import InternationalPhoneInput from './InternationalPhoneInput.vue'
import OtpInput from './OtpInput.vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['close', 'success'])

const appStore = useAppStore()
const currentStep = ref(1)
const loading = ref(false)

const form = reactive({
  phone: '',
  password: '',
  passwordConfirm: '',
  otp: '',
  rememberMe: false
})

const close = () => {
  emit('close')
  resetForm()
}

const resetForm = () => {
  currentStep.value = 1
  Object.assign(form, {
    phone: '',
    password: '',
    passwordConfirm: '',
    otp: '',
    rememberMe: false
  })
}

const submitStep1 = async () => {
  if (!form.phone || !form.password) {
    appStore.addNotification({
      type: 'error',
      message: '電話番号とパスワードを入力してください'
    })
    return
  }

  if (form.password.length < 8) {
    appStore.addNotification({
      type: 'error',
      message: 'パスワードは8文字以上で入力してください'
    })
    return
  }

  try {
    loading.value = true
    await api.entry.store({
      phone: form.phone,
      password: form.password
    })
    
    appStore.addNotification({
      type: 'success',
      message: '認証コードを送信しました'
    })
    
    currentStep.value = 2
  } catch (error) {
    appStore.addNotification({
      type: 'error',
      message: error.response?.data?.message || 'エラーが発生しました'
    })
  } finally {
    loading.value = false
  }
}

const submitStep2 = async () => {
  if (!form.otp || form.otp.length !== 6) {
    appStore.addNotification({
      type: 'error',
      message: '6桁の認証コードを入力してください'
    })
    return
  }

  try {
    loading.value = true
    await api.auth.verifyOtp({
      phone: form.phone,
      code: form.otp
    })
    
    currentStep.value = 3
  } catch (error) {
    appStore.addNotification({
      type: 'error',
      message: error.response?.data?.message || '認証に失敗しました'
    })
  } finally {
    loading.value = false
  }
}

const submitStep3 = async () => {
  if (form.password !== form.passwordConfirm) {
    appStore.addNotification({
      type: 'error',
      message: 'パスワードが一致しません'
    })
    return
  }

  try {
    loading.value = true
    await api.entry.complete({
      phone: form.phone,
      password: form.password,
      remember_me: form.rememberMe
    })
    
    appStore.addNotification({
      type: 'success',
      message: '事前登録が完了しました'
    })
    
    emit('success')
    close()
  } catch (error) {
    appStore.addNotification({
      type: 'error',
      message: error.response?.data?.message || '登録に失敗しました'
    })
  } finally {
    loading.value = false
  }
}
</script>
