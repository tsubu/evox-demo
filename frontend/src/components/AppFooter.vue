<template>
  <footer class="relative">
    <div class="absolute inset-0 flex items-end justify-center">
      <img src="/images/footerimage.png" alt="Footer Background" class="w-full h-auto object-contain" />
    </div>
    <div class="relative z-10 max-w-7xl mx-auto px-6 py-8">
      <!-- お問い合わせセクション -->
      <div class="text-center mb-12">
        <p class="text-lg text-gray-300 mb-8 max-w-3xl mx-auto">
          弊社へのお問い合わせ・取材依頼などは下記のお問い合わせフォームからお願いします。<br>
          （XEVOX@ac.jp）にてご連絡ください。
        </p>
        <router-link to="/contact" class="inline-block border border-white px-6 py-4 hover:bg-white hover:text-evox-black transition-all duration-300 mb-12">
          <h2 class="text-3xl font-bold mb-0 flex items-center justify-between">
            お問い合わせ
            <span class="text-2xl ml-4">＞</span>
          </h2>
        </router-link>
      </div>
      
      <!-- ロゴ画像 -->
      <div class="text-center mb-8">
        <img src="/images/footer_logo.png" alt="EvoX" class="h-auto w-auto mx-auto scale-50" />
      </div>
      
      <!-- SNSリンクブロック -->
      <div class="text-center mb-8">
        <div class="inline-flex space-x-4">
          <a href="#" class="hover:opacity-80 transition-opacity">
            <img src="/images/sns_li.png" alt="LINE" class="h-8 w-auto" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity">
            <img src="/images/sns_fb.png" alt="Facebook" class="h-8 w-auto" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity">
            <img src="/images/sns_ins.png" alt="Instagram" class="h-8 w-auto" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity">
            <img src="/images/sns_tk.png" alt="TikTok" class="h-8 w-auto" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity">
            <img src="/images/sns_x.png" alt="X" class="h-8 w-auto" />
          </a>
          <a href="#" class="hover:opacity-80 transition-opacity">
            <img src="/images/sns_yt.png" alt="YouTube" class="h-8 w-auto" />
          </a>
        </div>
      </div>
      
      <!-- リンクブロック -->
      <div class="text-center mb-8">
        <router-link to="/terms" class="text-white hover:text-gray-300 transition-all duration-300 transform hover:scale-110 link-slide text-sm">
          利用規約
        </router-link>
        <span class="text-white mx-4 text-sm">|</span>
        <router-link to="/privacy" class="text-white hover:text-gray-300 transition-all duration-300 transform hover:scale-110 link-slide text-sm">
          プライバシーポリシー
        </router-link>
      </div>
      
      <!-- 著作権 -->
      <div class="text-center">
        <p class="text-white">
          © {{ currentYear }} EvoX. All rights reserved.
        </p>
      </div>
    </div>
  </footer>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'AppFooter',
  setup() {
    const currentYear = ref(new Date().getFullYear())
    
    return {
      currentYear
    }
  }
}
</script>
