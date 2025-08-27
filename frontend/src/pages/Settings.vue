<template>
  <div class="min-h-screen bg-evox-black py-8">
    <div class="max-w-2xl mx-auto px-6">
      <!-- ヘッダー -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold mb-4">設定</h1>
        <p class="text-gray-400">アカウント情報の変更</p>
      </div>

      <!-- 設定メニュー -->
      <div class="space-y-6">
        <!-- ニックネーム変更 -->
        <div class="card">
          <h3 class="text-xl font-bold mb-4">ニックネーム変更</h3>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-2">現在のニックネーム</label>
              <div class="text-lg">{{ currentUser.nickname || '' }}</div>
            </div>
            <div>
              <label for="newNickname" class="block text-sm font-medium mb-2">新しいニックネーム</label>
              <input
                id="newNickname"
                v-model="newNickname"
                type="text"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue"
                placeholder="新しいニックネームを入力"
                maxlength="20"
              />
            </div>
            <button
              @click="updateNickname"
              :disabled="!newNickname || newNickname === currentUser.nickname || isUpdating"
              class="btn-primary w-full"
            >
              {{ isUpdating ? '更新中...' : 'ニックネームを更新' }}
            </button>
          </div>
        </div>

        <!-- パスワード変更 -->
        <div class="card">
          <h3 class="text-xl font-bold mb-4">パスワード変更</h3>
          <div class="space-y-4">
            <div>
              <label for="currentPassword" class="block text-sm font-medium mb-2">現在のパスワード</label>
              <input
                id="currentPassword"
                v-model="passwordForm.currentPassword"
                type="password"
                autocomplete="off"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue"
                placeholder="現在のパスワードを入力"
              />
            </div>
            <div>
              <label for="newPassword" class="block text-sm font-medium mb-2">新しいパスワード</label>
              <input
                id="newPassword"
                v-model="passwordForm.newPassword"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue"
                placeholder="新しいパスワードを入力（8文字以上）"
              />
            </div>
            <div>
              <label for="confirmPassword" class="block text-sm font-medium mb-2">新しいパスワード（確認）</label>
              <input
                id="confirmPassword"
                v-model="passwordForm.confirmPassword"
                type="password"
                autocomplete="new-password"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue"
                placeholder="新しいパスワードを再入力"
              />
            </div>
            <button
              @click="updatePassword"
              :disabled="!isPasswordFormValid || isUpdating"
              class="btn-primary w-full"
            >
              {{ isUpdating ? '更新中...' : 'パスワードを更新' }}
            </button>
          </div>
        </div>

        <!-- 個人情報変更 -->
        <div class="card">
          <h3 class="text-xl font-bold mb-4">個人情報変更</h3>
          <div class="space-y-4">
            <div>
              <label for="name" class="block text-sm font-medium mb-2">お名前</label>
              <input
                id="name"
                v-model="profileForm.name"
                type="text"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue"
                placeholder="お名前を入力"
              />
            </div>
            <div>
              <label for="email" class="block text-sm font-medium mb-2">メールアドレス</label>
              <input
                id="email"
                v-model="profileForm.email"
                type="email"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue"
                placeholder="メールアドレスを入力"
              />
            </div>
            <div>
              <label class="block text-sm font-medium mb-2">電話番号</label>
              <div class="px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                {{ currentUser.phone || '' }}
                <span class="text-sm ml-2">（変更不可）</span>
              </div>
            </div>
            <div>
              <label for="birthday" class="block text-sm font-medium mb-2">誕生日</label>
              <input
                id="birthday"
                v-model="profileForm.birthday"
                type="date"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg focus:outline-none focus:border-evox-blue cursor-pointer"
                placeholder="誕生日を選択"
                max="2025-12-31"
                min="1900-01-01"
              />
            </div>
            <button
              @click="updateProfile"
              :disabled="!isProfileFormValid || isUpdating"
              class="btn-primary w-full"
            >
              {{ isUpdating ? '更新中...' : '個人情報を更新' }}
            </button>
          </div>
        </div>

        <!-- 戻るボタン -->
        <div class="text-center">
          <button
            @click="goBack"
            class="btn-secondary"
          >
            マイページに戻る
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

export default {
  name: 'Settings',
  setup() {
    const router = useRouter()
    const isUpdating = ref(false)
    
    const currentUser = reactive({
      nickname: '',
      phone: '',
      email: '',
      name: '',
      birthday: ''
    })

    const newNickname = ref('')
    
    const passwordForm = reactive({
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    })

    const profileForm = reactive({
      email: '',
      name: '',
      birthday: ''
    })

    // パスワードフォームのバリデーション
    const isPasswordFormValid = computed(() => {
      return passwordForm.currentPassword && 
             passwordForm.newPassword && 
             passwordForm.confirmPassword &&
             passwordForm.newPassword.length >= 8 &&
             passwordForm.newPassword === passwordForm.confirmPassword
    })

    // プロフィールフォームのバリデーション
    const isProfileFormValid = computed(() => {
      return profileForm.email && profileForm.name
    })

    // 現在のユーザー情報を取得
    const fetchCurrentUser = async () => {
      try {
        console.log('Fetching current user info...')
        const response = await api.mypage.profile()
        console.log('User info response:', response.data)
        Object.assign(currentUser, response.data.data)
        console.log('Current user after assignment:', currentUser)
        
        // フォームの初期値を設定
        newNickname.value = currentUser.nickname || ''
        profileForm.email = currentUser.email || ''
        profileForm.name = currentUser.name || ''
        profileForm.birthday = currentUser.birthday || ''
        console.log('Form values set:', { newNickname: newNickname.value, email: profileForm.email, name: profileForm.name, birthday: profileForm.birthday })
      } catch (error) {
        console.error('Failed to fetch user info:', error)
      }
    }

    // ニックネーム更新
    const updateNickname = async () => {
      if (!newNickname.value || newNickname.value === currentUser.nickname) return
      
      isUpdating.value = true
      try {
        await api.profile.setNickname({ nickname: newNickname.value })
        currentUser.nickname = newNickname.value
        alert('ニックネームを更新しました')
      } catch (error) {
        console.error('Failed to update nickname:', error)
        alert('ニックネームの更新に失敗しました')
      } finally {
        isUpdating.value = false
      }
    }

    // パスワード更新
    const updatePassword = async () => {
      if (!isPasswordFormValid.value) return
      
      isUpdating.value = true
      try {
        await api.profile.updatePassword({
          current_password: passwordForm.currentPassword,
          new_password: passwordForm.newPassword,
          confirm_password: passwordForm.confirmPassword
        })
        
        // フォームをリセット
        passwordForm.currentPassword = ''
        passwordForm.newPassword = ''
        passwordForm.confirmPassword = ''
        
        alert('パスワードを更新しました')
      } catch (error) {
        console.error('Failed to update password:', error)
        alert('パスワードの更新に失敗しました')
      } finally {
        isUpdating.value = false
      }
    }

    // プロフィール更新
    const updateProfile = async () => {
      if (!isProfileFormValid.value) return
      
      isUpdating.value = true
      try {
        await api.profile.updateProfile({
          email: profileForm.email,
          name: profileForm.name,
          birthday: profileForm.birthday
        })
        
        currentUser.email = profileForm.email
        currentUser.name = profileForm.name
        currentUser.birthday = profileForm.birthday
        
        alert('個人情報を更新しました')
      } catch (error) {
        console.error('Failed to update profile:', error)
        alert('個人情報の更新に失敗しました')
      } finally {
        isUpdating.value = false
      }
    }

    const goBack = () => {
      router.push('/mypage')
    }

    onMounted(() => {
      fetchCurrentUser()
      
      // パスワードフォームを明示的にリセット
      passwordForm.currentPassword = ''
      passwordForm.newPassword = ''
      passwordForm.confirmPassword = ''
    })

    return {
      currentUser,
      newNickname,
      passwordForm,
      profileForm,
      isUpdating,
      isPasswordFormValid,
      isProfileFormValid,
      updateNickname,
      updatePassword,
      updateProfile,
      goBack
    }
  }
}
</script>

<style scoped>
.card {
  @apply bg-gray-900 rounded-lg p-6 border border-gray-700;
}

.btn-primary {
  @apply bg-evox-blue text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed;
}

.btn-secondary {
  @apply bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-500 transition-colors;
}

/* 日付入力フィールドのカレンダー表示を確実にする */
input[type="date"] {
  position: relative;
}

input[type="date"]::-webkit-calendar-picker-indicator {
  background: transparent;
  bottom: 0;
  color: transparent;
  cursor: pointer;
  height: auto;
  left: 0;
  position: absolute;
  right: 0;
  top: 0;
  width: auto;
}

input[type="date"]::-webkit-datetime-edit {
  color: white;
}

input[type="date"]::-webkit-datetime-edit-fields-wrapper {
  color: white;
}

input[type="date"]::-webkit-datetime-edit-text {
  color: white;
}

input[type="date"]::-webkit-datetime-edit-month-field {
  color: white;
}

input[type="date"]::-webkit-datetime-edit-day-field {
  color: white;
}

input[type="date"]::-webkit-datetime-edit-year-field {
  color: white;
}
</style>
