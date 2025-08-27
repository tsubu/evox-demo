<template>
  <button
    :class="buttonClasses"
    :disabled="disabled || loading"
    :type="type"
    @click="handleClick"
  >
    <div v-if="loading" class="loading-spinner mr-2"></div>
    <slot />
  </button>
</template>

<script>
import { computed } from 'vue'
import { DESIGN_CONFIG } from '@/config/design'

export default {
  name: 'BaseButton',
  props: {
    variant: {
      type: String,
      default: 'primary',
      validator: (value) => ['primary', 'secondary', 'outline', 'ghost', 'danger'].includes(value)
    },
    size: {
      type: String,
      default: 'md',
      validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
    },
    disabled: {
      type: Boolean,
      default: false
    },
    loading: {
      type: Boolean,
      default: false
    },
    fullWidth: {
      type: Boolean,
      default: false
    },
    type: {
      type: String,
      default: 'button'
    },
    rounded: {
      type: Boolean,
      default: false
    }
  },
  emits: ['click'],
  setup(props, { emit }) {
    const buttonClasses = computed(() => {
      const baseClasses = [
        'inline-flex',
        'items-center',
        'justify-center',
        'font-medium',
        'transition-all',
        'duration-300',
        'focus:outline-none',
        'focus:ring-2',
        'focus:ring-offset-2',
        'disabled:opacity-50',
        'disabled:cursor-not-allowed'
      ]

      // サイズクラス
      const sizeClasses = {
        sm: 'px-3 py-1.5 text-sm',
        md: 'px-4 py-2 text-base',
        lg: 'px-6 py-3 text-lg',
        xl: 'px-8 py-4 text-xl'
      }

      // バリアントクラス
      const variantClasses = {
        primary: 'bg-evox-blue text-white hover:bg-blue-700 focus:ring-evox-blue',
        secondary: 'bg-evox-gray text-white hover:bg-gray-700 focus:ring-evox-gray',
        outline: 'border-2 border-evox-blue text-evox-blue hover:bg-evox-blue hover:text-white focus:ring-evox-blue',
        ghost: 'text-evox-blue hover:bg-evox-blue hover:text-white focus:ring-evox-blue',
        danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500'
      }

      // 幅クラス
      const widthClasses = props.fullWidth ? 'w-full' : ''

      // 角丸クラス
      const roundedClasses = props.rounded ? 'rounded-full' : 'rounded-md'

      return [
        ...baseClasses,
        sizeClasses[props.size],
        variantClasses[props.variant],
        widthClasses,
        roundedClasses
      ].filter(Boolean)
    })

    const handleClick = (event) => {
      if (!props.disabled && !props.loading) {
        emit('click', event)
      }
    }

    return {
      buttonClasses,
      handleClick
    }
  }
}
</script>

<style scoped>
.loading-spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid transparent;
  border-top: 2px solid currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
