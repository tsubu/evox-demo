<template>
  <div class="bg-evox-gray rounded-lg p-4">
    <div v-if="newsItems.length > 0" class="space-y-3">
      <div 
        v-for="(news, index) in displayNews" 
        :key="news.id" 
        class="flex items-center space-x-4 cursor-pointer hover:bg-evox-dark p-2 rounded transition-colors"
        @click="openNewsModal(news)"
      >
        <span class="text-evox-blue font-bold">NEWS</span>
        <span class="text-gray-400">{{ formatDate(news.gamenews_created_at) }}</span>
        <span class="text-white">{{ news.gamenews_title }}</span>
      </div>
    </div>
    <div v-else class="flex items-center space-x-4">
      <span class="text-evox-blue font-bold">NEWS</span>
      <span class="text-gray-400">{{ formatDate(new Date()) }}</span>
      <span class="text-white">EvoXの事前登録が開始されました！</span>
    </div>

    <!-- ニュース詳細モーダル -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click="handleModalBackdropClick">
      <div class="bg-evox-gray rounded-lg p-6 max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-xl font-bold text-white">{{ selectedNews?.gamenews_title }}</h3>
          <button 
            @click="closeNewsModal" 
            class="text-gray-400 hover:text-white text-2xl font-bold"
          >
            ×
          </button>
        </div>
        
        <div class="mb-4">
          <span class="text-evox-blue font-bold">NEWS</span>
          <span class="text-gray-400 ml-4">{{ formatDate(selectedNews?.gamenews_created_at) }}</span>
        </div>
        
        <div v-if="selectedNews?.gamenews_image_url" class="mb-4">
          <img :src="selectedNews.gamenews_image_url" :alt="selectedNews.gamenews_title" class="w-full h-auto rounded-lg">
        </div>
        
        <div class="text-white whitespace-pre-wrap leading-relaxed">
          {{ selectedNews?.gamenews_content }}
        </div>
        
        <div class="mt-6 flex justify-end">
          <button 
            @click="closeNewsModal" 
            class="bg-evox-blue text-black px-6 py-2 rounded-lg font-semibold hover:bg-blue-400 transition-colors"
          >
            閉じる
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

export default {
  name: 'NewsTicker',
  setup() {
    const newsItems = ref([])
    const showModal = ref(false)
    const selectedNews = ref(null)
    
    const fetchLatestNews = async () => {
      try {
        console.log('Fetching latest news...')
        const response = await axios.get('/api/news/latest')
        console.log('News API response:', response.data)
        if (response.data.success && response.data.data) {
          newsItems.value = response.data.data
          console.log('News items set:', newsItems.value)
        }
      } catch (error) {
        console.error('Failed to fetch latest news:', error)
        newsItems.value = []
      }
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('ja-JP', {
        month: '2-digit',
        day: '2-digit'
      })
    }

    // 表示用のニュースを最大5件に制限
    const displayNews = computed(() => {
      return newsItems.value.slice(0, 5)
    })

    // モーダルを開く
    const openNewsModal = (news) => {
      selectedNews.value = news
      showModal.value = true
    }

    // モーダルを閉じる
    const closeNewsModal = () => {
      showModal.value = false
      selectedNews.value = null
    }

    // モーダルの外側をクリックして閉じる
    const handleModalBackdropClick = (event) => {
      if (event.target === event.currentTarget) {
        closeNewsModal()
      }
    }
    
    onMounted(() => {
      fetchLatestNews()
      // 30秒ごとにニュースを更新
      setInterval(fetchLatestNews, 30000)
    })
    
    return {
      newsItems,
      displayNews,
      showModal,
      selectedNews,
      openNewsModal,
      closeNewsModal,
      formatDate
    }
  }
}
</script>
