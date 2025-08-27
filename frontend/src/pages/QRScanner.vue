<template>
  <div class="min-h-screen bg-evox-black py-8">
    <div class="max-w-2xl mx-auto px-6">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">QRコード読み取り</h1>
        <p class="text-gray-400">QRコードをカメラで読み取ってポイントを獲得しよう</p>
      </div>
      
      <!-- QRスキャナー -->
      <div class="card">
        <div class="relative">
          <!-- カメラビュー -->
          <div class="relative bg-black rounded-lg overflow-hidden">
            <video 
              ref="video"
              class="w-full h-64 object-cover"
              autoplay
              playsinline
            ></video>
            
            <!-- QRガイド枠 -->
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="w-48 h-48 border-2 border-evox-blue relative">
                <!-- コーナーマーカー -->
                <div class="absolute top-0 left-0 w-6 h-6 border-t-4 border-l-4 border-evox-blue"></div>
                <div class="absolute top-0 right-0 w-6 h-6 border-t-4 border-r-4 border-evox-blue"></div>
                <div class="absolute bottom-0 left-0 w-6 h-6 border-b-4 border-l-4 border-evox-blue"></div>
                <div class="absolute bottom-0 right-0 w-6 h-6 border-b-4 border-r-4 border-evox-blue"></div>
              </div>
            </div>
            
            <!-- スキャンライン -->
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-48 h-0.5 bg-evox-blue animate-pulse"></div>
          </div>
          
          <!-- コントロール -->
          <div class="mt-4 flex justify-center space-x-4">
            <button 
              @click="startCamera"
              :disabled="isScanning"
              class="btn-primary"
            >
              カメラ開始
            </button>
            <button 
              @click="stopCamera"
              :disabled="!isScanning"
              class="btn-secondary"
            >
              カメラ停止
            </button>
            <button 
              @click="uploadImage"
              class="btn-secondary"
            >
              画像アップロード
            </button>
          </div>
          
          <!-- ファイルアップロード（非表示） -->
          <input 
            ref="fileInput"
            type="file"
            accept="image/*"
            @change="handleFileUpload"
            class="hidden"
          />
        </div>
      </div>
      
      <!-- 結果表示 -->
      <div v-if="scanResult" class="card mt-6">
        <h3 class="text-xl font-bold mb-4">スキャン結果</h3>
        <div class="space-y-3">
          <div class="flex justify-between items-center">
            <span>QRコード:</span>
            <span class="font-mono">{{ scanResult.code }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span>報酬:</span>
            <span class="text-evox-gold font-bold">+{{ scanResult.reward }}pt</span>
          </div>
          <div v-if="scanResult.item" class="flex justify-between items-center">
            <span>アイテム:</span>
            <span class="text-evox-blue">{{ scanResult.item }}</span>
          </div>
        </div>
        
        <button 
          @click="claimReward"
          :disabled="claiming"
          class="btn-primary w-full mt-4"
        >
          {{ claiming ? '処理中...' : '報酬を受け取る' }}
        </button>
      </div>
      
      <!-- エラーメッセージ -->
      <div v-if="error" class="card mt-6 bg-red-900 border-red-700">
        <div class="text-red-200">{{ error }}</div>
      </div>
      
      <!-- 成功メッセージ -->
      <div v-if="success" class="card mt-6 bg-green-900 border-green-700">
        <div class="text-green-200">{{ success }}</div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { BrowserMultiFormatReader } from '@zxing/library'
import axios from 'axios'

export default {
  name: 'QRScanner',
  setup() {
    const router = useRouter()
    const video = ref(null)
    const fileInput = ref(null)
    const isScanning = ref(false)
    const scanResult = ref(null)
    const error = ref('')
    const success = ref('')
    const claiming = ref(false)
    
    let codeReader = null
    let stream = null
    
    const startCamera = async () => {
      try {
        error.value = ''
        success.value = ''
        scanResult.value = null
        
        // カメラアクセス
        stream = await navigator.mediaDevices.getUserMedia({ 
          video: { 
            facingMode: 'environment',
            width: { ideal: 1280 },
            height: { ideal: 720 }
          } 
        })
        
        if (video.value) {
          video.value.srcObject = stream
          isScanning.value = true
        }
        
        // QRコード読み取り開始
        codeReader = new BrowserMultiFormatReader()
        codeReader.decodeFromVideoDevice(null, video.value, (result, err) => {
          if (result) {
            handleQRResult(result.text)
          }
        })
        
      } catch (err) {
        error.value = 'カメラへのアクセスに失敗しました: ' + err.message
      }
    }
    
    const stopCamera = () => {
      if (stream) {
        stream.getTracks().forEach(track => track.stop())
        stream = null
      }
      
      if (codeReader) {
        codeReader.reset()
        codeReader = null
      }
      
      if (video.value) {
        video.value.srcObject = null
      }
      
      isScanning.value = false
    }
    
    const uploadImage = () => {
      fileInput.value?.click()
    }
    
    const handleFileUpload = async (event) => {
      const file = event.target.files[0]
      if (!file) return
      
      try {
        error.value = ''
        success.value = ''
        scanResult.value = null
        
        const result = await codeReader.decodeFromInputVideoDevice(null, file)
        handleQRResult(result.text)
        
      } catch (err) {
        error.value = 'QRコードの読み取りに失敗しました'
      }
    }
    
    const handleQRResult = async (qrText) => {
      try {
        // QRコードの内容をサーバーに送信
        const response = await axios.post('/api/qr/claim', {
          code: qrText
        })
        
        // カメラを停止
        stopCamera()
        
        if (response.data.success) {
          const data = response.data.data
          
          // ゲームグッズ（オプション機能付き）の場合
          if (data.has_options) {
            // オプション選択画面に遷移
            sessionStorage.setItem('qrOptionsData', JSON.stringify(data))
            router.push('/qr-options-selection')
          } else {
            // ライブイベントやその他のQRコード（直接結果を表示）
            scanResult.value = data
          }
        } else {
          // エラーレスポンスの場合（使用済みなど）
          scanResult.value = response.data.data
        }
        
      } catch (err) {
        error.value = 'QRコードの処理に失敗しました: ' + (err.response?.data?.message || err.message)
      }
    }
    
    const claimReward = async () => {
      if (!scanResult.value) return
      
      claiming.value = true
      try {
        // 結果画面に遷移
        sessionStorage.setItem('qrResult', JSON.stringify(scanResult.value))
        router.push('/qr-result')
        
      } catch (err) {
        error.value = '報酬の受け取りに失敗しました: ' + (err.response?.data?.message || err.message)
      } finally {
        claiming.value = false
      }
    }
    
    onMounted(() => {
      // 自動的にカメラを開始
      startCamera()
    })
    
    onUnmounted(() => {
      stopCamera()
    })
    
    return {
      video,
      fileInput,
      isScanning,
      scanResult,
      error,
      success,
      claiming,
      startCamera,
      stopCamera,
      uploadImage,
      handleFileUpload,
      claimReward
    }
  }
}
</script>
