<template>
  <div class="min-h-screen bg-evox-black">
    <!-- メインコンテンツ -->
    <div class="max-w-4xl mx-auto px-6 py-12">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold mb-4">お問い合わせ</h1>
        <p class="text-gray-400 text-lg">
          EvoXに関するご質問やご意見がございましたら、<br>
          お気軽にお問い合わせください。
        </p>
      </div>

      <div class="grid md:grid-cols-2 gap-12">
        <!-- お問い合わせフォーム -->
        <div class="bg-evox-gray rounded-lg p-8">
          <h2 class="text-2xl font-bold mb-6">お問い合わせフォーム</h2>
          
          <form @submit.prevent="submitContact" class="space-y-6">
            <div>
              <label class="block text-sm font-medium mb-2">お名前 *</label>
              <input 
                v-model="form.name"
                type="text"
                class="input-field w-full"
                placeholder="お名前を入力"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">メールアドレス *</label>
              <input 
                v-model="form.email"
                type="email"
                class="input-field w-full"
                placeholder="example@email.com"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">お問い合わせ種別 *</label>
              <select 
                v-model="form.category"
                class="input-field w-full"
                required
              >
                <option value="">選択してください</option>
                <option value="general">一般のお問い合わせ</option>
                <option value="technical">技術的な問題</option>
                <option value="billing">課金・決済について</option>
                <option value="bug">バグ報告</option>
                <option value="feature">機能要望</option>
                <option value="other">その他</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">件名 *</label>
              <input 
                v-model="form.subject"
                type="text"
                class="input-field w-full"
                placeholder="件名を入力"
                required
              />
            </div>
            
            <div>
              <label class="block text-sm font-medium mb-2">お問い合わせ内容 *</label>
              <textarea 
                v-model="form.message"
                rows="6"
                class="input-field w-full resize-none"
                placeholder="お問い合わせ内容を詳しく入力してください"
                required
              ></textarea>
            </div>
            
            <div class="flex items-center">
              <input 
                v-model="form.agree"
                type="checkbox"
                id="agree"
                class="mr-2"
                required
              />
              <label for="agree" class="text-sm">
                <a href="/privacy" target="_blank" rel="noopener noreferrer" class="text-evox-blue hover:text-blue-400">
                  プライバシーポリシー
                </a>
                に同意する *
              </label>
            </div>
            
            <button type="submit" class="btn-primary w-full">
              送信する
            </button>
          </form>
        </div>

        <!-- お問い合わせ情報 -->
        <div class="space-y-8">
          <div class="bg-evox-gray rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-6">よくある質問</h2>
            
            <div class="space-y-4">
              <div class="border-b border-gray-700 pb-4">
                <h3 class="font-medium mb-2">Q. 事前登録はいつまでできますか？</h3>
                <p class="text-gray-400 text-sm">
                  リリース前日まで事前登録を受け付けています。
                </p>
              </div>
              
              <div class="border-b border-gray-700 pb-4">
                <h3 class="font-medium mb-2">Q. 事前登録特典はいつ受け取れますか？</h3>
                <p class="text-gray-400 text-sm">
                  ゲームリリース時に自動で付与されます。
                </p>
              </div>
              
              <div class="border-b border-gray-700 pb-4">
                <h3 class="font-medium mb-2">Q. パスワードを忘れてしまいました</h3>
                <p class="text-gray-400 text-sm">
                  <router-link to="/forgot-password" class="text-evox-blue hover:text-blue-400">
                    パスワードリセットページ
                  </router-link>
                  から再設定できます。
                </p>
              </div>
              
              <div class="border-b border-gray-700 pb-4">
                <h3 class="font-medium mb-2">Q. QRコードが読み取れません</h3>
                <p class="text-gray-400 text-sm">
                  カメラの権限を許可し、QRコードがはっきり見えるようにしてください。
                </p>
              </div>
            </div>
          </div>

          <div class="bg-evox-gray rounded-lg p-8">
            <h2 class="text-2xl font-bold mb-6">お問い合わせ先</h2>
            
            <div class="space-y-4">
              <div>
                <h3 class="font-medium mb-2">メールサポート</h3>
                <p class="text-gray-400 text-sm">
                  通常、１営業日以内にご返信いたします。お問い合わせが混み合った際は、遅延することがございます。あらかじめご了承ください。
                </p>
              </div>
              
              <div>
                <h3 class="font-medium mb-2">営業時間</h3>
                <p class="text-gray-400 text-sm">
                  平日 9:00〜18:00（土日祝日除く）
                </p>
              </div>
              
              <div>
                <h3 class="font-medium mb-2">緊急時</h3>
                <p class="text-gray-400 text-sm">
                  システム障害などの緊急時は、公式SNS、または、最新情報欄をご確認ください。
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { reactive } from 'vue'
import { useAppStore } from '@/stores/app'
import { api, handleApiError, handleApiSuccess } from '@/services/api'

export default {
  name: 'Contact',
  setup() {
    const appStore = useAppStore()
    
    const form = reactive({
      name: '',
      email: '',
      category: '',
      subject: '',
      message: '',
      agree: false
    })

    const submitContact = async () => {
      try {
        appStore.setLoading(true)
        
        // 実際のAPIエンドポイントに合わせて調整
        const response = await api.contact.submit({
          name: form.name,
          email: form.email,
          category: form.category,
          subject: form.subject,
          message: form.message
        })
        
        handleApiSuccess('お問い合わせを送信しました。ありがとうございます。')
        
        // フォームをリセット
        Object.assign(form, {
          name: '',
          email: '',
          category: '',
          subject: '',
          message: '',
          agree: false
        })
      } catch (error) {
        handleApiError(error)
      } finally {
        appStore.setLoading(false)
      }
    }

    return {
      form,
      submitContact
    }
  }
}
</script>
