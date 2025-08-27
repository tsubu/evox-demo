<template>
  <div class="penlight-page">
    <!-- ページタイトル -->
    <div class="page-title">
      <h1>ペンライトモード</h1>
      <p>配信中のペンライトエフェクトをお楽しみください</p>
    </div>



    <!-- ペンライト表示エリア -->
    <div class="penlight-area">
      <div 
        ref="penlightEffect" 
        class="penlight-effect"
        :class="{ active: isActive }"
        @click="togglePenlight"
      ></div>
    </div>

    <!-- 認証状態表示（MyPageと同じように簡素化） -->
    <div class="auth-status-bar">
      <div class="status-item">
        <span class="status-label">状態:</span>
        <span class="status-value text-success">ペンライトモード</span>
      </div>
      <div class="status-item">
        <span class="status-label">認証:</span>
        <span class="status-value text-success">MyPageと同じ処理</span>
      </div>
    </div>

    <!-- ステータス表示 -->
    <div class="status-bar">
      <div class="status-item">
        <span class="status-label">状態:</span>
        <span class="status-value">{{ isActive ? 'アクティブ' : '非アクティブ' }}</span>
      </div>
      <div class="status-item">
        <span class="status-label">配信:</span>
        <span class="status-value">{{ isStreaming ? '配信中' : '停止中' }}</span>
      </div>
      <div class="status-item">
        <span class="status-label">モード:</span>
        <span class="status-value">{{ currentMode }}</span>
      </div>
      <div class="status-item">
        <span class="status-label">アーティスト:</span>
        <span class="status-value">{{ currentArtist }}</span>
      </div>
      <div class="status-item" v-if="currentPreset">
        <span class="status-label">プリセット:</span>
        <span class="status-value">{{ currentPreset.name }}</span>
      </div>
      <div class="status-item" v-if="currentPreset && currentPreset.audio_sync">
        <span class="status-label">音声連動:</span>
        <span class="status-value">ON</span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import logger from '@/utils/logger'

const router = useRouter()
const route = useRoute()

// 基本状態
const penlightEffect = ref(null)
const isActive = ref(true)
const isAuthenticated = ref(false)
const currentMode = ref('レインボー')
const currentArtist = ref('T-BOLAN')
const artistDescription = ref('デフォルトアーティスト')

// 配信データ受信用
const currentPreset = ref(null)
const isStreaming = ref(false)

// 音声連動機能用
let audioContext = null
let analyser = null
let microphone = null
let audioAnimationId = null

// 認証状態を確認（MyPageと同じように認証チェックなし）
const checkAuth = () => {
  // MyPageと同じように認証チェックは行わない
  console.log('PenlightMode - Auth check skipped (like MyPage)')
}



// 認証復元（MyPageと同じように不要）
const restoreAuth = async () => {
  console.log('PenlightMode - Auth restore skipped (like MyPage)')
}

// ペンライト制御
const togglePenlight = () => {
  isActive.value = !isActive.value
  updatePenlightEffect()
  logger.info('PenlightMode - Toggle penlight:', { isActive: isActive.value })
  console.log('PenlightMode - Toggle penlight:', isActive.value)
}

// 配信データを取得
const fetchStreamingData = async () => {
  try {
    console.log('🔍 配信データを取得中...')
    const response = await fetch('/api/penlight/streaming-status')
    const result = await response.json()
    
    console.log('📡 APIレスポンス:', result)
    
    if (result.success) {
      if (result.data.is_streaming && result.data.preset) {
        currentPreset.value = result.data.preset
        isStreaming.value = true
        console.log('🎵 配信データを受信しました:', result.data)
        console.log('📡 プリセット名:', result.data.preset.name)
        console.log('🎨 パターン:', result.data.preset.pattern)
        console.log('🎵 音楽連動:', result.data.preset.audio_sync ? 'ON' : 'OFF')
        console.log('⚡ 明度:', result.data.preset.brightness || 'N/A')
        console.log('🎼 BPM:', result.data.preset.bpm || 'N/A')
        console.log('💪 強度:', result.data.preset.intensity || 'N/A')
        console.log('🎨 ペンライト色:', result.data.preset.penlight_color || 'N/A')
        console.log('🎵 音楽感度:', result.data.preset.music_intensity || 'N/A')
        
        // 配信中の場合はペンライトを自動的にアクティブにする
        if (!isActive.value) {
          isActive.value = true
        }
        
        // 現在のモードとアーティストを更新
        currentMode.value = result.data.preset.pattern || 'レインボー'
        currentArtist.value = result.data.preset.song?.artist || 'T-BOLAN'
        
        // ペンライトエフェクトを更新
        updatePenlightEffect()
      } else {
        if (isStreaming.value) {
          console.log('📡 配信が停止されました')
          isStreaming.value = false
          currentPreset.value = null
        }
        console.log('📡 現在配信中のプリセットはありません')
      }
    } else {
      console.error('❌ APIエラー:', result.message)
    }
  } catch (error) {
    console.error('❌ 配信データの取得に失敗:', error)
  }
}

// 音声連動機能を開始
const startAudioReaction = (musicIntensity = 0.8) => {
  console.log('🎤 音声連動機能を開始します', { musicIntensity })
  
  // 既存の音声処理を停止
  stopAudioReaction()
  
  // マイク権限の確認
  if (navigator.permissions) {
    navigator.permissions.query({ name: 'microphone' }).then(permissionStatus => {
      console.log('🎤 マイク権限状態:', permissionStatus.state)
      
      if (permissionStatus.state === 'denied') {
        console.log('🎤 マイクアクセスが拒否されています。テスト音声を使用します')
        startAudioSimulation(musicIntensity)
        return
      }
      
      // 権限が許可されている場合は音声取得を開始
      startMicrophoneAccess(musicIntensity)
    }).catch(error => {
      console.log('🎤 権限確認エラー:', error)
      // 権限確認ができない場合は直接音声取得を試行
      startMicrophoneAccess(musicIntensity)
    })
  } else {
    // 権限APIがサポートされていない場合は直接音声取得を試行
    startMicrophoneAccess(musicIntensity)
  }
}

// マイクアクセス開始
const startMicrophoneAccess = (musicIntensity) => {
  // Web Audio APIの初期化
  if (!audioContext) {
    audioContext = new (window.AudioContext || window.webkitAudioContext)()
  }
  
  // マイクからの音声取得
  navigator.mediaDevices.getUserMedia({ 
    audio: {
      echoCancellation: true,
      noiseSuppression: true,
      autoGainControl: false,
      sampleRate: 44100
    } 
  })
  .then(stream => {
    console.log('🎤 マイクアクセスが許可されました')
    
    microphone = audioContext.createMediaStreamSource(stream)
    analyser = audioContext.createAnalyser()
    
    // アナライザーの設定
    analyser.fftSize = 512
    analyser.smoothingTimeConstant = 0.3
    
    microphone.connect(analyser)
    
    // 音声データの解析とペンライト制御
    const bufferLength = analyser.frequencyBinCount
    const dataArray = new Uint8Array(bufferLength)
    
    function updatePenlightFromAudio() {
      analyser.getByteFrequencyData(dataArray)
      
      // 音声レベルを計算（より詳細な分析）
      let sum = 0
      let maxValue = 0
      for (let i = 0; i < bufferLength; i++) {
        sum += dataArray[i]
        if (dataArray[i] > maxValue) {
          maxValue = dataArray[i]
        }
      }
      const average = sum / bufferLength
      
      // 音楽感度に応じて閾値を調整（より低い閾値）
      const threshold = 10 + (1 - musicIntensity) * 30
      
      console.log('🎤 音声レベル:', { average, maxValue, threshold, musicIntensity })
      
      if (average > threshold || maxValue > threshold * 2) {
        // 音声レベルが閾値を超えた場合、アニメーションを開始
        if (penlightEffect.value) {
          penlightEffect.value.style.animationPlayState = 'running'
          const brightnessMultiplier = 1 + (Math.min(average, 100) / 100) * 0.8
          penlightEffect.value.style.filter = `brightness(${brightnessMultiplier})`
          penlightEffect.value.style.opacity = '1'
          console.log('🎤 音声反応: アニメーション開始, 明度:', brightnessMultiplier)
        }
      } else {
        // 音声レベルが低い場合、アニメーションを一時停止して透明にする
        if (penlightEffect.value) {
          penlightEffect.value.style.animationPlayState = 'paused'
          penlightEffect.value.style.filter = 'brightness(1)'
          penlightEffect.value.style.opacity = '0.3'
          console.log('🎤 音声反応: アニメーション停止, 透明化')
        }
      }
      
      audioAnimationId = requestAnimationFrame(updatePenlightFromAudio)
    }
    
    updatePenlightFromAudio()
  })
  .catch(error => {
    console.log('🎤 マイクアクセスエラー:', error)
    console.log('🎤 テスト音声を使用します')
    startAudioSimulation(musicIntensity)
  })
}

// テスト音声シミュレーション
const startAudioSimulation = (musicIntensity) => {
  console.log('🎤 テスト音声シミュレーション開始')
  
  let beatCount = 0
  const beatInterval = 1000 / (120 / 60) // 120 BPM
  
  function simulateAudio() {
    beatCount++
    
    if (penlightEffect.value) {
      if (beatCount % 2 === 0) {
        // ビートが来た時
        penlightEffect.value.style.animationPlayState = 'running'
        penlightEffect.value.style.opacity = '1'
      } else {
        // ビートが止まった時
        penlightEffect.value.style.animationPlayState = 'paused'
        penlightEffect.value.style.opacity = '0.3'
      }
    }
    
    audioAnimationId = setTimeout(simulateAudio, beatInterval)
  }
  
  simulateAudio()
}

// 音声連動機能を停止
const stopAudioReaction = () => {
  console.log('🎤 音声連動機能を停止します')
  
  if (audioAnimationId) {
    if (typeof audioAnimationId === 'number') {
      cancelAnimationFrame(audioAnimationId)
    } else {
      clearTimeout(audioAnimationId)
    }
    audioAnimationId = null
  }
  
  if (microphone) {
    microphone.disconnect()
    microphone = null
  }
  
  if (analyser) {
    analyser = null
  }
  
  if (penlightEffect.value) {
    penlightEffect.value.style.animationPlayState = 'running'
    penlightEffect.value.style.opacity = '1'
  }
}

// ペンライトエフェクト更新
const updatePenlightEffect = () => {
  if (!penlightEffect.value) return
  
  if (isActive.value && currentPreset.value) {
    // プリセットデータに基づいてエフェクトを設定
    const preset = currentPreset.value
    const pattern = preset.pattern || 'solid'
    const bpm = preset.bpm || 120
    const brightness = preset.brightness || 80
    const penlightColor = preset.penlight_color || '#ff0000'
    const songColor = preset.color || '#ff0000'
    const audioSync = preset.audio_sync || false
    const musicIntensity = preset.music_intensity || 0.8
    
    console.log('🎨 ペンライトエフェクト更新:', {
      pattern,
      bpm,
      brightness,
      penlightColor,
      songColor,
      audioSync,
      musicIntensity
    })
    
    // グラデーション背景を作成
    const gradient = `linear-gradient(45deg, ${songColor} 0%, ${penlightColor} 50%, ${songColor} 100%)`
    penlightEffect.value.style.background = gradient
    penlightEffect.value.style.backgroundSize = '400% 400%'
    
    // アニメーションを設定
    if (pattern && pattern !== 'solid') {
      const duration = 60 / bpm // BPMから秒数を計算
      
      if (audioSync) {
        // 音声連動モードの場合、アニメーションを一時停止状態で開始
        penlightEffect.value.style.animation = `${pattern} ${duration}s infinite paused`
        penlightEffect.value.style.opacity = '0.3' // 音声がない時は透明
        
        // 音声連動機能を開始
        startAudioReaction(musicIntensity)
      } else {
        // 通常モードの場合
        penlightEffect.value.style.animation = `${pattern} ${duration}s infinite`
        penlightEffect.value.style.opacity = (brightness / 100).toString()
        
        // 音声連動機能を停止
        stopAudioReaction()
      }
    } else {
      penlightEffect.value.style.animation = 'none'
      penlightEffect.value.style.opacity = (brightness / 100).toString()
      
      // 音声連動機能を停止
      stopAudioReaction()
    }
    
    // 明度を設定（音声連動モードでない場合のみ）
    if (!audioSync) {
      penlightEffect.value.style.filter = `brightness(${brightness / 50})`
    }
  } else if (isActive.value) {
    // プリセットがない場合はデフォルトのレインボーエフェクト
    penlightEffect.value.style.background = 'linear-gradient(45deg, #ff0000, #00ff00, #0000ff, #ffff00, #ff00ff, #00ffff)'
    penlightEffect.value.style.backgroundSize = '400% 400%'
    penlightEffect.value.style.animation = 'rainbow 2s infinite'
    penlightEffect.value.style.opacity = '1'
    penlightEffect.value.style.filter = 'brightness(1)'
    
    // 音声連動機能を停止
    stopAudioReaction()
  } else {
    // 非アクティブ
    penlightEffect.value.style.animation = 'none'
    penlightEffect.value.style.opacity = '0'
    
    // 音声連動機能を停止
    stopAudioReaction()
  }
}



// 配信データの定期取得
let streamingInterval = null

// コンポーネントマウント（MyPageと同じように簡素化）
onMounted(() => {
  try {
    console.log('PenlightMode - Component mounted')
    console.log('🔧 デバッグ情報:')
    console.log('- 現在のURL:', window.location.href)
    console.log('- APIエンドポイント:', '/api/penlight/streaming-status')
    console.log('- ルート情報:', route.path, route.name)
    
    // MyPageと同じように認証チェックは行わない
    updatePenlightEffect()
    
    // 配信データを定期的に取得（2秒ごと）
    streamingInterval = setInterval(() => {
      fetchStreamingData()
    }, 2000)
    
    // 初回取得
    fetchStreamingData()
    
    console.log('PenlightMode - Component mounted successfully')
  } catch (error) {
    console.error('PenlightMode - Error during mount:', error)
  }
})

// コンポーネントアンマウント
onUnmounted(() => {
  console.log('PenlightMode - Component unmounted')
  
  // インターバルをクリア
  if (streamingInterval) {
    clearInterval(streamingInterval)
  }
  
  // 音声連動機能を停止
  stopAudioReaction()
})
</script>

<style scoped>
.penlight-page {
  min-height: calc(100vh - 200px); /* ヘッダーとフッターの高さを考慮 */
  background: #000;
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px;
}

.page-title {
  text-align: center;
  margin-bottom: 30px;
  padding: 20px;
  background: rgba(0, 0, 0, 0.8);
  border-radius: 10px;
  border: 1px solid #333;
}

.page-title h1 {
  font-size: 2.5rem;
  margin-bottom: 10px;
  color: #fff;
  text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

.page-title p {
  font-size: 1.1rem;
  color: #ccc;
  margin: 0;
}



.penlight-area {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  min-height: 400px;
  border: 2px solid #333;
  border-radius: 15px;
  background: rgba(0, 0, 0, 0.5);
  margin: 20px 0;
}

.penlight-effect {
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, #ff0000, #00ff00, #0000ff, #ffff00, #ff00ff, #00ffff);
  background-size: 400% 400%;
  cursor: pointer;
  transition: all 0.3s;
  position: absolute;
  top: 0;
  left: 0;
}

.penlight-effect.active {
  animation: rainbow 2s infinite;
}

.auth-status-bar {
  background: rgba(255, 0, 0, 0.1);
  border: 1px solid #ff0000;
  border-radius: 10px;
  padding: 15px;
  margin-top: 20px;
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  justify-content: center;
}

.status-bar {
  background: rgba(0, 0, 0, 0.8);
  border: 1px solid #333;
  border-radius: 10px;
  padding: 20px;
  margin-top: 20px;
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  justify-content: center;
}

.status-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.status-label {
  color: #ccc;
  font-size: 14px;
}

.status-value {
  color: #fff;
  font-weight: bold;
  font-size: 14px;
}

.text-success {
  color: #28a745 !important;
}

.text-danger {
  color: #dc3545 !important;
}

.restore-btn {
  background: #28a745;
  color: white;
  border: none;
  padding: 5px 10px;
  border-radius: 5px;
  cursor: pointer;
  font-size: 12px;
}

.restore-btn:hover {
  background: #218838;
}

@keyframes rainbow {
  0% { background-position: 0% 50%; }
  50% { background-position: 100% 50%; }
  100% { background-position: 0% 50%; }
}

@keyframes blink {
  0%, 20% { opacity: 1; transform: scale(1); filter: brightness(1.5); }
  21%, 40% { opacity: 0.3; transform: scale(0.8); filter: brightness(0.8); }
  41%, 60% { opacity: 1; transform: scale(1.2); filter: brightness(2); }
  61%, 80% { opacity: 0.4; transform: scale(0.9); filter: brightness(0.9); }
  81%, 100% { opacity: 1; transform: scale(1); filter: brightness(1.5); }
}

@keyframes fade {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0.3; }
}

@keyframes wave {
  0% { transform: translateX(-100%); }
  100% { transform: translateX(100%); }
}

@keyframes pulse {
  0%, 100% { transform: scale(1); filter: brightness(1); }
  50% { transform: scale(1.1); filter: brightness(1.3); }
}

@keyframes strobe {
  0%, 10% { opacity: 1; filter: brightness(2); }
  11%, 20% { opacity: 0.1; filter: brightness(0.5); }
  21%, 30% { opacity: 1; filter: brightness(2); }
  31%, 40% { opacity: 0.1; filter: brightness(0.5); }
  41%, 50% { opacity: 1; filter: brightness(2); }
  51%, 60% { opacity: 0.1; filter: brightness(0.5); }
  61%, 70% { opacity: 1; filter: brightness(2); }
  71%, 80% { opacity: 0.1; filter: brightness(0.5); }
  81%, 90% { opacity: 1; filter: brightness(2); }
  91%, 100% { opacity: 0.1; filter: brightness(0.5); }
}

@keyframes solid {
  0%, 100% { opacity: 1; }
}

@media (max-width: 768px) {
  .penlight-page {
    padding: 10px;
  }
  
  .page-title h1 {
    font-size: 2rem;
  }
  
  .page-title p {
    font-size: 1rem;
  }
  
  .penlight-area {
    min-height: 300px;
    margin: 10px 0;
  }
  
  .status-bar {
    flex-direction: column;
    gap: 10px;
    padding: 15px;
  }
  
  .status-item {
    justify-content: center;
  }
}
</style>
