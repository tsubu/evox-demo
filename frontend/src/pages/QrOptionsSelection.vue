<template>
  <div class="min-h-screen bg-evox-black py-8">
    <div class="max-w-4xl mx-auto px-6">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">カスタマイズオプション</h1>
        <p class="text-gray-400">{{ qrData.title }}</p>
        <div class="mt-2 text-evox-gold font-bold">
          +{{ qrData.points }}pt
        </div>
      </div>

      <!-- オプション選択フォーム -->
      <div class="card">
        <form @submit.prevent="submitOptions">
          <!-- アバター表情選択 -->
          <div class="option-group mb-6">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span class="mr-2">😊</span>
              アバター表情
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
              <button
                v-for="expression in qrData.options.expressions"
                :key="expression"
                type="button"
                @click="selectedOptions.expression = expression"
                :class="[
                  'option-button',
                  selectedOptions.expression === expression ? 'selected' : ''
                ]"
              >
                {{ expression }}
              </button>
            </div>
          </div>

          <!-- アバター行動選択 -->
          <div class="option-group mb-6">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span class="mr-2">🏃</span>
              アバター行動
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
              <button
                v-for="action in qrData.options.actions"
                :key="action"
                type="button"
                @click="selectedOptions.action = action"
                :class="[
                  'option-button',
                  selectedOptions.action === action ? 'selected' : ''
                ]"
              >
                {{ action }}
              </button>
            </div>
          </div>

          <!-- 背景色選択 -->
          <div class="option-group mb-6">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span class="mr-2">🎨</span>
              背景色
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
              <button
                v-for="color in qrData.options.background_colors"
                :key="color"
                type="button"
                @click="selectedOptions.background = color"
                :class="[
                  'option-button',
                  selectedOptions.background === color ? 'selected' : ''
                ]"
              >
                {{ color }}
              </button>
            </div>
          </div>

          <!-- エフェクト選択 -->
          <div class="option-group mb-6">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span class="mr-2">✨</span>
              エフェクト
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
              <button
                v-for="effect in qrData.options.effects"
                :key="effect"
                type="button"
                @click="selectedOptions.effect = effect"
                :class="[
                  'option-button',
                  selectedOptions.effect === effect ? 'selected' : ''
                ]"
              >
                {{ effect }}
              </button>
            </div>
          </div>

          <!-- サウンド選択 -->
          <div class="option-group mb-8">
            <h3 class="text-xl font-bold mb-4 flex items-center">
              <span class="mr-2">🔊</span>
              サウンド
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
              <button
                v-for="sound in qrData.options.sounds"
                :key="sound"
                type="button"
                @click="selectedOptions.sound = sound"
                :class="[
                  'option-button',
                  selectedOptions.sound === sound ? 'selected' : ''
                ]"
              >
                {{ sound }}
              </button>
            </div>
          </div>

          <!-- 選択確認 -->
          <div class="selected-summary mb-6 p-4 bg-gray-800 rounded-lg">
            <h4 class="text-lg font-bold mb-3">選択内容</h4>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
              <div>
                <span class="text-gray-400">表情:</span>
                <span class="ml-2">{{ selectedOptions.expression || '未選択' }}</span>
              </div>
              <div>
                <span class="text-gray-400">行動:</span>
                <span class="ml-2">{{ selectedOptions.action || '未選択' }}</span>
              </div>
              <div>
                <span class="text-gray-400">背景:</span>
                <span class="ml-2">{{ selectedOptions.background || '未選択' }}</span>
              </div>
              <div>
                <span class="text-gray-400">エフェクト:</span>
                <span class="ml-2">{{ selectedOptions.effect || '未選択' }}</span>
              </div>
              <div>
                <span class="text-gray-400">サウンド:</span>
                <span class="ml-2">{{ selectedOptions.sound || '未選択' }}</span>
              </div>
            </div>
          </div>

          <!-- 送信ボタン -->
          <div class="flex justify-center space-x-4">
            <button
              type="button"
              @click="goBack"
              class="btn-secondary px-8 py-3"
            >
              戻る
            </button>
            <button
              type="submit"
              :disabled="submitting"
              class="btn-primary px-8 py-3 disabled:opacity-50"
            >
              <span v-if="submitting">処理中...</span>
              <span v-else>選択を確定</span>
            </button>
          </div>
        </form>
      </div>

      <!-- エラーメッセージ -->
      <div v-if="error" class="card mt-6 bg-red-900 border-red-700">
        <div class="text-red-200">{{ error }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'

export default {
  name: 'QrOptionsSelection',
  setup() {
    const router = useRouter()
    const route = useRoute()
    
    const qrData = ref({})
    const selectedOptions = reactive({
      expression: '',
      action: '',
      background: '',
      effect: '',
      sound: ''
    })
    const submitting = ref(false)
    const error = ref('')

    onMounted(() => {
      // ルートパラメータからQRデータを取得
      if (route.params.qrData) {
        qrData.value = JSON.parse(route.params.qrData)
      } else {
        // セッションストレージから取得
        const storedData = sessionStorage.getItem('qrOptionsData')
        if (storedData) {
          qrData.value = JSON.parse(storedData)
        } else {
          router.push('/qr-scanner')
        }
      }
    })

    const submitOptions = async () => {
      submitting.value = true
      error.value = ''

      try {
        const response = await axios.post('/api/qr/process-options', {
          qr_code_id: qrData.value.qr_code_id,
          selected_expression: selectedOptions.expression,
          selected_action: selectedOptions.action,
          selected_background: selectedOptions.background,
          selected_effect: selectedOptions.effect,
          selected_sound: selectedOptions.sound
        })

        if (response.data.success) {
          // 成功時の処理
          sessionStorage.removeItem('qrOptionsData')
          router.push({
            name: 'QrResult',
            params: { 
              result: JSON.stringify(response.data.data)
            }
          })
        }
      } catch (err) {
        error.value = err.response?.data?.message || 'オプション処理中にエラーが発生しました。'
      } finally {
        submitting.value = false
      }
    }

    const goBack = () => {
      router.push('/qr-scanner')
    }

    return {
      qrData,
      selectedOptions,
      submitting,
      error,
      submitOptions,
      goBack
    }
  }
}
</script>

<style scoped>
.option-button {
  @apply px-4 py-3 border border-gray-600 rounded-lg text-center transition-all duration-200;
  @apply hover:border-evox-blue hover:bg-evox-blue hover:bg-opacity-10;
}

.option-button.selected {
  @apply border-evox-blue bg-evox-blue bg-opacity-20 text-evox-blue;
}

.card {
  @apply bg-gray-900 border border-gray-700 rounded-lg p-6;
}

.btn-primary {
  @apply bg-evox-blue text-white px-6 py-2 rounded-lg font-semibold;
  @apply hover:bg-blue-600 transition-colors duration-200;
}

.btn-secondary {
  @apply bg-gray-700 text-white px-6 py-2 rounded-lg font-semibold;
  @apply hover:bg-gray-600 transition-colors duration-200;
}
</style>
