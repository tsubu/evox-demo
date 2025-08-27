<template>
  <div class="min-h-screen bg-evox-black py-8">
    <div class="max-w-2xl mx-auto px-6">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">🎉 報酬を受け取りました！</h1>
        <p class="text-gray-400">QRコードの処理が完了しました</p>
      </div>

      <!-- 結果表示 -->
      <div class="card">
        <!-- 基本情報 -->
        <div class="mb-6">
          <h2 class="text-2xl font-bold mb-4">{{ result.title }}</h2>
          <p class="text-gray-300 mb-4">{{ result.description }}</p>
          
          <!-- ポイント表示 -->
          <div class="bg-evox-gold bg-opacity-20 border border-evox-gold rounded-lg p-4 mb-4">
            <div class="text-center">
              <div class="text-3xl font-bold text-evox-gold mb-2">
                +{{ result.points }}pt
              </div>
              <div class="text-sm text-gray-400">ポイントを獲得しました</div>
            </div>
          </div>
        </div>

        <!-- オプション選択結果 -->
        <div v-if="result.selected_options" class="mb-6">
          <h3 class="text-xl font-bold mb-4">選択したオプション</h3>
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div v-if="result.selected_options.expression" class="option-result">
              <div class="text-sm text-gray-400">表情</div>
              <div class="font-semibold">{{ result.selected_options.expression }}</div>
            </div>
            <div v-if="result.selected_options.action" class="option-result">
              <div class="text-sm text-gray-400">行動</div>
              <div class="font-semibold">{{ result.selected_options.action }}</div>
            </div>
            <div v-if="result.selected_options.background" class="option-result">
              <div class="text-sm text-gray-400">背景</div>
              <div class="font-semibold">{{ result.selected_options.background }}</div>
            </div>
            <div v-if="result.selected_options.effect" class="option-result">
              <div class="text-sm text-gray-400">エフェクト</div>
              <div class="font-semibold">{{ result.selected_options.effect }}</div>
            </div>
            <div v-if="result.selected_options.sound" class="option-result">
              <div class="text-sm text-gray-400">サウンド</div>
              <div class="font-semibold">{{ result.selected_options.sound }}</div>
            </div>
          </div>
        </div>

        <!-- ライブイベント情報 -->
        <div v-if="result.is_liveevent && result.artist_name" class="mb-6">
          <div class="bg-evox-blue bg-opacity-20 border border-evox-blue rounded-lg p-4">
            <h3 class="text-lg font-bold text-evox-blue mb-2">🎵 ライブイベント</h3>
            <div class="text-lg font-semibold">{{ result.artist_name }}</div>
            <div class="text-sm text-gray-400 mt-1">ライブイベントに参加しました</div>
          </div>
        </div>

        <!-- 処理時刻 -->
        <div class="text-center text-sm text-gray-400 mb-6">
          処理時刻: {{ formatDate(result.claimed_at) }}
        </div>

        <!-- アクションボタン -->
        <div class="flex justify-center space-x-4">
          <button
            @click="scanAnother"
            class="btn-primary px-8 py-3"
          >
            <span class="mr-2">📱</span>
            別のQRコードをスキャン
          </button>
          <button
            @click="goToMyPage"
            class="btn-secondary px-8 py-3"
          >
            <span class="mr-2">👤</span>
            マイページへ
          </button>
        </div>
      </div>

      <!-- 使用済みQRコードの場合 -->
      <div v-if="result.already_used" class="card mt-6 bg-yellow-900 border-yellow-700">
        <div class="text-center">
          <div class="text-2xl mb-2">⚠️</div>
          <h3 class="text-lg font-bold mb-2">このQRコードは既に使用済みです</h3>
          <p class="text-gray-300 mb-4">
            使用日時: {{ formatDate(result.used_at) }}
          </p>
          <button
            @click="scanAnother"
            class="btn-primary px-6 py-2"
          >
            別のQRコードをスキャン
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

export default {
  name: 'QrResult',
  setup() {
    const router = useRouter()
    const route = useRoute()
    
    const result = ref({})

    onMounted(() => {
      // ルートパラメータから結果データを取得
      if (route.params.result) {
        result.value = JSON.parse(route.params.result)
      } else {
        // セッションストレージから取得
        const storedResult = sessionStorage.getItem('qrResult')
        if (storedResult) {
          result.value = JSON.parse(storedResult)
          sessionStorage.removeItem('qrResult')
        } else {
          router.push('/qr-scanner')
        }
      }
    })

    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const scanAnother = () => {
      router.push('/qr-scanner')
    }

    const goToMyPage = () => {
      router.push('/mypage')
    }

    return {
      result,
      formatDate,
      scanAnother,
      goToMyPage
    }
  }
}
</script>

<style scoped>
.option-result {
  @apply bg-gray-800 rounded-lg p-3;
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
