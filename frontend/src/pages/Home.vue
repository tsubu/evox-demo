<template>
  <div class="min-h-screen">
    <!-- Hero Section -->
    <section class="relative h-96 sm:h-screen flex items-center justify-center overflow-hidden bg-black">
      <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-0">
        <img src="/images/hero.png" alt="EvoX Hero" class="max-w-full max-h-full w-auto h-auto object-contain" />
      </div>
      
      <!-- 現在の日時（中央右） -->
      <div class="absolute top-1/2 right-8 transform -translate-y-1/2 z-20">
        <div class="bg-evox-blue px-4 py-1">
          <div class="text-black text-xs font-medium font-sevenbar">
            {{ currentDate }}
          </div>
        </div>
      </div>
    </section>

    <!-- メインコンテンツ -->
    <section class="py-16 bg-black">
      <div class="max-w-4xl mx-auto px-6 text-center">
        <!-- カウントダウン -->
        <Countdown :target-date="releaseDate" />
        
        <!-- キャッチ画像 -->
        <div class="mt-8 mb-6">
          <img src="/images/chatch_pre.png" alt="リリース開始日" class="w-full h-auto max-w-md mx-auto object-contain" />
        </div>
        
        <!-- 事前登録ボタン -->
        <div class="mt-8 space-y-4">
          <router-link to="/entry" class="inline-block link-hover">
            <img src="/images/btn_reg01.png" alt="事前登録する" class="w-64 md:w-80 h-auto transition-all duration-300 hover:animate-pulse" />
          </router-link>
          
          <!-- ログインボタン -->
          <div class="mt-4">
            <router-link to="/login" class="inline-block">
              <button class="bg-evox-blue text-black px-8 py-3 rounded-lg font-semibold hover:bg-blue-400 transition-colors">
                ログイン
              </button>
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- 登録手順 -->
    <section class="py-16 bg-black">
      <div class="w-full">
        <img src="/images/step1-3.png" alt="登録手順" class="w-full h-auto object-cover" />
      </div>
    </section>

    <!-- 登録者数 -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold mb-8">現在の登録者数</h2>
        <div class="text-6xl mb-4 font-sevenbar" style="color: #00AFB6;">
          {{ registrationCount.toLocaleString() }}
        </div>
      </div>
    </section>

    <!-- アイテムショップ -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-center">
          <img src="/images/itemshop.png" alt="アイテムショップ" class="max-w-4xl w-full" />
        </div>
      </div>
    </section>

    <!-- ストーリー -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-center">
          <img src="/images/story.png" alt="ストーリー" class="max-w-4xl w-full" />
        </div>
      </div>
    </section>

    <!-- 最新情報 -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-6">
        <h2 class="text-3xl font-bold text-center mb-12">最新情報</h2>
        <NewsTicker />
      </div>
    </section>

    <!-- 世界観 -->
    <section class="py-16 bg-black">
      <div class="max-w-7xl mx-auto px-6">
        <div class="flex justify-center mb-8">
          <img src="/images/worldimage.png" alt="世界観" class="max-w-4xl w-full" />
        </div>
        <div class="max-w-4xl mx-auto text-center">
          <p class="text-lg text-gray-300 leading-relaxed">
            いま、私たちの社会はかつてないほどコミュニケーションの希薄化に直面しています。<br>
            誰かに相談することも、ちょっとした感情を素直に表現することも、容易ではない時代。<br>
            だからこそ大切なのは、自分の"心の声"に気づき、寄り添い、育てていく力です。<br>
            EvoXは、そんな時代に必要とされる「感情共育型プラットフォーム」。<br>
            感情や行動を日々記録しながら、心の状態を"可視化"し、自分のペースで育てていくことができます。<br>
            朝、目覚めた瞬間の気分。日中にふと感じたこと。眠る前の静かな時間。<br>
            日々の"感情の波"をワンタップで記録するだけで、その積み重ねがアバターの成長として反映されていきます。<br>
            さらに、そのデータは「自己信頼スコア」として蓄積され、自分自身のメンタル状態を可視化・理解する指標にもなります。<br>
            「自分の心を、自分で守り、自分で育てる」そんな力を、ゲーム感覚で自然に身につけていく。<br>
            それは、ただのエンタメではありません。<br>
            メンタルケアでもあり、新しい感情教育でもあり、この時代を生き抜くための"自分との信頼関係を築く第一歩となるのです。
          </p>
        </div>
      </div>
    </section>



             <!-- EvoXの解説 -->
         <section class="py-16 bg-black">
           <div class="max-w-4xl mx-auto px-6 text-center">
             <div class="mb-8">
               <router-link to="/about" class="inline-block border border-white px-6 py-4 hover:bg-white hover:text-evox-black transition-all duration-300">
                 <h2 class="text-3xl font-bold mb-0 flex items-center justify-between">
                   EvoXとは
                   <span class="text-2xl ml-4">＞</span>
                 </h2>
               </router-link>
             </div>
             <div>
               <img src="/images/worldimage2.png" alt="EvoX" class="w-full h-auto max-w-2xl mx-auto rounded-lg object-contain" />
             </div>
           </div>
         </section>


       </div>
     </template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Countdown from '../components/Countdown.vue'
import NewsTicker from '../components/NewsTicker.vue'
import axios from 'axios'

export default {
  name: 'Home',
  components: {
    Countdown,
    NewsTicker
  },
  setup() {
    const router = useRouter()
    const registrationCount = ref(0)
    
    // リリース日
    const releaseDate = new Date('2026-07-10T00:00:00+09:00')
    
    // 現在の日時を動的に更新
    const currentDate = ref('')
    
    const updateCurrentDateTime = () => {
      try {
        const now = new Date()
        const year = now.getFullYear()
        const month = (now.getMonth() + 1).toString().padStart(2, '0')
        const day = now.getDate().toString().padStart(2, '0')
        const hours = now.getHours().toString().padStart(2, '0')
        const minutes = now.getMinutes().toString().padStart(2, '0')
        const seconds = now.getSeconds().toString().padStart(2, '0')
        
        currentDate.value = `${year}/${month}/${day} ${hours}:${minutes}:${seconds}`
      } catch (error) {
        console.error('Error updating current date time:', error)
        currentDate.value = '--/--/-- --:--:--'
      }
    }
    
    const fetchRegistrationCount = async () => {
      try {
        const response = await axios.get('/api/registrations/count')
        registrationCount.value = response.data.count || 0
      } catch (error) {
        console.error('Failed to fetch registration count:', error)
        registrationCount.value = 1234 // フォールバック
      }
    }
    
    const showPreRegisterDialog = () => {
      // 事前登録ページに遷移
      window.location.href = '/entry'
    }
    
    // ログイン状態をチェックしてマイページにリダイレクト
    const checkLoginStatus = () => {
      const authToken = localStorage.getItem('auth_token')
      if (authToken) {
        console.log('User is logged in, redirecting to mypage...')
        router.push('/mypage')
      }
    }
    
    onMounted(() => {
      // ログイン状態をチェック
      checkLoginStatus()
      
      fetchRegistrationCount()
      // 20秒ごとに更新
      setInterval(fetchRegistrationCount, 20000)
      
      // 日時を初期化して1秒ごとに更新
      updateCurrentDateTime()
      setInterval(updateCurrentDateTime, 1000)
    })
    
    return {
      registrationCount,
      currentDate,
      releaseDate,
      showPreRegisterDialog
    }
  }
}
</script>
