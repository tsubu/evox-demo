<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-evox-gray rounded-lg max-w-6xl w-full max-h-[90vh] overflow-y-auto">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center p-6 border-b border-gray-700">
        <h2 class="text-xl font-bold">GIFTへの交換</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <!-- ポイント表示 -->
      <div class="p-6 border-b border-gray-700">
        <div class="flex justify-between items-center">
          <div>
            <span class="text-gray-400">利用可能ポイント:</span>
            <span class="text-2xl font-bold text-evox-gold ml-2">{{ availablePoints }}pt</span>
          </div>
          <div>
            <span class="text-gray-400">交換可能アイテム:</span>
            <span class="text-lg font-bold text-evox-blue ml-2">{{ availableItems.length }}個</span>
          </div>
        </div>
      </div>
      
      <!-- アイテム一覧 -->
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="item in availableItems" 
            :key="item.id"
            class="card hover:bg-gray-700 transition-colors"
          >
            <div class="text-center">
              <img 
                :src="item.image || '/images/itemshop.png'" 
                :alt="item.name"
                class="w-full h-32 object-cover rounded mb-4"
              />
              <h3 class="text-lg font-bold mb-2">{{ item.name }}</h3>
              <p class="text-gray-300 text-sm mb-4">{{ item.description }}</p>
              
              <div class="flex justify-between items-center mb-4">
                <span class="text-evox-gold font-bold">{{ item.points }}pt</span>
                <span class="text-sm text-gray-400">
                  残り: {{ item.remainingCount }}個
                </span>
              </div>
              
              <button 
                @click="exchangeItem(item)"
                :disabled="item.points > availablePoints || item.remainingCount <= 0"
                :class="[
                  'w-full py-2 px-4 rounded transition-colors',
                  item.points <= availablePoints && item.remainingCount > 0
                    ? 'bg-evox-blue hover:bg-blue-700 text-white'
                    : 'bg-gray-600 text-gray-400 cursor-not-allowed'
                ]"
              >
                {{ 
                  item.points > availablePoints 
                    ? 'ポイント不足' 
                    : item.remainingCount <= 0 
                      ? '在庫切れ' 
                      : '交換する'
                }}
              </button>
            </div>
          </div>
        </div>
        
        <div v-if="availableItems.length === 0" class="text-center py-8">
          <p class="text-gray-400">交換可能なアイテムはありません</p>
        </div>
      </div>
      
      <!-- 交換履歴 -->
      <div class="p-6 border-t border-gray-700">
        <h3 class="text-lg font-bold mb-4">交換履歴</h3>
        <div class="space-y-3">
          <div 
            v-for="history in exchangeHistory" 
            :key="history.id"
            class="flex justify-between items-center py-2 border-b border-gray-700 last:border-b-0"
          >
            <div>
              <div class="font-medium">{{ history.itemName }}</div>
              <div class="text-sm text-gray-400">{{ formatDate(history.date) }}</div>
            </div>
            <div class="text-evox-gold font-semibold">
              -{{ history.points }}pt
            </div>
          </div>
          
          <div v-if="exchangeHistory.length === 0" class="text-center py-4">
            <p class="text-gray-400">交換履歴はありません</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'GiftModal',
  emits: ['close'],
  setup() {
    const availablePoints = ref(0)
    const availableItems = ref([])
    const exchangeHistory = ref([])
    
    const fetchGiftData = async () => {
      try {
        const [pointsResponse, itemsResponse, historyResponse] = await Promise.all([
          axios.get('/api/mypage/points'),
          axios.get('/api/gift/items'),
          axios.get('/api/gift/history')
        ])
        
        availablePoints.value = pointsResponse.data.availablePoints || 0
        availableItems.value = itemsResponse.data || []
        exchangeHistory.value = historyResponse.data || []
      } catch (error) {
        console.error('Failed to fetch gift data:', error)
        // フォールバックデータ
        availablePoints.value = 100
        availableItems.value = [
          {
            id: 1,
            name: '限定アバター',
            description: '事前登録者限定の特別アバター',
            points: 50,
            remainingCount: 10,
            image: '/images/itemshop.png'
          },
          {
            id: 2,
            name: 'ボーナスポイント',
            description: 'ゲーム内で使用できるボーナスポイント',
            points: 100,
            remainingCount: 5,
            image: '/images/itemshop.png'
          },
          {
            id: 3,
            name: '特別アイテム',
            description: 'ゲーム内で使用できる特別アイテム',
            points: 200,
            remainingCount: 3,
            image: '/images/itemshop.png'
          }
        ]
        exchangeHistory.value = []
      }
    }
    
    const exchangeItem = async (item) => {
      try {
        await axios.post('/api/gift/exchange', {
          item_id: item.id
        })
        
        // データを再取得
        await fetchGiftData()
        
        alert(`${item.name}の交換が完了しました！`)
        
      } catch (error) {
        alert('交換に失敗しました: ' + (error.response?.data?.message || 'エラーが発生しました'))
      }
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    onMounted(() => {
      fetchGiftData()
    })
    
    return {
      availablePoints,
      availableItems,
      exchangeHistory,
      exchangeItem,
      formatDate
    }
  }
}
</script>
