<template>
  <div class="min-h-screen bg-black relative overflow-hidden">
    <!-- 同期ペンライトキャンバス -->
    <canvas 
      ref="syncCanvas" 
      class="w-full h-full absolute inset-0"
    ></canvas>

    <!-- コントロールパネル -->
    <div class="absolute top-4 left-4 z-10">
      <div class="bg-gray-900 bg-opacity-80 rounded-lg p-4 backdrop-blur-sm">
        <h2 class="text-white text-lg font-bold mb-4">🎆 同期ペンライト</h2>
        
        <!-- 接続状態 -->
        <div class="mb-4">
          <div class="flex items-center text-white text-sm">
            <div 
              :class="[
                'w-3 h-3 rounded-full mr-2',
                isConnected ? 'bg-green-500' : 'bg-red-500'
              ]"
            ></div>
            {{ isConnected ? '接続中' : '未接続' }}
          </div>
        </div>

        <!-- ルーム設定 -->
        <div class="mb-4">
          <label class="text-white text-sm mb-2 block">ルームID</label>
          <div class="flex space-x-2">
            <input 
              v-model="roomId" 
              type="text" 
              placeholder="ルームIDを入力"
              class="flex-1 bg-gray-800 text-white rounded px-3 py-2 text-sm"
              :disabled="isConnected"
            >
            <button 
              @click="joinRoom"
              :disabled="isConnected || !roomId"
              class="bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700 disabled:opacity-50"
            >
              参加
            </button>
          </div>
        </div>

        <!-- 参加者数 -->
        <div class="mb-4">
          <div class="text-white text-sm">
            参加者: {{ participants.length }}人
          </div>
        </div>

        <!-- 同期パターン -->
        <div class="mb-4">
          <label class="text-white text-sm mb-2 block">同期パターン</label>
          <select 
            v-model="syncPattern" 
            class="w-full bg-gray-800 text-white rounded px-3 py-2 text-sm"
            :disabled="!isConnected"
          >
            <option value="wave">波</option>
            <option value="circle">円</option>
            <option value="heart">ハート</option>
            <option value="star">星</option>
            <option value="random">ランダム</option>
          </select>
        </div>

        <!-- 色設定 -->
        <div class="mb-4">
          <label class="text-white text-sm mb-2 block">色</label>
          <div class="flex space-x-2">
            <button
              v-for="color in syncColors"
              :key="color.name"
              @click="setSyncColor(color.value)"
              :class="[
                'w-8 h-8 rounded-full border-2',
                selectedSyncColor === color.value ? 'border-white' : 'border-gray-600'
              ]"
              :style="{ backgroundColor: color.value }"
              :title="color.name"
              :disabled="!isConnected"
            ></button>
          </div>
        </div>

        <!-- コントロールボタン -->
        <div class="flex space-x-2">
          <button 
            @click="startSync"
            :disabled="!isConnected"
            class="bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700 disabled:opacity-50"
          >
            同期開始
          </button>
          <button 
            @click="stopSync"
            :disabled="!isConnected"
            class="bg-red-600 text-white px-3 py-2 rounded text-sm hover:bg-red-700 disabled:opacity-50"
          >
            同期停止
          </button>
          <button 
            @click="leaveRoom"
            :disabled="!isConnected"
            class="bg-gray-600 text-white px-3 py-2 rounded text-sm hover:bg-gray-700 disabled:opacity-50"
          >
            退出
          </button>
        </div>
      </div>
    </div>

    <!-- 参加者リスト -->
    <div class="absolute top-4 right-4 z-10">
      <div class="bg-gray-900 bg-opacity-80 rounded-lg p-4 backdrop-blur-sm">
        <h3 class="text-white text-sm font-bold mb-2">参加者</h3>
        <div class="space-y-1">
          <div 
            v-for="participant in participants" 
            :key="participant.id"
            class="text-white text-xs flex items-center"
          >
            <div 
              class="w-2 h-2 rounded-full mr-2"
              :style="{ backgroundColor: participant.color }"
            ></div>
            {{ participant.name }}
          </div>
        </div>
      </div>
    </div>

    <!-- 戻るボタン -->
    <div class="absolute bottom-4 right-4 z-10">
      <button 
        @click="goBack"
        class="bg-gray-900 bg-opacity-80 text-white px-4 py-2 rounded-lg backdrop-blur-sm hover:bg-gray-800"
      >
        ← 戻る
      </button>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

export default {
  name: 'PenlightSync',
  setup() {
    const router = useRouter()
    const syncCanvas = ref(null)
    const ctx = ref(null)
    const isConnected = ref(false)
    const roomId = ref('')
    const participants = ref([])
    const syncPattern = ref('wave')
    const selectedSyncColor = ref('#FF0000')
    const isSyncing = ref(false)
    
    let ws = null
    let animationId = null
    let syncInterval = null

    const syncColors = [
      { name: '赤', value: '#FF0000' },
      { name: '青', value: '#0000FF' },
      { name: '緑', value: '#00FF00' },
      { name: '黄', value: '#FFFF00' },
      { name: '紫', value: '#800080' },
      { name: 'オレンジ', value: '#FFA500' },
      { name: 'ピンク', value: '#FFC0CB' },
      { name: '白', value: '#FFFFFF' },
    ]

    onMounted(() => {
      initCanvas()
    })

    onUnmounted(() => {
      if (ws) {
        ws.close()
      }
      if (animationId) {
        cancelAnimationFrame(animationId)
      }
      if (syncInterval) {
        clearInterval(syncInterval)
      }
    })

    const initCanvas = () => {
      const canvas = syncCanvas.value
      ctx.value = canvas.getContext('2d')
      
      const resizeCanvas = () => {
        canvas.width = window.innerWidth
        canvas.height = window.innerHeight
      }
      
      resizeCanvas()
      window.addEventListener('resize', resizeCanvas)
    }

    const joinRoom = () => {
      if (!roomId.value) return
      
      // シミュレーション（WebSocket接続は使用しない）
      isConnected.value = true
      participants.value = [
        { id: 1, name: 'ユーザー1', color: '#FF0000' },
        { id: 2, name: 'ユーザー2', color: '#0000FF' },
        { id: 3, name: 'あなた', color: selectedSyncColor.value },
      ]
      
      // 接続成功メッセージ
      console.log(`ルーム ${roomId.value} に参加しました（シミュレーション）`)
    }

    const leaveRoom = () => {
      // WebSocket接続は使用しない（ペンライト機能専用）
      isConnected.value = false
      participants.value = []
      isSyncing.value = false
      
      if (syncInterval) {
        clearInterval(syncInterval)
        syncInterval = null
      }
      
      // キャンバスをクリア
      if (ctx.value) {
        ctx.value.clearRect(0, 0, syncCanvas.value.width, syncCanvas.value.height)
      }
    }

    const startSync = () => {
      if (!isConnected.value) return
      
      isSyncing.value = true
      
      // 同期パターンの実行
      syncInterval = setInterval(() => {
        drawSyncPattern()
      }, 100)
    }

    const stopSync = () => {
      isSyncing.value = false
      
      if (syncInterval) {
        clearInterval(syncInterval)
        syncInterval = null
      }
      
      // キャンバスをクリア
      if (ctx.value) {
        ctx.value.clearRect(0, 0, syncCanvas.value.width, syncCanvas.value.height)
      }
    }

    const drawSyncPattern = () => {
      if (!ctx.value) return
      
      ctx.value.clearRect(0, 0, syncCanvas.value.width, syncCanvas.value.height)
      
      const time = Date.now() * 0.001
      const centerX = syncCanvas.value.width / 2
      const centerY = syncCanvas.value.height / 2
      
      switch (syncPattern.value) {
        case 'wave':
          drawWavePattern(time, centerX, centerY)
          break
        case 'circle':
          drawCirclePattern(time, centerX, centerY)
          break
        case 'heart':
          drawHeartPattern(time, centerX, centerY)
          break
        case 'star':
          drawStarPattern(time, centerX, centerY)
          break
        case 'random':
          drawRandomPattern(time, centerX, centerY)
          break
      }
    }

    const drawWavePattern = (time, centerX, centerY) => {
      ctx.value.strokeStyle = selectedSyncColor.value
      ctx.value.lineWidth = 3
      ctx.value.beginPath()
      
      for (let x = 0; x < syncCanvas.value.width; x += 10) {
        const y = centerY + Math.sin(x * 0.01 + time * 2) * 100
        if (x === 0) {
          ctx.value.moveTo(x, y)
        } else {
          ctx.value.lineTo(x, y)
        }
      }
      
      ctx.value.stroke()
    }

    const drawCirclePattern = (time, centerX, centerY) => {
      const radius = 100 + Math.sin(time * 3) * 50
      
      ctx.value.strokeStyle = selectedSyncColor.value
      ctx.value.lineWidth = 5
      ctx.value.beginPath()
      ctx.value.arc(centerX, centerY, radius, 0, Math.PI * 2)
      ctx.value.stroke()
    }

    const drawHeartPattern = (time, centerX, centerY) => {
      const scale = 1 + Math.sin(time * 2) * 0.3
      
      ctx.value.fillStyle = selectedSyncColor.value
      ctx.value.beginPath()
      
      for (let angle = 0; angle < Math.PI * 2; angle += 0.1) {
        const x = centerX + 16 * Math.pow(Math.sin(angle), 3) * scale
        const y = centerY - (13 * Math.cos(angle) - 5 * Math.cos(2 * angle) - 2 * Math.cos(3 * angle) - Math.cos(4 * angle)) * scale
        
        if (angle === 0) {
          ctx.value.moveTo(x, y)
        } else {
          ctx.value.lineTo(x, y)
        }
      }
      
      ctx.value.fill()
    }

    const drawStarPattern = (time, centerX, centerY) => {
      const points = 5
      const outerRadius = 100 + Math.sin(time * 2) * 30
      const innerRadius = 40
      
      ctx.value.fillStyle = selectedSyncColor.value
      ctx.value.beginPath()
      
      for (let i = 0; i < points * 2; i++) {
        const angle = (i * Math.PI) / points
        const radius = i % 2 === 0 ? outerRadius : innerRadius
        const x = centerX + Math.cos(angle) * radius
        const y = centerY + Math.sin(angle) * radius
        
        if (i === 0) {
          ctx.value.moveTo(x, y)
        } else {
          ctx.value.lineTo(x, y)
        }
      }
      
      ctx.value.closePath()
      ctx.value.fill()
    }

    const drawRandomPattern = (time, centerX, centerY) => {
      const particles = 20
      
      for (let i = 0; i < particles; i++) {
        const x = centerX + Math.sin(time + i) * 200
        const y = centerY + Math.cos(time + i) * 200
        const size = 10 + Math.sin(time * 2 + i) * 5
        
        ctx.value.fillStyle = selectedSyncColor.value
        ctx.value.globalAlpha = 0.7
        ctx.value.beginPath()
        ctx.value.arc(x, y, size, 0, Math.PI * 2)
        ctx.value.fill()
      }
      
      ctx.value.globalAlpha = 1
    }

    const setSyncColor = (color) => {
      selectedSyncColor.value = color
    }

    const goBack = () => {
      router.push('/penlight-app')
    }

    return {
      syncCanvas,
      isConnected,
      roomId,
      participants,
      syncPattern,
      selectedSyncColor,
      syncColors,
      joinRoom,
      leaveRoom,
      startSync,
      stopSync,
      setSyncColor,
      goBack
    }
  }
}
</script>

<style scoped>
canvas {
  touch-action: none;
}
</style>
