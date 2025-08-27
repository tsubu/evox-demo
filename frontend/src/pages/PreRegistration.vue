<template>
  <div class="min-h-screen bg-evox-black">
    <!-- ヘッダー -->
    <div class="bg-evox-gray border-b border-gray-700">
      <div class="max-w-7xl mx-auto px-6 py-2">
        <div class="flex justify-center">
                      <div class="text-sm text-gray-400">
              ステップ {{ currentStep }}/3
            </div>
        </div>
      </div>
    </div>

    <!-- メインコンテンツ -->
    <div class="max-w-2xl mx-auto px-6 py-12">
      <!-- ステップ1: 電話番号入力 -->
      <div v-if="currentStep === 1" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">事前登録</h1>
          <p class="text-gray-400">EvoXの事前登録を行って限定特典をゲットしましょう</p>
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
                @input="errorMessage = ''"
              />
            </div>
            
            <!-- エラーメッセージ表示エリア -->
            <div v-if="errorMessage" class="bg-red-600 text-white text-sm px-4 py-3 rounded-lg">
              {{ errorMessage }}
            </div>
            
            <button type="submit" class="btn-primary w-full">
              認証コードを送信
            </button>
          </form>
          
          <!-- ログインリンク -->
          <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">
              既にアカウントをお持ちですか？
              <router-link to="/login" class="text-evox-blue hover:text-evox-blue-dark underline">
                ログインはこちら
              </router-link>
            </p>
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

      <!-- ステップ3: パスワード設定 -->
      <div v-if="currentStep === 3" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">パスワード設定</h1>
          <p class="text-gray-400">アカウント用のパスワードを設定してください</p>
        </div>

        <div class="bg-evox-gray rounded-lg p-8">
          <div class="mb-6">
            <div class="text-sm text-gray-400 mb-2">仮ID: {{ tempId }}</div>
          </div>

          <form @submit.prevent="submitStep3" class="space-y-6">
            <div>
              <label class="block text-sm font-medium mb-2">パスワード</label>
              <input 
                v-model="form.password"
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
                v-model="form.passwordConfirm"
                type="password"
                class="input-field w-full"
                placeholder="パスワードを再入力"
                required
              />
            </div>
            
            <div class="flex items-center">
              <input 
                v-model="form.rememberPassword"
                type="checkbox"
                id="remember"
                class="mr-2"
              />
              <label for="remember" class="text-sm">パスワードを記憶する</label>
            </div>
            
            <button type="submit" class="btn-primary w-full">
              登録完了
            </button>
          </form>
        </div>
      </div>





      <!-- 完了画面 -->
      <div v-if="currentStep === 4" class="space-y-8">
        <div class="text-center">
          <h1 class="text-3xl font-bold mb-4">登録完了！</h1>
          <p class="text-gray-400">EvoXの事前登録が完了しました</p>
        </div>

        <div class="bg-evox-gray rounded-lg p-8 text-center">
          <div class="text-6xl mb-4">🎉</div>
          <h2 class="text-2xl font-bold mb-4">おめでとうございます！</h2>
          <p class="text-gray-400 mb-6">
            事前登録が完了しました。<br>
            限定特典と100ポイントを獲得しました。
          </p>
          
          <div class="space-y-4 mb-8">
            <div class="bg-green-900/20 border border-green-600 rounded-lg p-4">
              <div class="text-green-400 font-medium">獲得ポイント</div>
              <div class="text-2xl font-bold text-evox-gold">100pt</div>
            </div>
            
            <div class="bg-blue-900/20 border border-blue-600 rounded-lg p-4">
              <div class="text-blue-400 font-medium">登録完了</div>
              <div class="text-lg font-medium">EvoXへようこそ！</div>
            </div>
          </div>

          <button @click="submitStep4" class="btn-primary w-full">
            マイページへ
          </button>
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
  name: 'PreRegistration',
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
      password: '',
      passwordConfirm: '',
      rememberPassword: false
    })
    
    // エラーメッセージの状態
    const errorMessage = ref('')



    onMounted(() => {
      tempId.value = getOrGenerateTempId()
    })

    const submitStep1 = async () => {
      try {
        appStore.setLoading(true)
        errorMessage.value = '' // エラーメッセージをクリア
        
        const response = await api.entry.store({
          phone: form.phone,
          temp_id: tempId.value
        })
        
        // SMS送信が失敗した場合のみOTPコードを表示
        if (response.data.data && response.data.data.otp_code) {
          console.log('SMS送信失敗 - OTPコード:', response.data.data.otp_code)
          alert('SMS送信に失敗しました。OTPコード: ' + response.data.data.otp_code)
        }
        
        handleApiSuccess('OTPコードを送信しました')
        currentStep.value = 2
      } catch (error) {
        console.log('Error in submitStep1:', error.response?.data)
        
        if (error.response?.data?.code === 'ALREADY_REGISTERED') {
          // 専用エリアにエラーメッセージを表示
          errorMessage.value = 'この電話番号は既に登録済みです。'
        } else {
          // その他のエラーは通常の通知システムで処理
          handleApiError(error)
        }
      } finally {
        appStore.setLoading(false)
      }
    }

    const submitStep2 = async () => {
      try {
        appStore.setLoading(true)
        console.log('OTP認証 - temp_id:', tempId.value, 'otp:', form.otp)
        const response = await api.entry.verify({
          temp_id: tempId.value,
          otp: form.otp
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
      if (form.password !== form.passwordConfirm) {
        appStore.showError('パスワードが一致しません')
        return
      }

      try {
        appStore.setLoading(true)
        
        console.log('Sending complete request with temp_id:', tempId.value)
        
        // 登録完了処理（パスワード設定も含む）
        const completeResponse = await api.entry.complete({
          temp_id: tempId.value,
          password: form.password
        })
        
        console.log('Registration complete response:', completeResponse.data)
        
        // 認証トークンをlocalStorageに保存
        if (completeResponse.data.data && completeResponse.data.data.token) {
          localStorage.setItem('auth_token', completeResponse.data.data.token)
          console.log('Auth token saved:', completeResponse.data.data.token)
        } else {
          console.error('No token found in response:', completeResponse.data)
        }
        
        // ユーザー情報をストアに保存
        if (completeResponse.data.data && completeResponse.data.data.user) {
          appStore.setAuthenticated(true, completeResponse.data.data.user)
        }
        
        // 登録完了後、仮IDをクリア
        clearTempId()
        
        handleApiSuccess('登録が完了しました')
        
        // キャラクター選択ページにリダイレクト
        router.push('/character-selection')
      } catch (error) {
        handleApiError(error)
      } finally {
        appStore.setLoading(false)
      }
    }



    const submitStep4 = async () => {
      try {
        appStore.setLoading(true)
        const response = await api.entry.complete({
          temp_id: tempId.value
        })
        
        console.log('Registration complete response:', response.data)
        
        // 認証トークンをlocalStorageに保存
        if (response.data.data && response.data.data.token) {
          localStorage.setItem('auth_token', response.data.data.token)
          console.log('Auth token saved:', response.data.data.token)
        } else {
          console.error('No token found in response:', response.data)
        }
        
        // ユーザー情報をストアに保存
        if (response.data.data && response.data.data.user) {
          appStore.setAuthenticated(true, response.data.data.user)
        }
        
        // 登録完了後、仮IDをクリア
        clearTempId()
        
        handleApiSuccess('登録が完了しました')
        
        // マイページに直接リダイレクト
        setTimeout(() => {
          router.push('/mypage')
        }, 1000)
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

      errorMessage,
      submitStep1,
      submitStep2,
      submitStep3,
      submitStep4
    }
  }
}
</script>
