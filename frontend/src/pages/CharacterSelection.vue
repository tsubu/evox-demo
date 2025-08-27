<template>
  <div class="min-h-screen bg-evox-dark text-white">
    <div class="container mx-auto px-4 py-8">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">キャラクター選択</h1>
        <p class="text-gray-400">あなたのキャラクターを選択してください</p>
      </div>

      <!-- キャラクター選択 -->
      <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div
            v-for="character in characters"
            :key="character.id"
            @click="selectCharacter(character.id)"
            class="character-card cursor-pointer transition-all duration-300 hover:scale-105"
            :class="{ 'selected': selectedCharacter === character.id }"
          >
            <div class="bg-evox-gray rounded-lg p-6 text-center">
              <div class="w-40 h-40 mx-auto mb-4 flex items-center justify-center">
                <img 
                  :src="character.image" 
                  :alt="character.name"
                  class="w-full h-full object-contain"
                />
              </div>
              <h3 class="text-lg font-semibold mb-2">{{ character.name }}</h3>
              <p class="text-sm text-gray-400">{{ character.description }}</p>
            </div>
          </div>
        </div>

        <!-- エラーメッセージ表示エリア -->
        <div v-if="errorMessage" class="text-center mt-4">
          <div class="bg-red-600 text-white px-4 py-1.5 text-sm inline-block">
            {{ errorMessage }}
          </div>
        </div>

        <!-- 選択ボタン -->
        <div class="text-center mt-8">
          <button
            @click="submitSelection"
            :disabled="!selectedCharacter || loading"
            class="btn-primary px-8 py-3 text-lg disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="loading">選択中...</span>
            <span v-else>このキャラクターを選択</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { api } from '@/services/api'

const router = useRouter()
const appStore = useAppStore()

const loading = ref(false)
const selectedCharacter = ref('')
const errorMessage = ref('')

const characters = ref([])

const selectCharacter = (characterId) => {
  selectedCharacter.value = characterId
}

const submitSelection = async () => {
  // エラーメッセージをクリア
  errorMessage.value = ''
  
  // キャラクターが選択されているかチェック
  if (!selectedCharacter.value) {
    errorMessage.value = 'キャラクターを選択してください。'
    return
  }

  try {
    loading.value = true
    appStore.setLoading(true)

    const response = await api.profile.setAvatar({
      avatar_choice: selectedCharacter.value
    })

    appStore.showSuccess(response.data.message)
    
    // プロフィール完了状況を確認
    const profileResponse = await api.profile.check()
    const profileStatus = profileResponse.data.data

    if (profileStatus.is_complete) {
      // プロフィール完了の場合はマイページへ
      router.push('/mypage')
    } else if (profileStatus.missing_steps.includes('nickname')) {
      // ニックネーム設定が必要な場合
      router.push('/nickname-setup')
    } else {
      // マイページへ
      router.push('/mypage')
    }

  } catch (error) {
    console.error('キャラクター選択エラー:', error)
    errorMessage.value = 'キャラクター選択に失敗しました。'
  } finally {
    loading.value = false
    appStore.setLoading(false)
  }
}

onMounted(async () => {
  // アバター情報を取得
  try {
    const avatarsResponse = await api.avatars.list()
    characters.value = avatarsResponse.data.avatars
  } catch (error) {
    console.error('アバター情報取得エラー:', error)
    // フォールバック用のデフォルトアバター
    characters.value = [
      {
        id: 'car001',
        name: 'BABY ARASHI 01',
        image: '/images/car001.png',
        description: 'T-BOLANコラボ特別アバター'
      },
      {
        id: 'car002',
        name: 'BABY ARASHI 02',
        image: '/images/car002.png',
        description: 'T-BOLANコラボ特別アバター'
      },
      {
        id: 'car003',
        name: 'BABY ARASHI 03',
        image: '/images/car003.png',
        description: 'T-BOLANコラボ特別アバター'
      },
      {
        id: 'car004',
        name: 'BABY ARASHI 04',
        image: '/images/car004.png',
        description: 'T-BOLANコラボ特別アバター'
      }
    ]
  }

  // プロフィール完了状況を確認
  try {
    const response = await api.profile.check()
    const profileStatus = response.data.data

    if (profileStatus.is_complete) {
      // 既に完了している場合はマイページへ
      router.push('/mypage')
    } else if (profileStatus.has_avatar) {
      // キャラクター選択済みの場合はニックネーム設定へ
      router.push('/nickname-setup')
    }
  } catch (error) {
    console.error('プロフィール確認エラー:', error)
  }
})
</script>

<style scoped>
.character-card {
  border: 3px solid transparent;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.character-card.selected {
  border-color: #fbbf24;
  background-color: rgba(251, 191, 36, 0.15);
  transform: scale(1.05);
  box-shadow: 0 0 20px rgba(251, 191, 36, 0.3);
}

.character-card:hover {
  border-color: #fbbf24;
  transform: scale(1.02);
}

.character-card img {
  transition: all 0.3s ease;
}

.character-card.selected img {
  transform: scale(1.1);
}
</style>
