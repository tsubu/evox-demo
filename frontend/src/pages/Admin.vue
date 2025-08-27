<template>
  <div class="min-h-screen bg-evox-black py-8">
    <div class="max-w-7xl mx-auto px-6">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">管理者ダッシュボード</h1>
        <p class="text-gray-400">EvoX管理システム</p>
      </div>
      
      <!-- 統計カード -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">総ユーザー数</div>
          <div class="text-2xl text-evox-gold">{{ stats.totalUsers }}</div>
        </div>
        
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">今日の登録者</div>
          <div class="text-2xl text-evox-gold">{{ stats.todayRegistrations }}</div>
        </div>
        
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">総ポイント</div>
          <div class="text-2xl text-evox-gold">{{ stats.totalPoints }}</div>
        </div>
        
        <div class="card text-center">
          <div class="text-3xl font-bold text-evox-blue mb-2">QR使用回数</div>
          <div class="text-2xl text-evox-gold">{{ stats.qrUsage }}</div>
        </div>
      </div>
      
      <!-- 管理メニュー -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- コンテンツ管理 -->
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">📢</div>
            <h3 class="text-xl font-bold mb-2">ニュース管理</h3>
            <p class="text-gray-400 mb-4">お知らせの作成・編集</p>
            <button @click="showNewsModal = true" class="btn-primary w-full">
              ニュース管理
            </button>
          </div>
        </div>
        
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">🎉</div>
            <h3 class="text-xl font-bold mb-2">イベント管理</h3>
            <p class="text-gray-400 mb-4">イベントの作成・編集</p>
            <button @click="showEventModal = true" class="btn-primary w-full">
              イベント管理
            </button>
          </div>
        </div>
        
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">🎁</div>
            <h3 class="text-xl font-bold mb-2">ギフト管理</h3>
            <p class="text-gray-400 mb-4">アイテムの作成・編集</p>
            <button @click="showGiftModal = true" class="btn-primary w-full">
              ギフト管理
            </button>
          </div>
        </div>
        
        <!-- システム管理 -->
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">📱</div>
            <h3 class="text-xl font-bold mb-2">QRコード管理</h3>
            <p class="text-gray-400 mb-4">QRコードの作成・管理</p>
            <button @click="showQrModal = true" class="btn-primary w-full">
              QRコード管理
            </button>
          </div>
        </div>
        
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">👥</div>
            <h3 class="text-xl font-bold mb-2">ユーザー管理</h3>
            <p class="text-gray-400 mb-4">ユーザー一覧と詳細管理</p>
            <button @click="showUserModal = true" class="btn-primary w-full">
              ユーザー一覧
            </button>
          </div>
        </div>
        
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">📊</div>
            <h3 class="text-xl font-bold mb-2">統計レポート</h3>
            <p class="text-gray-400 mb-4">詳細な統計情報</p>
            <button @click="showReportModal = true" class="btn-primary w-full">
              レポート表示
            </button>
          </div>
        </div>
        
        <!-- システム設定 -->
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">⚙️</div>
            <h3 class="text-xl font-bold mb-2">システム設定</h3>
            <p class="text-gray-400 mb-4">システム設定の変更</p>
            <button @click="showSystemModal = true" class="btn-primary w-full">
              設定変更
            </button>
          </div>
        </div>
        
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">🔒</div>
            <h3 class="text-xl font-bold mb-2">セキュリティ</h3>
            <p class="text-gray-400 mb-4">セキュリティ設定</p>
            <button @click="showSecurityModal = true" class="btn-primary w-full">
              セキュリティ設定
            </button>
          </div>
        </div>
        
        <div class="card hover:bg-evox-gray transition-colors">
          <div class="text-center">
            <div class="text-4xl mb-4">📝</div>
            <h3 class="text-xl font-bold mb-2">お問い合わせ</h3>
            <p class="text-gray-400 mb-4">お問い合わせ管理</p>
            <button @click="showContactModal = true" class="btn-primary w-full">
              お問い合わせ管理
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- モーダル -->
    <div v-if="showNewsModal" class="modal-overlay" @click="showNewsModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">ニュース管理</h3>
        <p class="text-gray-400 mb-4">ニュースの作成・編集機能</p>
        <button @click="showNewsModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showEventModal" class="modal-overlay" @click="showEventModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">イベント管理</h3>
        <p class="text-gray-400 mb-4">イベントの作成・編集機能</p>
        <button @click="showEventModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showGiftModal" class="modal-overlay" @click="showGiftModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">ギフト管理</h3>
        <p class="text-gray-400 mb-4">アイテムの作成・編集機能</p>
        <button @click="showGiftModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showQrModal" class="modal-overlay" @click="showQrModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">QRコード管理</h3>
        <p class="text-gray-400 mb-4">QRコードの作成・管理機能</p>
        <button @click="showQrModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showUserModal" class="modal-overlay" @click="showUserModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">ユーザー管理</h3>
        <p class="text-gray-400 mb-4">ユーザー一覧・詳細管理機能</p>
        <button @click="showUserModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showReportModal" class="modal-overlay" @click="showReportModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">統計レポート</h3>
        <p class="text-gray-400 mb-4">詳細な統計情報表示機能</p>
        <button @click="showReportModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showSystemModal" class="modal-overlay" @click="showSystemModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">システム設定</h3>
        <p class="text-gray-400 mb-4">システム設定変更機能</p>
        <button @click="showSystemModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showSecurityModal" class="modal-overlay" @click="showSecurityModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">セキュリティ設定</h3>
        <p class="text-gray-400 mb-4">セキュリティ設定機能</p>
        <button @click="showSecurityModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
    
    <div v-if="showContactModal" class="modal-overlay" @click="showContactModal = false">
      <div class="modal-content" @click.stop>
        <h3 class="text-xl font-bold mb-4">お問い合わせ管理</h3>
        <p class="text-gray-400 mb-4">お問い合わせ管理機能</p>
        <button @click="showContactModal = false" class="btn-secondary">閉じる</button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'

export default {
  name: 'Admin',
  setup() {
    const stats = ref({
      totalUsers: 0,
      todayRegistrations: 0,
      totalPoints: 0,
      qrUsage: 0
    })
    
    // モーダル状態
    const showNewsModal = ref(false)
    const showEventModal = ref(false)
    const showGiftModal = ref(false)
    const showQrModal = ref(false)
    const showUserModal = ref(false)
    const showReportModal = ref(false)
    const showSystemModal = ref(false)
    const showSecurityModal = ref(false)
    const showContactModal = ref(false)
    
    const fetchStats = async () => {
      try {
        const response = await axios.get('/api/admin/stats')
        stats.value = response.data
      } catch (error) {
        console.error('Failed to fetch admin stats:', error)
        // フォールバックデータ
        stats.value = {
          totalUsers: 1234,
          todayRegistrations: 56,
          totalPoints: 78900,
          qrUsage: 234
        }
      }
    }
    
    onMounted(() => {
      fetchStats()
    })
    
    return {
      stats,
      showNewsModal,
      showEventModal,
      showGiftModal,
      showQrModal,
      showUserModal,
      showReportModal,
      showSystemModal,
      showSecurityModal,
      showContactModal
    }
  }
}
</script>

<style scoped>
.card {
  @apply bg-evox-gray rounded-lg p-6 border border-gray-700;
}

.modal-overlay {
  @apply fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50;
}

.modal-content {
  @apply bg-evox-gray rounded-lg p-6 max-w-md w-full mx-4 border border-gray-700;
}
</style>
