<template>
  <div class="flex justify-center space-x-2 otp-input-container">
    <input
      v-for="(digit, index) in 6"
      :key="index"
      :ref="el => inputRefs[index] = el"
      v-model="digits[index]"
      type="text"
      inputmode="numeric"
      pattern="[0-9]*"
      maxlength="1"
      class="w-12 h-12 text-center text-xl font-bold input-field"
      @input="handleInput(index, $event)"
      @keydown="handleKeydown(index, $event)"
      @paste="handlePaste"
      @focus="handleFocus(index)"
      @mousedown="handleMouseDown(index)"
    />
  </div>
</template>

<script>
import { ref, watch, nextTick, onMounted } from 'vue'

export default {
  name: 'OtpInput',
  props: {
    modelValue: {
      type: String,
      default: ''
    }
  },
  emits: ['update:modelValue'],
  setup(props, { emit }) {
    const digits = ref(['', '', '', '', '', ''])
    const inputRefs = ref([])
    
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
      // 基本的な漢数字のマッピング
      const basicDigits = {
        '零': 0, '一': 1, '二': 2, '三': 3, '四': 4,
        '五': 5, '六': 6, '七': 7, '八': 8, '九': 9
      }
      
      // 位の漢数字のマッピング
      const placeDigits = {
        '十': 10, '百': 100, '千': 1000, '万': 10000,
        '億': 100000000, '兆': 1000000000000
      }
      
      // 簡単な変換（一桁の漢数字のみ）
      if (kanjiStr.length === 1) {
        if (basicDigits[kanjiStr] !== undefined) {
          return basicDigits[kanjiStr].toString()
        }
        if (placeDigits[kanjiStr] !== undefined) {
          return placeDigits[kanjiStr].toString()
        }
      }
      
      // 複雑な漢数字の場合は、基本的な変換のみ行う
      let result = ''
      for (let i = 0; i < kanjiStr.length; i++) {
        const char = kanjiStr[i]
        if (basicDigits[char] !== undefined) {
          result += basicDigits[char]
        } else if (placeDigits[char] !== undefined) {
          // 位の漢数字は無視（認証コードでは使用しない）
          continue
        }
      }
      
      return result || '0'
    }
    
    // 外部からの値の変更を監視
    watch(() => props.modelValue, (newValue) => {
      if (newValue && newValue.length <= 6) {
        const newDigits = newValue.split('')
        digits.value = [...newDigits, ...Array(6 - newDigits.length).fill('')]
      }
    }, { immediate: true })
    
    // 内部の値の変更を外部に通知
    watch(digits, (newDigits) => {
      const value = newDigits.join('')
      emit('update:modelValue', value)
    }, { deep: true })
    
    const handleInput = (index, event) => {
      let value = event.target.value
      
      // 全角数字と漢数字を半角数字に変換
      value = convertFullWidthToHalfWidth(value)
      
      // 数字以外は無視
      if (!/^\d*$/.test(value)) {
        digits.value[index] = ''
        return
      }
      
      digits.value[index] = value
      
      // 次の入力フィールドにフォーカス
      if (value && index < 5) {
        nextTick(() => {
          inputRefs.value[index + 1]?.focus()
        })
      }
    }
    
    const handleKeydown = (index, event) => {
      // バックスペースで前のフィールドに移動
      if (event.key === 'Backspace' && !digits.value[index] && index > 0) {
        nextTick(() => {
          inputRefs.value[index - 1]?.focus()
        })
      }
    }
    
    const handlePaste = (event) => {
      event.preventDefault()
      const pastedData = event.clipboardData.getData('text')
      
      // 全角数字と漢数字を半角数字に変換
      let convertedData = convertFullWidthToHalfWidth(pastedData)
      
      // 数字以外を削除
      const numbers = convertedData.replace(/\D/g, '').slice(0, 6)
      
      if (numbers.length > 0) {
        const newDigits = numbers.split('')
        digits.value = [...newDigits, ...Array(6 - newDigits.length).fill('')]
        
        // 最後の入力フィールドにフォーカス
        nextTick(() => {
          const lastIndex = Math.min(numbers.length - 1, 5)
          inputRefs.value[lastIndex]?.focus()
        })
      }
    }
    
    const handleFocus = (index) => {
      // フォーカス時に全選択
      nextTick(() => {
        inputRefs.value[index]?.select()
      })
      
      // 半角数字入力に強制切り替え
      forceNumericInput(index)
    }
    
    const handleMouseDown = (index) => {
      // マウスクリック時にも半角数字入力に強制切り替え
      forceNumericInput(index)
    }
    
    const forceNumericInput = (index) => {
      const input = inputRefs.value[index]
      if (input) {
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
    }
    
    // コンポーネントマウント時に全入力フィールドを半角数字入力に設定
    onMounted(() => {
      nextTick(() => {
        for (let i = 0; i < 6; i++) {
          forceNumericInput(i)
        }
      })
    })
    
    return {
      digits,
      inputRefs,
      handleInput,
      handleKeydown,
      handlePaste,
      handleFocus,
      handleMouseDown
    }
  }
}
</script>

<style scoped>
.otp-input-container input {
  /* IMEモードを無効化 */
  ime-mode: disabled;
  /* 数字入力に最適化 */
  -webkit-text-security: none;
  /* モバイルで数字キーパッドを表示 */
  -webkit-appearance: none;
  appearance: none;
}

/* 数字以外の入力を防ぐ */
.otp-input-container input::-webkit-outer-spin-button,
.otp-input-container input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.otp-input-container input[type=number] {
  -moz-appearance: textfield;
}
</style>
