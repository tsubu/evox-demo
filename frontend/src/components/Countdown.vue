<template>
  <div class="countdown-container">
    <div class="countdown-item">
      <span class="countdown-number font-sevenbar">{{ days }}</span>
      <img src="/images/time_d.png" alt="日" class="countdown-icon" />
    </div>
    <div class="countdown-item">
      <span class="countdown-number font-sevenbar">{{ hours }}</span>
      <img src="/images/time_h.png" alt="時" class="countdown-icon" />
    </div>
    <div class="countdown-item">
      <span class="countdown-number font-sevenbar">{{ minutes }}</span>
      <img src="/images/time_m.png" alt="分" class="countdown-icon" />
    </div>
    <div class="countdown-item">
      <span class="countdown-number font-sevenbar">{{ seconds }}</span>
      <img src="/images/time_s.png" alt="秒" class="countdown-icon" />
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'Countdown',
  props: {
    targetDate: {
      type: Date,
      required: true
    }
  },
  setup(props) {
    const days = ref(0)
    const hours = ref(0)
    const minutes = ref(0)
    const seconds = ref(0)
    let interval = null

    const updateCountdown = () => {
      const now = new Date().getTime()
      const target = props.targetDate.getTime()
      const difference = target - now

      if (difference > 0) {
        const daysNum = Math.floor(difference / (1000 * 60 * 60 * 24))
        const hoursNum = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
        const minutesNum = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60))
        const secondsNum = Math.floor((difference % (1000 * 60)) / 1000)
        
        days.value = daysNum.toString().padStart(2, '0')
        hours.value = hoursNum.toString().padStart(2, '0')
        minutes.value = minutesNum.toString().padStart(2, '0')
        seconds.value = secondsNum.toString().padStart(2, '0')
      } else {
        days.value = '00'
        hours.value = '00'
        minutes.value = '00'
        seconds.value = '00'
      }
    }

    onMounted(() => {
      updateCountdown()
      interval = setInterval(updateCountdown, 1000)
    })

    onUnmounted(() => {
      if (interval) {
        clearInterval(interval)
      }
    })

    return {
      days,
      hours,
      minutes,
      seconds
    }
  }
}
</script>

<style scoped>
.countdown-container {
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 1rem;
  width: calc(100% - 3em);
  margin: 0 auto;
}

.countdown-item {
  display: flex;
  align-items: flex-end;
  gap: 0.1rem;
}

.countdown-number {
  font-size: 8rem;
  font-weight: normal;
  color: #00AFB6;
  line-height: 1;
  text-shadow: 0 0 10px rgba(0, 175, 182, 0.3);
  display: flex;
  align-items: flex-end;
}

.countdown-icon {
  width: 21.5px;
  height: auto;
  object-fit: contain;
  margin-top: -1rem;
  margin-bottom: 1.4em;
}

/* 980px以下 */
@media (max-width: 980px) {
  .countdown-number {
    font-size: 6rem;
  }
  
  .countdown-icon {
    width: 16.5px;
    height: auto;
    margin-top: -0.8rem;
    margin-bottom: 1.05em;
  }
}

/* 480px以下 */
@media (max-width: 480px) {
  .countdown-container {
    width: calc(100% - 1em);
    gap: 0.5rem;
  }
  
  .countdown-number {
    font-size: 4rem;
  }
  
  .countdown-icon {
    width: 10.8px;
    height: auto;
    margin-top: -0.5rem;
    margin-bottom: 0.75em;
  }
}
</style>
