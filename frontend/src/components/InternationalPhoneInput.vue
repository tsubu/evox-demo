<template>
  <div class="relative">
    <label v-if="label" class="block text-sm font-medium mb-2">{{ label }}</label>
    <div class="flex">
      <!-- 国選択ドロップダウン -->
      <div class="relative">
        <button
          type="button"
          @click="toggleCountryDropdown"
          class="flex items-center px-3 py-2 border border-gray-600 bg-gray-800 text-white rounded-l-md focus:outline-none focus:ring-evox-blue focus:border-evox-blue"
        >
          <span class="text-lg mr-2">{{ selectedCountry.flag }}</span>
          <span class="text-sm">{{ selectedCountry.code }}</span>
          <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>
        
        <!-- 国選択ドロップダウンメニュー -->
        <div
          v-if="showCountryDropdown"
          class="absolute z-50 mt-1 w-64 bg-gray-800 border border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto"
        >
          <div class="p-2">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="国を検索..."
              class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded text-white text-sm focus:outline-none focus:ring-evox-blue focus:border-evox-blue"
            />
          </div>
          <div class="py-1">
            <button
              v-for="country in filteredCountries"
              :key="country.code"
              @click="selectCountry(country)"
              class="w-full flex items-center px-4 py-2 text-sm text-white hover:bg-gray-700 focus:outline-none"
            >
              <span class="text-lg mr-3">{{ country.flag }}</span>
              <span class="flex-1 text-left">{{ country.name }}</span>
              <span class="text-gray-400">{{ country.code }}</span>
            </button>
          </div>
        </div>
      </div>
      
      <!-- 電話番号入力フィールド -->
      <input
        :value="phoneNumber"
        @input="updatePhoneNumber"
        @paste="handlePaste"
        @focus="handleFocus"
        @mousedown="handleMouseDown"
        type="tel"
        inputmode="numeric"
        pattern="[0-9]*"
        :required="required"
        :placeholder="placeholder"
        class="flex-1 px-3 py-2 border border-l-0 border-gray-600 placeholder-gray-400 text-white bg-gray-800 rounded-r-md focus:outline-none focus:ring-evox-blue focus:border-evox-blue"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: '電話番号を入力'
  },
  required: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue'])

const showCountryDropdown = ref(false)
const searchQuery = ref('')
const phoneNumber = ref('')

// 国リスト（主要な国のみ）
const countries = [
  { name: '日本', code: '+81', flag: '🇯🇵' },
  { name: 'United States', code: '+1', flag: '🇺🇸' },
  { name: '中国', code: '+86', flag: '🇨🇳' },
  { name: '한국', code: '+82', flag: '🇰🇷' },
  { name: 'United Kingdom', code: '+44', flag: '🇬🇧' },
  { name: 'Deutschland', code: '+49', flag: '🇩🇪' },
  { name: 'France', code: '+33', flag: '🇫🇷' },
  { name: 'Italia', code: '+39', flag: '🇮🇹' },
  { name: 'España', code: '+34', flag: '🇪🇸' },
  { name: 'Canada', code: '+1', flag: '🇨🇦' },
  { name: 'Australia', code: '+61', flag: '🇦🇺' },
  { name: 'Brasil', code: '+55', flag: '🇧🇷' },
  { name: 'भारत', code: '+91', flag: '🇮🇳' },
  { name: 'Россия', code: '+7', flag: '🇷🇺' },
  { name: 'México', code: '+52', flag: '🇲🇽' },
  { name: 'Indonesia', code: '+62', flag: '🇮🇩' },
  { name: 'ประเทศไทย', code: '+66', flag: '🇹🇭' },
  { name: 'Việt Nam', code: '+84', flag: '🇻🇳' },
  { name: 'Malaysia', code: '+60', flag: '🇲🇾' },
  { name: 'Pilipinas', code: '+63', flag: '🇵🇭' },
  { name: 'Singapore', code: '+65', flag: '🇸🇬' },
  { name: '香港', code: '+852', flag: '🇭🇰' },
  { name: '台灣', code: '+886', flag: '🇹🇼' },
  { name: 'New Zealand', code: '+64', flag: '🇳🇿' },
  { name: 'South Africa', code: '+27', flag: '🇿🇦' },
  { name: 'مصر', code: '+20', flag: '🇪🇬' },
  { name: 'Nigeria', code: '+234', flag: '🇳🇬' },
  { name: 'Kenya', code: '+254', flag: '🇰🇪' },
  { name: 'المغرب', code: '+212', flag: '🇲🇦' },
  { name: 'تونس', code: '+216', flag: '🇹🇳' },
  { name: 'الجزائر', code: '+213', flag: '🇩🇿' },
  { name: 'ليبيا', code: '+218', flag: '🇱🇾' },
  { name: 'السودان', code: '+249', flag: '🇸🇩' },
  { name: 'ኢትዮጵያ', code: '+251', flag: '🇪🇹' },
  { name: 'Uganda', code: '+256', flag: '🇺🇬' },
  { name: 'Tanzania', code: '+255', flag: '🇹🇿' },
  { name: 'Ghana', code: '+233', flag: '🇬🇭' },
  { name: 'Côte d\'Ivoire', code: '+225', flag: '🇨🇮' },
  { name: 'Sénégal', code: '+221', flag: '🇸🇳' },
  { name: 'Mali', code: '+223', flag: '🇲🇱' },
  { name: 'Burkina Faso', code: '+226', flag: '🇧🇫' },
  { name: 'Niger', code: '+227', flag: '🇳🇪' },
  { name: 'تشاد', code: '+235', flag: '🇹🇩' },
  { name: 'Cameroun', code: '+237', flag: '🇨🇲' },
  { name: 'République centrafricaine', code: '+236', flag: '🇨🇫' },
  { name: 'Gabon', code: '+241', flag: '🇬🇦' },
  { name: 'République du Congo', code: '+242', flag: '🇨🇬' },
  { name: 'République démocratique du Congo', code: '+243', flag: '🇨🇩' },
  { name: 'Angola', code: '+244', flag: '🇦🇴' },
  { name: 'Guiné-Bissau', code: '+245', flag: '🇬🇼' },
  { name: 'Guinée', code: '+224', flag: '🇬🇳' },
  { name: 'Sierra Leone', code: '+232', flag: '🇸🇱' },
  { name: 'Liberia', code: '+231', flag: '🇱🇷' },
  { name: 'Togo', code: '+228', flag: '🇹🇬' },
  { name: 'Bénin', code: '+229', flag: '🇧🇯' }
]

const selectedCountry = ref(countries.find(c => c.code === '+81') || countries[0])

const filteredCountries = computed(() => {
  if (!searchQuery.value) return countries
  const query = searchQuery.value.toLowerCase()
  return countries.filter(country => 
    country.name.toLowerCase().includes(query) ||
    country.code.includes(query)
  )
})

const toggleCountryDropdown = () => {
  showCountryDropdown.value = !showCountryDropdown.value
}

const selectCountry = (country) => {
  selectedCountry.value = country
  showCountryDropdown.value = false
  searchQuery.value = ''
  updateValue()
}

const updatePhoneNumber = (event) => {
  let value = event.target.value
  
  // 全角数字と漢数字を半角数字に変換
  value = convertFullWidthToHalfWidth(value)
  
  // 数字以外の文字を除去
  value = value.replace(/[^0-9]/g, '')
  
  phoneNumber.value = value
  updateValue()
}

// 全角数字と漢数字を半角数字に変換する関数
const convertFullWidthToHalfWidth = (str) => {
  // 全角数字の変換マッピング
  const fullWidthDigits = {
    '０': '0', '１': '1', '２': '2', '３': '3', '４': '4',
    '５': '5', '６': '6', '７': '7', '８': '8', '９': '9'
  }
  
  // 漢数字の変換マッピング
  const kanjiDigits = {
    '零': '0', '一': '1', '二': '2', '三': '3', '四': '4',
    '五': '5', '六': '6', '七': '7', '八': '8', '九': '9',
    '十': '10', '百': '100', '千': '1000', '万': '10000',
    '億': '100000000', '兆': '1000000000000'
  }
  
  let result = str
  
  // 全角数字を変換
  result = result.replace(/[０-９]/g, (match) => {
    return fullWidthDigits[match] || match
  })
  
  // 漢数字を変換（複雑な漢数字の処理）
  result = result.replace(/[零一二三四五六七八九十百千万億兆]+/g, (match) => {
    return convertKanjiToNumber(match)
  })
  
  return result
}

// 漢数字をアラビア数字に変換する関数
const convertKanjiToNumber = (kanjiStr) => {
  if (kanjiStr.length === 1) {
    const kanjiDigits = {
      '零': '0', '一': '1', '二': '2', '三': '3', '四': '4',
      '五': '5', '六': '6', '七': '7', '八': '8', '九': '9'
    }
    return kanjiDigits[kanjiStr] || kanjiStr
  }
  
  // 複雑な漢数字の処理（簡易版）
  let result = kanjiStr
  const simpleKanji = {
    '零': '0', '一': '1', '二': '2', '三': '3', '四': '4',
    '五': '5', '六': '6', '七': '7', '八': '8', '九': '9'
  }
  
  for (const [kanji, num] of Object.entries(simpleKanji)) {
    result = result.replace(new RegExp(kanji, 'g'), num)
  }
  
  return result
}

const handlePaste = (event) => {
  event.preventDefault()
  const pastedData = event.clipboardData.getData('text/plain')
  
  // 全角数字と漢数字を半角数字に変換
  let convertedValue = convertFullWidthToHalfWidth(pastedData)
  
  // 数字以外の文字を除去
  convertedValue = convertedValue.replace(/[^0-9]/g, '')
  
  phoneNumber.value = convertedValue
  updateValue()
}

const handleFocus = (event) => {
  // フォーカス時にIMEを無効化して半角数字入力に強制切り替え
  forceNumericInput(event.target)
}

const handleMouseDown = (event) => {
  // マウスクリック時にもIMEを無効化
  forceNumericInput(event.target)
}

const forceNumericInput = (input) => {
  // inputmode="numeric"を設定
  input.setAttribute('inputmode', 'numeric')
  input.setAttribute('pattern', '[0-9]*')
  
  // 日本語IMEを無効化
  input.style.imeMode = 'disabled'
  
  // モバイルデバイスで数字キーパッドを表示
  if ('virtualKeyboard' in navigator) {
    input.setAttribute('inputmode', 'numeric')
  }
}

const updateValue = () => {
  const fullNumber = selectedCountry.value.code + phoneNumber.value
  emit('update:modelValue', fullNumber)
}

// 外部クリックでドロップダウンを閉じる
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showCountryDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  // 初期値を設定
  if (props.modelValue) {
    const countryCode = countries.find(c => props.modelValue.startsWith(c.code))
    if (countryCode) {
      selectedCountry.value = countryCode
      phoneNumber.value = props.modelValue.substring(countryCode.code.length)
    }
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
input[type="tel"] {
  /* IMEモードを無効化 */
  ime-mode: disabled;
  /* 数字入力に最適化 */
  -webkit-text-security: none;
  /* モバイルで数字キーパッドを表示 */
  -webkit-appearance: none;
  appearance: none;
}

/* 数字以外の入力を防ぐ */
input[type="tel"]::-webkit-outer-spin-button,
input[type="tel"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

input[type="tel"][type=number] {
  -moz-appearance: textfield;
}
</style>
