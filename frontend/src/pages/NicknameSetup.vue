<template>
  <div class="min-h-screen bg-evox-dark text-white">
    <div class="container mx-auto px-4 py-8">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">ニックネーム設定</h1>
        <p class="text-gray-400">あなたのニックネームを設定してください</p>
      </div>

      <!-- ニックネーム設定フォーム -->
      <div class="max-w-md mx-auto">
        <div class="bg-evox-gray rounded-lg p-8">
          <form @submit.prevent="submitNickname" class="space-y-6" novalidate>
            <div>
              <label class="block text-sm font-medium mb-2">ニックネーム</label>
              <input 
                v-model="nickname"
                type="text"
                class="input-field w-full"
                placeholder="ニックネームを入力"
                required
                :disabled="loading"
                @input="validateNicknameLength"
              />
              <p class="text-xs text-gray-400 mt-1">
                {{ getCharacterCount(nickname) }}/16文字（全角8文字相当）
              </p>
            </div>
            
            <!-- エラーメッセージ表示エリア -->
            <div v-if="errorMessage" class="bg-red-600 text-white px-4 py-1.5 text-sm text-center">
              {{ errorMessage }}
            </div>
            
            <button 
              type="submit" 
              class="btn-primary w-full"
              :disabled="loading"
              @click="submitNickname"
            >
              <span v-if="loading">設定中...</span>
              <span v-else>ニックネームを設定</span>
            </button>
          </form>
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
const nickname = ref('')
const errorMessage = ref('')

// 全角文字を2文字としてカウントする関数
const getCharacterCount = (text) => {
  let count = 0
  for (let i = 0; i < text.length; i++) {
    const char = text.charAt(i)
    // 全角文字（ひらがな、カタカナ、漢字、全角英数字、全角記号）を2文字としてカウント
    if (char.match(/[^\x00-\x7F]/)) {
      count += 2
    } else {
      count += 1
    }
  }
  return count
}

// 入力時の文字数制限チェック
const validateNicknameLength = () => {
  const charCount = getCharacterCount(nickname.value)
  if (charCount > 16) {
    // 16文字を超えた場合は、超えた部分を削除
    let truncated = ''
    let count = 0
    for (let i = 0; i < nickname.value.length; i++) {
      const char = nickname.value.charAt(i)
      const charSize = char.match(/[^\x00-\x7F]/) ? 2 : 1
      if (count + charSize <= 16) {
        truncated += char
        count += charSize
      } else {
        break
      }
    }
    nickname.value = truncated
  }
}

const submitNickname = async (event) => {
  // フォームのデフォルト動作を完全に防ぐ
  event.preventDefault()
  event.stopPropagation()
  
  // エラーメッセージをクリア
  errorMessage.value = ''
  
  // ニックネームが入力されているかチェック
  if (!nickname.value.trim()) {
    errorMessage.value = 'ニックネームを入力してください。'
    return
  }
  
  // ニックネームの長さをチェック（全角文字を2文字としてカウント）
  const charCount = getCharacterCount(nickname.value.trim())
  if (charCount < 2) {
    errorMessage.value = 'ニックネームは2文字以上で入力してください。'
    return
  }
  
  if (charCount > 16) {
    errorMessage.value = 'ニックネームは全角8文字（半角16文字）以内で入力してください。'
    return
  }

  try {
    loading.value = true
    appStore.setLoading(true)

    const response = await api.profile.setNickname({
      nickname: nickname.value.trim()
    })

    appStore.showSuccess(response.data.message)
    
    // プロフィール完了状況を確認
    const profileResponse = await api.profile.check()
    const profileStatus = profileResponse.data.data

    if (profileStatus.is_complete) {
      // プロフィール完了の場合はマイページへ
      router.push('/mypage')
    } else {
      // マイページへ
      router.push('/mypage')
    }

  } catch (error) {
    console.error('ニックネーム設定エラー:', error)
    errorMessage.value = 'ニックネーム設定に失敗しました。'
  } finally {
    loading.value = false
    appStore.setLoading(false)
  }
}

onMounted(async () => {
  // プロフィール完了状況を確認
  try {
    const response = await api.profile.check()
    const profileStatus = response.data.data

    if (profileStatus.is_complete) {
      // 既に完了している場合はマイページへ
      router.push('/mypage')
    } else if (!profileStatus.has_avatar) {
      // キャラクター選択が未完了の場合はキャラクター選択へ
      router.push('/character-selection')
    }
  } catch (error) {
    console.error('プロフィール確認エラー:', error)
  }
})
</script>
