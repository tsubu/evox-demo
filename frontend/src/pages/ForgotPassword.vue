<template>
  <div class="min-h-screen bg-evox-black">
    <!-- ヘッダー -->
    <div class="bg-evox-gray border-b border-gray-700">
      <div class="max-w-7xl mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <router-link to="/" class="flex items-center">
            <img src="/images/logo_evox.png" alt="EvoX" class="h-8" />
          </router-link>
          <div class="text-sm text-gray-400">
            ステップ {{ currentStep }}/4
          </div>
        </div>
      </div>
    </div>

    <!-- メインコンテンツ -->
    <div class="max-w-2xl mx-auto px-6 py-12">
      <!-- ステップ1: 電話番号入力 -->
      <div v-if="currentStep === 1" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">パスワードリセット</h1>
          <p class="text-gray-400">ご利用の電話番号を入力してください</p>
        </div>

        <div class="bg-evox-gray rounded-lg p-8">
          <div class="mb-6">
            <div class="text-sm text-gray-400 mb-2">仮ID: {{ tempId }}</div>
          </div>

          <form @submit.prevent="submitStep1" class="space-y-6">
            <div>
              <InternationalPhoneInput
                v-model="form.phone"
                label="電話番号"
                placeholder="電話番号を入力"
                required
              />
            </div>
            
            <button type="submit" class="btn-primary w-full">
              認証コードを発行
            </button>
          </form>

          <div class="mt-6 pt-6 border-t border-gray-700">
            <router-link to="/login" class="text-evox-blue hover:text-blue-400 text-sm">
              ログインページに戻る
            </router-link>
          </div>
        </div>
      </div>

      <!-- ステップ2: OTP認証 -->
      <div v-if="currentStep === 2" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">認証コード入力</h1>
          <p class="text-gray-400">携帯番号に届いた6桁のコードを入力してください</p>
        </div>

        <div class="bg-evox-gray rounded-lg p-8">
          <div class="mb-6">
            <div class="text-sm text-gray-400 mb-2">仮ID: {{ tempId }}</div>
          </div>

          <form @submit.prevent="submitStep2" class="space-y-6">
            <OtpInput v-model="form.otp" />
            
            <button type="submit" class="btn-primary w-full">
              認証する
            </button>
          </form>
        </div>
      </div>

      <!-- ステップ3: 新しいパスワード設定 -->
      <div v-if="currentStep === 3" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">新しいパスワード設定</h1>
          <p class="text-gray-400">新しいパスワードを設定してください</p>
        </div>

        <div class="bg-evox-gray rounded-lg p-8">
          <div class="mb-6">
            <div class="text-sm text-gray-400 mb-2">仮ID: {{ tempId }}</div>
          </div>

          <form @submit.prevent="submitStep3" class="space-y-6">
            <div>
              <label class="block text-sm font-medium mb-2">新しいパスワード</label>
              <input 
                v-model="form.newPassword"
                type="password"
                class="input-field w-full"
                placeholder="8文字以上の英数字"
                minlength="8"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">パスワード確認</label>
              <input 
                v-model="form.newPasswordConfirm"
                type="password"
                class="input-field w-full"
                placeholder="パスワードを再入力"
                required
              />
            </div>
            
            <button type="submit" class="btn-primary w-full">
              パスワードを変更する
            </button>
          </form>
        </div>
      </div>

      <!-- ステップ4: 完了 -->
      <div v-if="currentStep === 4" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">パスワード変更完了！</h1>
          <p class="text-gray-400">パスワードの変更が完了しました</p>
        </div>

        <div class="bg-evox-gray rounded-lg p-8 text-center">
          <div class="text-6xl mb-4">✅</div>
          <h2 class="text-2xl font-bold mb-4">変更完了</h2>
          <p class="text-gray-400 mb-6">
            パスワードの変更が正常に完了しました。<br>
            新しいパスワードでログインしてください。
          </p>
          
          <router-link to="/login" class="btn-primary w-full">
            ログインページへ
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { api, handleApiError, handleApiSuccess } from '@/services/api'
import { getOrGenerateTempId, clearTempId } from '@/utils/cookies'
import OtpInput from '@/components/OtpInput.vue'
import InternationalPhoneInput from '@/components/InternationalPhoneInput.vue'

export default {
  name: 'ForgotPassword',
  components: {
    OtpInput,
    InternationalPhoneInput
  },
  setup() {
    const router = useRouter()
    const appStore = useAppStore()
    
    const currentStep = ref(1)
    const tempId = ref('')
    
    const form = reactive({
      phone: '',
      otp: '',
      newPassword: '',
      newPasswordConfirm: ''
    })

    onMounted(() => {
      tempId.value = getOrGenerateTempId()
    })

    const submitStep1 = async () => {
      try {
        appStore.setLoading(true)
        const response = await api.auth.forgotPassword({
          phone: form.phone,
          temp_id: tempId.value
        })
        
        handleApiSuccess('OTPコードを送信しました')
        currentStep.value = 2
      } catch (error) {
        handleApiError(error)
      } finally {
        appStore.setLoading(false)
      }
    }

    const submitStep2 = async () => {
      try {
        appStore.setLoading(true)
        const response = await api.auth.verifyOtp({
          phone: form.phone,
          code: form.otp,
          temp_id: tempId.value
        })
        
        handleApiSuccess('認証が完了しました')
        currentStep.value = 3
      } catch (error) {
        handleApiError(error)
      } finally {
        appStore.setLoading(false)
      }
    }

    const submitStep3 = async () => {
      if (form.newPassword !== form.newPasswordConfirm) {
        appStore.showError('パスワードが一致しません')
        return
      }

      try {
        appStore.setLoading(true)
        const response = await api.auth.resetPassword({
          phone: form.phone,
          password: form.newPassword,
          temp_id: tempId.value
        })
        
        // パスワードリセット完了後、仮IDをクリア
        clearTempId()
        
        handleApiSuccess('パスワードを変更しました')
        currentStep.value = 4
      } catch (error) {
        handleApiError(error)
      } finally {
        appStore.setLoading(false)
      }
    }

    return {
      currentStep,
      tempId,
      form,
      submitStep1,
      submitStep2,
      submitStep3
    }
  }
}
</script>
