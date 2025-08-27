<template>
  <div class="penlight-control">
    <div class="penlight-status">
      <div class="status-indicator" :class="{ active: isActive }">
        <span class="icon">💡</span>
        <span>{{ statusText }}</span>
      </div>
    </div>
    
    <!-- アーティスト・楽曲選択 -->
    <div class="selection-section">
      <div class="artist-selection">
        <label>アーティスト選択:</label>
        <select v-model="selectedArtistId" @change="onArtistChange">
          <option value="">アーティストを選択してください</option>
          <option v-for="artist in artists" :key="artist.id" :value="artist.id">
            {{ artist.name }}
          </option>
        </select>
      </div>
      
      <div class="song-selection" v-if="selectedArtistId">
        <label>楽曲選択:</label>
        <select v-model="selectedSongId" @change="onSongChange">
          <option value="">楽曲を選択してください</option>
          <option v-for="song in songs" :key="song.id" :value="song.id">
            {{ song.title }}
          </option>
        </select>
      </div>
    </div>
    
    <div class="penlight-visualization" v-if="isActive">
      <div class="light-effect" :style="lightStyle"></div>
    </div>
    
          <div class="penlight-info">
        <div v-if="currentPreset">
          <p><strong>現在のプリセット:</strong> {{ currentPreset.name }}</p>
          <p><strong>楽曲:</strong> {{ currentPreset.song?.title }}</p>
          <p><strong>アーティスト:</strong> {{ currentPreset.song?.artist?.name }}</p>
          <p><strong>パターン:</strong> {{ currentPreset.pattern }}</p>
        </div>
        <p v-else-if="selectedSongId">楽曲が選択されています。プリセットの実行を待機中...</p>
        <p v-else-if="selectedArtistId">アーティストが選択されています。楽曲を選択してください</p>
        <p v-else>アーティストを選択してください</p>
        
        <div v-if="selectedArtistId" class="room-info">
          <p><strong>ルームID:</strong> {{ artists.find(a => a.id === selectedArtistId)?.name || 'default' }}</p>
          <p><small>選択したアーティスト名がルームIDとして使用されます</small></p>
        </div>
      </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import api from '../services/api'

export default {
  name: 'PenlightControl',
  props: {
    roomId: {
      type: String,
      default: 'default'
    }
  },
  setup(props) {
    const isActive = ref(false)
    const currentPreset = ref(null)
    const ws = ref(null)
    
    // 選択状態
    const selectedArtistId = ref('')
    const selectedSongId = ref('')
    
    // データ
    const artists = ref([])
    const songs = ref([])
    
    const statusText = computed(() => {
      if (!isActive.value) return 'ペンライト無効'
      return currentPreset.value ? 'プリセット実行中' : '待機中'
    })
    
    const lightStyle = computed(() => {
      if (!isActive.value || !currentPreset.value) return {}
      
      const preset = currentPreset.value
      const baseStyle = {
        backgroundColor: preset.color,
        opacity: preset.brightness / 100,
        transition: 'all 0.1s ease'
      }
      
      // パターンに応じたアニメーション
      switch (preset.pattern) {
        case 'blink':
          return {
            ...baseStyle,
            animation: `blink ${60 / preset.bpm}s infinite`
          }
        case 'fade':
          return {
            ...baseStyle,
            animation: `fade ${60 / preset.bpm}s infinite`
          }
        case 'wave':
          return {
            ...baseStyle,
            animation: `wave ${60 / preset.bpm}s infinite`
          }
        case 'pulse':
          return {
            ...baseStyle,
            animation: `pulse ${60 / preset.bpm}s infinite`
          }
        case 'rainbow':
          return {
            ...baseStyle,
            animation: `rainbow ${60 / preset.bpm}s infinite`
          }
        case 'strobe':
          return {
            ...baseStyle,
            animation: `strobe ${60 / preset.bpm}s infinite`
          }
        default:
          return baseStyle
      }
    })
    
    // アーティスト一覧取得
    const fetchArtists = async () => {
      try {
        const response = await api.admin.artists()
        artists.value = response.data.data
      } catch (error) {
        console.error('アーティスト一覧の取得に失敗:', error)
      }
    }
    
    // 楽曲一覧取得
    const fetchSongs = async (artistId) => {
      try {
        const response = await api.admin.songs({ artist_id: artistId })
        songs.value = response.data.data
      } catch (error) {
        console.error('楽曲一覧の取得に失敗:', error)
      }
    }
    
    // アーティスト変更時の処理
    const onArtistChange = () => {
      selectedSongId.value = ''
      songs.value = []
      if (selectedArtistId.value) {
        fetchSongs(selectedArtistId.value)
        
        // 選択したアーティスト名をルームIDとして使用
        const selectedArtist = artists.value.find(artist => artist.id === selectedArtistId.value)
        const roomId = selectedArtist ? selectedArtist.name : 'default'
        
        if (ws.value) {
          // 新しいルームに参加
          ws.value.send(JSON.stringify({
            type: 'join_room',
            room_id: roomId
          }))
        }
      }
    }
    
    // 楽曲変更時の処理
    const onSongChange = () => {
      if (selectedSongId.value && ws.value) {
        // 選択したアーティスト名をルームIDとして使用
        const selectedArtist = artists.value.find(artist => artist.id === selectedArtistId.value)
        const roomId = selectedArtist ? selectedArtist.name : 'default'
        
        // 新しいルームに参加
        ws.value.send(JSON.stringify({
          type: 'join_room',
          room_id: roomId
        }))
      }
    }
    
    // ペンライト機能専用WebSocket接続
    const connectWebSocket = () => {
      const wsUrl = `ws://localhost:8080/penlight`
      ws.value = new WebSocket(wsUrl)
      
      ws.value.onopen = () => {
        console.log('WebSocket接続確立')
        // 初期ルームに参加（デフォルトルーム）
        ws.value.send(JSON.stringify({
          type: 'join_room',
          room_id: 'default'
        }))
      }
      
      ws.value.onmessage = (event) => {
        const data = JSON.parse(event.data)
        
        switch (data.type) {
          case 'penlight_preset':
            currentPreset.value = data.data
            isActive.value = true
            console.log('プリセット受信:', data.data)
            break
            
          case 'penlight_deactivate':
            currentPreset.value = null
            isActive.value = false
            console.log('プリセット停止')
            break
            
          case 'room_joined':
            console.log('ルーム参加完了:', data.room_id)
            break
        }
      }
      
      ws.value.onerror = (error) => {
        console.error('WebSocketエラー:', error)
      }
      
      ws.value.onclose = () => {
        console.log('WebSocket接続終了')
        // コンポーネントがマウントされている時のみ再接続を試行
        // （ペンライト画面が非表示の時は再接続しない）
      }
    }
    
    // コンポーネントマウント時
    onMounted(() => {
      fetchArtists()
      // ペンライト画面がアクティブになった時にWebSocket接続を開始
      connectWebSocket()
    })
    
    // コンポーネントアンマウント時
    onUnmounted(() => {
      if (ws.value) {
        ws.value.close()
      }
    })
    
    // WebSocket接続を開始する関数（外部から呼び出し可能）
    const startWebSocket = () => {
      if (!ws.value || ws.value.readyState === WebSocket.CLOSED) {
        connectWebSocket()
      }
    }
    
    // WebSocket接続を停止する関数（外部から呼び出し可能）
    const stopWebSocket = () => {
      if (ws.value) {
        ws.value.close()
      }
    }
    
    return {
      isActive,
      currentPreset,
      selectedArtistId,
      selectedSongId,
      artists,
      songs,
      statusText,
      lightStyle,
      onArtistChange,
      onSongChange,
      startWebSocket,
      stopWebSocket
    }
  }
}
</script>

<style scoped>
.penlight-control {
  padding: 20px;
  background: #1a1a1a;
  border-radius: 10px;
  margin: 20px 0;
}

.penlight-status {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.status-indicator {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  background: #333;
  border-radius: 20px;
  font-weight: bold;
}

.status-indicator.active {
  background: #28a745;
}

.icon {
  font-size: 1.2em;
}

.selection-section {
  margin: 20px 0;
  padding: 15px;
  background: #2a2a2a;
  border-radius: 8px;
}

.artist-selection,
.song-selection {
  margin-bottom: 15px;
}

.artist-selection label,
.song-selection label {
  display: block;
  margin-bottom: 5px;
  color: #fff;
  font-weight: bold;
}

.artist-selection select,
.song-selection select {
  width: 100%;
  padding: 8px 12px;
  background: #333;
  color: #fff;
  border: 1px solid #555;
  border-radius: 4px;
  font-size: 14px;
}

.artist-selection select:focus,
.song-selection select:focus {
  outline: none;
  border-color: #007bff;
}

.penlight-visualization {
  margin: 20px 0;
  height: 100px;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
}

.light-effect {
  width: 100%;
  height: 100%;
  border-radius: 10px;
}

.penlight-info {
  margin-top: 15px;
  padding: 15px;
  background: #2a2a2a;
  border-radius: 8px;
}

.penlight-info p {
  margin: 5px 0;
  color: #ccc;
}

@keyframes blink {
  0%, 50% { opacity: 1; }
  51%, 100% { opacity: 0.3; }
}

@keyframes fade {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.5; }
}

@keyframes wave {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.1); }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

@keyframes rainbow {
  0% { filter: hue-rotate(0deg); }
  100% { filter: hue-rotate(360deg); }
}

@keyframes strobe {
  0%, 90% { opacity: 1; }
  95%, 100% { opacity: 0; }
}

@keyframes musicPulse {
  0%, 100% { transform: scale(1); opacity: 1; }
  50% { transform: scale(1.05); opacity: 0.8; }
}
</style>
