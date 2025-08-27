<template>
  <div class="input-wrapper">
    <label v-if="label" :for="inputId" class="input-label">
      {{ label }}
      <span v-if="required" class="required-mark">*</span>
    </label>
    
    <div class="input-container">
      <input
        :id="inputId"
        :type="inputType"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :maxlength="maxlength"
        :minlength="minlength"
        :pattern="pattern"
        :autocomplete="autocomplete"
        :class="inputClasses"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
      />
      
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
      
      <div v-if="hint" class="hint-message">
        {{ hint }}
      </div>
    </div>
  </div>
</template>

<script>
import { computed, ref } from 'vue'

export default {
  name: 'BaseInput',
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    type: {
      type: String,
      default: 'text',
      validator: (value) => [
        'text', 'email', 'password', 'number', 'tel', 'url', 'search', 'date', 'time', 'datetime-local'
      ].includes(value)
    },
    label: {
      type: String,
      default: ''
    },
    placeholder: {
      type: String,
      default: ''
    },
    required: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    readonly: {
      type: Boolean,
      default: false
    },
    error: {
      type: String,
      default: ''
    },
    hint: {
      type: String,
      default: ''
    },
    maxlength: {
      type: Number,
      default: null
    },
    minlength: {
      type: Number,
      default: null
    },
    pattern: {
      type: String,
      default: ''
    },
    autocomplete: {
      type: String,
      default: 'off'
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    variant: {
      type: String,
      default: 'default',
      validator: (value) => ['default', 'outlined', 'filled'].includes(value)
    }
  },
  emits: ['update:modelValue', 'focus', 'blur', 'keydown'],
  setup(props, { emit }) {
    const inputId = ref(`input-${Math.random().toString(36).substr(2, 9)}`)
    const isFocused = ref(false)

    const inputType = computed(() => {
      // パスワードフィールドの表示/非表示切り替え対応
      return props.type
    })

    const inputClasses = computed(() => {
      const baseClasses = [
        'w-full',
        'transition-all',
        'duration-200',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-evox-blue',
        'focus:ring-opacity-50',
        'disabled:opacity-50',
        'disabled:cursor-not-allowed',
        'readonly:bg-gray-100'
      ]

      // サイズクラス
      const sizeClasses = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-base',
        lg: 'px-6 py-3 text-lg'
      }

      // バリアントクラス
      const variantClasses = {
        default: 'border border-gray-300 rounded-md bg-white text-gray-900',
        outlined: 'border-2 border-gray-300 rounded-md bg-transparent text-gray-900',
        filled: 'border-none rounded-md bg-gray-100 text-gray-900 focus:bg-white'
      }

      // エラー状態クラス
      const errorClasses = props.error ? 'border-red-500 focus:ring-red-500' : ''

      // フォーカス状態クラス
      const focusClasses = isFocused.value ? 'ring-2 ring-evox-blue ring-opacity-50' : ''

      return [
        ...baseClasses,
        sizeClasses[props.size],
        variantClasses[props.variant],
        errorClasses,
        focusClasses
      ].filter(Boolean)
    })

    const handleInput = (event) => {
      emit('update:modelValue', event.target.value)
    }

    const handleFocus = (event) => {
      isFocused.value = true
      emit('focus', event)
    }

    const handleBlur = (event) => {
      isFocused.value = false
      emit('blur', event)
    }

    const handleKeydown = (event) => {
      emit('keydown', event)
    }

    return {
      inputId,
      inputType,
      inputClasses,
      handleInput,
      handleFocus,
      handleBlur,
      handleKeydown
    }
  }
}
</script>

<style scoped>
.input-wrapper {
  @apply w-full;
}

.input-label {
  @apply block text-sm font-medium text-gray-700 mb-1;
}

.required-mark {
  @apply text-red-500 ml-1;
}

.input-container {
  @apply relative;
}

.error-message {
  @apply text-sm text-red-600 mt-1;
}

.hint-message {
  @apply text-sm text-gray-500 mt-1;
}
</style>
