<template>
  <div class="min-h-screen bg-evox-black py-8">
    <div class="max-w-4xl mx-auto px-6">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">マイページ</h1>
        <p class="text-gray-400">EvoXの世界へようこそ</p>
      </div>
      
      <!-- ユーザー情報カード -->
      <div class="card mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div class="text-center">
            <div class="text-2xl font-bold text-evox-blue mb-2">アバター</div>
            <div class="flex justify-center">
              <img 
                v-if="userInfo.avatar_choice" 
                :src="`/images/${userInfo.avatar_choice}.png`" 
                :alt="userInfo.avatar_choice"
                class="w-16 h-16 rounded-full object-cover"
                @error="handleAvatarError"
              />
              <div v-else class="w-16 h-16 rounded-full bg-gray-600 flex items-center justify-center text-white">
                ?
              </div>
            </div>
            <div v-if="userInfo.avatar_choice" class="text-sm text-gray-400 mt-2">
              {{ getAvatarName(userInfo.avatar_choice) }}
            </div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-evox-blue mb-2">ユーザーID</div>
            <div class="text-lg">{{ userInfo.userId }}</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-evox-blue mb-2">ニックネーム</div>
            <div class="text-lg">{{ userInfo.nickname || '未設定' }}</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-evox-blue mb-2">保有ポイント</div>
            <div class="text-lg text-evox-gold">{{ userInfo.points }}pt</div>
          </div>
        </div>
      </div>
      
      <!-- クイックアクション -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <button 
          @click="navigateToQR"
          class="card hover:bg-evox-gray transition-colors text-center"
        >
          <div class="text-2xl mb-2">📱</div>
          <div class="font-semibold">QRコードを読む</div>
        </button>
        
        <button 
          @click="navigateToPenlight"
          class="card hover:bg-evox-gray transition-colors text-center"
        >
          <div class="text-2xl mb-2">🎆</div>
          <div class="font-semibold">ペンライト</div>
        </button>
        
        <button 
          @click="showEvents"
          class="card hover:bg-evox-gray transition-colors text-center"
        >
          <div class="text-2xl mb-2">🎉</div>
          <div class="font-semibold">イベント情報</div>
        </button>
        
        <button 
          @click="showNews"
          class="card hover:bg-evox-gray transition-colors text-center"
        >
          <div class="text-2xl mb-2">📢</div>
          <div class="font-semibold">お知らせ</div>
        </button>
        
        <button 
          @click="showGift"
          class="card hover:bg-evox-gray transition-colors text-center"
        >
          <div class="text-2xl mb-2">🎁</div>
          <div class="font-semibold">GIFTへの交換</div>
        </button>
        
        <button 
          @click="navigateToSettings"
          class="card hover:bg-evox-gray transition-colors text-center"
        >
          <div class="text-2xl mb-2">⚙️</div>
          <div class="font-semibold">設定</div>
        </button>
      </div>
      
      <!-- ポイント詳細 -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">利用可能ポイント</div>
          <div class="text-2xl text-evox-gold">{{ userInfo.availablePoints }}pt</div>
        </div>
        
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">ミッションポイント</div>
          <div class="text-2xl text-evox-gold">{{ userInfo.missionPoints }}pt</div>
        </div>
        
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">通算獲得ポイント</div>
          <div class="text-2xl text-evox-gold">{{ userInfo.totalPoints }}pt</div>
        </div>
      </div>
      
      <!-- 最近の活動 -->
      <div class="card">
        <h3 class="text-xl font-bold mb-4">最近の活動</h3>
        <div class="space-y-3">
          <div 
            v-for="activity in recentActivities" 
            :key="activity.id"
            class="flex justify-between items-center py-2 border-b border-gray-700 last:border-b-0"
          >
            <div>
              <div class="font-medium">{{ activity.title }}</div>
              <div class="text-sm text-gray-400">{{ formatDate(activity.date) }}</div>
            </div>
            <div class="text-evox-gold font-semibold">
              {{ activity.points > 0 ? '+' : '' }}{{ activity.points }}pt
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- モーダル -->
    <EventsModal v-if="showEventsModal" @close="showEventsModal = false" />
    <NewsModal v-if="showNewsModal" @close="showNewsModal = false" />
    <GiftModal v-if="showGiftModal" @close="showGiftModal = false" />
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { api } from '@/services/api'
import EventsModal from '../components/EventsModal.vue'
import NewsModal from '../components/NewsModal.vue'
import GiftModal from '../components/GiftModal.vue'

export default {
  name: 'MyPage',
  components: {
    EventsModal,
    NewsModal,
    GiftModal
  },
  setup() {
    const router = useRouter()
    const appStore = useAppStore()
    const showEventsModal = ref(false)
    const showNewsModal = ref(false)
    const showGiftModal = ref(false)
    
    const userInfo = reactive({
      userId: '',
      nickname: '',
      avatar_choice: '',
      points: 0,
      availablePoints: 0,
      missionPoints: 0,
      totalPoints: 0
    })
    
    const recentActivities = ref([])
    
    const checkProfileCompletion = async () => {
      try {
        const response = await api.profile.check()
        const profileStatus = response.data.data

        console.log('Profile status:', profileStatus)

        if (!profileStatus.is_complete) {
          // missing_stepsが存在し、配列であることを確認
          if (profileStatus.missing_steps && Array.isArray(profileStatus.missing_steps)) {
            if (profileStatus.missing_steps.includes('avatar')) {
              console.log('Redirecting to character selection')
              router.push('/character-selection')
              return false
            } else if (profileStatus.missing_steps.includes('nickname')) {
              console.log('Redirecting to nickname setup')
              router.push('/nickname-setup')
              return false
            }
          } else {
            console.log('Missing steps is not an array or undefined:', profileStatus.missing_steps)
            // missing_stepsが不正な場合は、デフォルトでキャラクター選択にリダイレクト
            router.push('/character-selection')
            return false
          }
        }
        return true
      } catch (error) {
        console.error('プロフィール確認エラー:', error)
        // エラーの場合もリダイレクトを避ける
        return false
      }
    }

    const fetchUserInfo = async () => {
      try {
        console.log('Fetching user info...')
        const response = await api.mypage.profile()
        console.log('User info response:', response.data)
        Object.assign(userInfo, response.data.data)
      } catch (error) {
        console.error('Failed to fetch user info:', error)
      }
    }
    
    const fetchPoints = async () => {
      try {
        const response = await api.mypage.points()
        console.log('Points response:', response.data)
        Object.assign(userInfo, response.data.data)
      } catch (error) {
        console.error('Failed to fetch points:', error)
      }
    }
    
    const fetchRecentActivities = async () => {
      try {
        const response = await api.mypage.activities()
        console.log('Activities response:', response.data)
        recentActivities.value = response.data.data.activities || []
      } catch (error) {
        console.error('Failed to fetch recent activities:', error)
        recentActivities.value = []
      }
    }
    
    const navigateToQR = () => {
      router.push('/qr-scanner')
    }
    
    const navigateToPenlight = () => {
      router.push('/penlight-app')
    }
    
    const navigateToSettings = () => {
      router.push('/settings')
    }
    
    const showEvents = () => {
      showEventsModal.value = true
    }
    
    const showNews = () => {
      showNewsModal.value = true
    }
    
    const showGift = () => {
      showGiftModal.value = true
    }
    
    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('ja-JP', {
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
      })
    }
    
    const handleAvatarError = (event) => {
      console.error('Avatar image failed to load:', event.target.src)
      // デフォルトアバターを表示
      event.target.style.display = 'none'
      event.target.nextElementSibling.style.display = 'flex'
    }

    const getAvatarName = (avatarId) => {
      // アバターIDから名前を取得するマッピング
      const avatarNames = {
        'car001': 'BABY ARASHI 01',
        'car002': 'BABY ARASHI 02',
        'car003': 'BABY ARASHI 03',
        'car004': 'BABY ARASHI 04'
      }
      return avatarNames[avatarId] || avatarId
    }
    
    onMounted(async () => {
      // プロフィール完了状況を確認
      const isProfileComplete = await checkProfileCompletion()
      if (isProfileComplete) {
        fetchUserInfo()
        fetchPoints()
        fetchRecentActivities()
      }
      // プロフィールが完了していない場合は、リダイレクト後に処理を停止
    })
    
    return {
      userInfo,
      recentActivities,
      showEventsModal,
      showNewsModal,
      showGiftModal,
      navigateToQR,
      navigateToPenlight,
      navigateToSettings,
      showEvents,
      showNews,
      showGift,
      formatDate,
      handleAvatarError,
      getAvatarName,
      checkProfileCompletion
    }
  }
}
</script>
