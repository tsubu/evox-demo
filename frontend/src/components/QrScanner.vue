<template>
  <div class="qr-scanner">
    <div class="scanner-container">
      <!-- カメラビュー -->
      <div class="camera-view">
        <video ref="video" class="camera-video" autoplay muted></video>
        
        <!-- QRガイド枠 -->
        <div class="qr-guide">
          <div class="corner top-left"></div>
          <div class="corner top-right"></div>
          <div class="corner bottom-left"></div>
          <div class="corner bottom-right"></div>
        </div>
        
        <!-- スキャン状態表示 -->
        <div class="scan-status">
          <div v-if="scanning" class="scanning-indicator">
            <div class="spinner"></div>
            <span>QRコードをスキャン中...</span>
          </div>
          <div v-else-if="error" class="error-message">
            {{ error }}
          </div>
        </div>
      </div>
      
      <!-- コントロール -->
      <div class="scanner-controls">
        <button 
          @click="toggleCamera" 
          :disabled="!cameraSupported"
          class="btn-secondary"
        >
          {{ isCameraOn ? 'カメラ停止' : 'カメラ開始' }}
        </button>
        
        <button 
          @click="uploadImage" 
          class="btn-primary"
        >
          画像から読み取り
        </button>
        
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
    <div v-if="scanResult" class="scan-result">
      <div class="result-content">
        <h3>スキャン結果</h3>
        <p>{{ scanResult.message }}</p>
        <div v-if="scanResult.points" class="points-earned">
          <span class="points-label">獲得ポイント:</span>
          <span class="points-value">+{{ scanResult.points }}</span>
        </div>
        <button @click="closeResult" class="btn-primary">
          閉じる
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { BrowserMultiFormatReader } from '@zxing/library'
import { useAppStore } from '@/stores/app'
import { api } from '@/services/api'

const appStore = useAppStore()

const video = ref(null)
const fileInput = ref(null)
const codeReader = ref(null)
const stream = ref(null)

const scanning = ref(false)
const isCameraOn = ref(false)
const error = ref('')
const scanResult = ref(null)
const cameraSupported = ref(true)

onMounted(() => {
  initializeScanner()
})

onUnmounted(() => {
  stopCamera()
})

const initializeScanner = () => {
  try {
    codeReader.value = new BrowserMultiFormatReader()
    cameraSupported.value = true
  } catch (err) {
    cameraSupported.value = false
    error.value = 'QRコード読み取りがサポートされていません'
  }
}

const startCamera = async () => {
  if (!codeReader.value || !video.value) return
  
  try {
    scanning.value = true
    error.value = ''
    
    stream.value = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'environment' }
    })
    
    video.value.srcObject = stream.value
    isCameraOn.value = true
    
    // QRコード読み取り開始
    await codeReader.value.decodeFromVideoDevice(
      null,
      video.value,
      (result, err) => {
        if (result) {
          handleScanResult(result.text)
        }
        if (err && err.name !== 'NotFoundException') {
          error.value = 'カメラエラーが発生しました'
        }
      }
    )
  } catch (err) {
    error.value = 'カメラへのアクセスが拒否されました'
    cameraSupported.value = false
  } finally {
    scanning.value = false
  }
}

const stopCamera = () => {
  if (stream.value) {
    stream.value.getTracks().forEach(track => track.stop())
    stream.value = null
  }
  
  if (codeReader.value) {
    codeReader.value.reset()
  }
  
  isCameraOn.value = false
  scanning.value = false
}

const toggleCamera = () => {
  if (isCameraOn.value) {
    stopCamera()
  } else {
    startCamera()
  }
}

const uploadImage = () => {
  fileInput.value?.click()
}

const handleFileUpload = async (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  try {
    scanning.value = true
    error.value = ''
    
    const result = await codeReader.value.decodeFromInputVideoDevice(
      null,
      file
    )
    
    handleScanResult(result.text)
  } catch (err) {
    error.value = '画像からQRコードを読み取れませんでした'
  } finally {
    scanning.value = false
    event.target.value = '' // ファイル入力をリセット
  }
}

const handleScanResult = async (qrCode) => {
  try {
    scanning.value = true
    
    const response = await api.qr.claim({ code: qrCode })
    
    scanResult.value = {
      message: response.data.message,
      points: response.data.points_earned
    }
    
    // カメラを停止
    stopCamera()
    
    // 成功通知
    appStore.addNotification({
      type: 'success',
      message: 'QRコードの読み取りが完了しました'
    })
    
  } catch (err) {
    error.value = err.response?.data?.message || 'QRコードの処理に失敗しました'
    
    appStore.addNotification({
      type: 'error',
      message: error.value
    })
  } finally {
    scanning.value = false
  }
}

const closeResult = () => {
  scanResult.value = null
  error.value = ''
}
</script>

<style scoped>
.qr-scanner {
  @apply relative w-full max-w-md mx-auto;
}

.scanner-container {
  @apply bg-evox-black rounded-lg overflow-hidden;
}

.camera-view {
  @apply relative aspect-square bg-black;
}

.camera-video {
  @apply w-full h-full object-cover;
}

.qr-guide {
  @apply absolute inset-0 flex items-center justify-center;
}

.corner {
  @apply absolute w-8 h-8 border-2 border-evox-blue;
}

.top-left {
  @apply top-8 left-8 border-r-0 border-b-0;
}

.top-right {
  @apply top-8 right-8 border-l-0 border-b-0;
}

.bottom-left {
  @apply bottom-8 left-8 border-r-0 border-t-0;
}

.bottom-right {
  @apply bottom-8 right-8 border-l-0 border-t-0;
}

.scan-status {
  @apply absolute bottom-4 left-1/2 transform -translate-x-1/2;
}

.scanning-indicator {
  @apply flex items-center space-x-2 text-white;
}

.spinner {
  @apply w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin;
}

.error-message {
  @apply text-red-400 text-sm;
}

.scanner-controls {
  @apply p-4 space-y-2;
}

.scan-result {
  @apply fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75;
}

.result-content {
  @apply bg-evox-gray p-6 rounded-lg max-w-sm w-full mx-4 text-center;
}

.points-earned {
  @apply my-4 p-3 bg-evox-blue rounded;
}

.points-label {
  @apply text-sm text-gray-300;
}

.points-value {
  @apply text-lg font-bold text-white ml-2;
}
</style>
