<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-evox-gray rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center p-6 border-b border-gray-700">
        <h2 class="text-xl font-bold">イベント情報</h2>
        <button @click="$emit('close')" class="text-gray-400 hover:text-white">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
      
      <!-- タブ -->
      <div class="border-b border-gray-700">
        <div class="flex">
          <button 
            @click="activeTab = 'current'"
            :class="[
              'px-6 py-3 font-medium transition-colors',
              activeTab === 'current' 
                ? 'text-evox-blue border-b-2 border-evox-blue' 
                : 'text-gray-400 hover:text-white'
            ]"
          >
            開催中イベント
          </button>
          <button 
            @click="activeTab = 'upcoming'"
            :class="[
              'px-6 py-3 font-medium transition-colors',
              activeTab === 'upcoming' 
                ? 'text-evox-blue border-b-2 border-evox-blue' 
                : 'text-gray-400 hover:text-white'
            ]"
          >
            予定イベント
          </button>
        </div>
      </div>
      
      <!-- コンテンツ -->
      <div class="p-6">
        <!-- 開催中イベント -->
        <div v-if="activeTab === 'current'" class="space-y-6">
          <div 
            v-for="event in currentEvents" 
            :key="event.id"
            class="card border-l-4 border-evox-blue"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <h3 class="text-lg font-bold mb-2">{{ event.title }}</h3>
                <p class="text-gray-300 mb-3">{{ event.description }}</p>
                <div class="flex items-center space-x-4 text-sm text-gray-400">
                  <span>期間: {{ formatDate(event.startDate) }} - {{ formatDate(event.endDate) }}</span>
                  <span>報酬: {{ event.reward }}pt</span>
                </div>
              </div>
              <div class="ml-4">
                <span class="inline-block bg-evox-blue text-white px-3 py-1 rounded-full text-sm">
                  開催中
                </span>
              </div>
            </div>
            
            <div class="mt-4">
              <button class="btn-primary">
                参加する
              </button>
            </div>
          </div>
          
          <div v-if="currentEvents.length === 0" class="text-center py-8">
            <p class="text-gray-400">現在開催中のイベントはありません</p>
          </div>
        </div>
        
        <!-- 予定イベント -->
        <div v-if="activeTab === 'upcoming'" class="space-y-6">
          <div 
            v-for="event in upcomingEvents" 
            :key="event.id"
            class="card border-l-4 border-gray-600"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <h3 class="text-lg font-bold mb-2">{{ event.title }}</h3>
                <p class="text-gray-300 mb-3">{{ event.description }}</p>
                <div class="flex items-center space-x-4 text-sm text-gray-400">
                  <span>開始予定: {{ formatDate(event.startDate) }}</span>
                  <span>報酬: {{ event.reward }}pt</span>
                </div>
              </div>
              <div class="ml-4">
                <span class="inline-block bg-gray-600 text-white px-3 py-1 rounded-full text-sm">
                  予定
                </span>
              </div>
            </div>
            
            <div class="mt-4">
              <button class="btn-secondary" disabled>
                開始までお待ちください
              </button>
            </div>
          </div>
          
          <div v-if="upcomingEvents.length === 0" class="text-center py-8">
            <p class="text-gray-400">予定されているイベントはありません</p>
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
  name: 'EventsModal',
  emits: ['close'],
  setup() {
    const activeTab = ref('current')
    const currentEvents = ref([])
    const upcomingEvents = ref([])
    
    const fetchEvents = async () => {
      try {
        const response = await axios.get('/api/events')
        currentEvents.value = response.data.current || []
        upcomingEvents.value = response.data.upcoming || []
      } catch (error) {
        console.error('Failed to fetch events:', error)
        // エラーの場合は空の配列を設定（勝手にイベントを作成しない）
        currentEvents.value = []
        upcomingEvents.value = []
      }
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('ja-JP', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
      })
    }
    
    onMounted(() => {
      fetchEvents()
    })
    
    return {
      activeTab,
      currentEvents,
      upcomingEvents,
      formatDate
    }
  }
}
</script>
