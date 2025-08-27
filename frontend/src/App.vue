<template>
  <div id="app" class="min-h-screen bg-evox-black">
    <!-- 認証維持コンポーネント（見た目は表示されません） -->
    <AuthMaintainer ref="authMaintainer" v-if="!isPenlightPage" />
    
    <!-- ペンライトモードページではヘッダーとフッターを非表示 -->
    <AppHeader v-if="!isPenlightModePage" />
    <main>
      <router-view />
    </main>
    <AppFooter v-if="!isPenlightModePage" />
    
    <!-- 通知トースト -->
    <NotificationToast />
    
    <!-- ペンライト制御（ペンライト関連ページでのみ表示） -->
    <PenlightControl v-if="isPenlightPage" :room-id="currentRoomId" />
  </div>
</template>

<script>
import { ref, provide, computed } from 'vue'
import { useRoute } from 'vue-router'
import AppHeader from './components/AppHeader.vue'
import AppFooter from './components/AppFooter.vue'
import NotificationToast from './components/NotificationToast.vue'
import PenlightControl from './components/PenlightControl.vue'
import AuthMaintainer from './components/AuthMaintainer.vue'

export default {
  name: 'App',
  components: {
    AppHeader,
    AppFooter,
    NotificationToast,
    PenlightControl,
    AuthMaintainer
  },
  setup() {
    const route = useRoute()
    
    // ペンライト関連のページかどうかを判定
    const isPenlightPage = computed(() => {
      const penlightRoutes = ['PenlightMode', 'PenlightNew', 'PenlightApp', 'PenlightSync']
      return penlightRoutes.includes(route.name)
    })
    
    // ペンライトモードページかどうかを判定（ヘッダー・フッター非表示を無効化）
    const isPenlightModePage = computed(() => {
      // ヘッダー・フッターを常に表示するため、falseを返す
      return false
    })
    
    // 現在のルームIDを計算（例：アーティスト名やイベント名から）
    const currentRoomId = computed(() => {
      // デフォルトルームIDを返す
      return 'default'
    })
    
    return {
      currentRoomId,
      isPenlightPage,
      isPenlightModePage
    }
  }
}
</script>
