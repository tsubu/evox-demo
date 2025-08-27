<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-evox-gray rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
      <!-- ヘッダー -->
      <div class="flex justify-between items-center p-6 border-b border-gray-700">
        <h2 class="text-xl font-bold">お知らせ</h2>
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
            @click="activeTab = 'important'"
            :class="[
              'px-6 py-3 font-medium transition-colors',
              activeTab === 'important' 
                ? 'text-evox-blue border-b-2 border-evox-blue' 
                : 'text-gray-400 hover:text-white'
            ]"
          >
            重要なお知らせ
          </button>
          <button 
            @click="activeTab = 'general'"
            :class="[
              'px-6 py-3 font-medium transition-colors',
              activeTab === 'general' 
                ? 'text-evox-blue border-b-2 border-evox-blue' 
                : 'text-gray-400 hover:text-white'
            ]"
          >
            お知らせ
          </button>
        </div>
      </div>
      
      <!-- コンテンツ -->
      <div class="p-6">
        <!-- 重要なお知らせ -->
        <div v-if="activeTab === 'important'" class="space-y-6">
          <div 
            v-for="news in importantNews" 
            :key="news.id"
            class="card border-l-4 border-red-500"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                  <span class="inline-block bg-red-500 text-white px-2 py-1 rounded text-xs">
                    重要
                  </span>
                  <span class="text-sm text-gray-400">{{ formatDate(news.date) }}</span>
                </div>
                <h3 class="text-lg font-bold mb-2">{{ news.title }}</h3>
                <p class="text-gray-300">{{ news.content }}</p>
              </div>
            </div>
          </div>
          
          <div v-if="importantNews.length === 0" class="text-center py-8">
            <p class="text-gray-400">重要な知らせはありません</p>
          </div>
        </div>
        
        <!-- 一般のお知らせ -->
        <div v-if="activeTab === 'general'" class="space-y-6">
          <div 
            v-for="news in generalNews" 
            :key="news.id"
            class="card border-l-4 border-gray-600"
          >
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <div class="flex items-center space-x-2 mb-2">
                  <span class="text-sm text-gray-400">{{ formatDate(news.date) }}</span>
                </div>
                <h3 class="text-lg font-bold mb-2">{{ news.title }}</h3>
                <p class="text-gray-300">{{ news.content }}</p>
              </div>
            </div>
          </div>
          
          <div v-if="generalNews.length === 0" class="text-center py-8">
            <p class="text-gray-400">お知らせはありません</p>
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
  name: 'NewsModal',
  emits: ['close'],
  setup() {
    const activeTab = ref('important')
    const importantNews = ref([])
    const generalNews = ref([])
    
    const fetchNews = async () => {
      try {
        const response = await axios.get('/api/news')
        importantNews.value = response.data.important || []
        generalNews.value = response.data.general || []
      } catch (error) {
        console.error('Failed to fetch news:', error)
        // フォールバックデータ
        importantNews.value = [
          {
            id: 1,
            title: 'EvoX事前登録開始のお知らせ',
            content: 'EvoXの事前登録が開始されました。事前登録者限定の特典をご用意しております。',
            date: new Date(),
            type: 'important'
          }
        ]
        generalNews.value = [
          {
            id: 2,
            title: 'サイトメンテナンスのお知らせ',
            content: 'システムメンテナンスのため、一時的にサービスを停止いたします。',
            date: new Date(Date.now() - 24 * 60 * 60 * 1000),
            type: 'general'
          }
        ]
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
      fetchNews()
    })
    
    return {
      activeTab,
      importantNews,
      generalNews,
      formatDate
    }
  }
}
</script>
