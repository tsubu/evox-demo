<template>
  <div class="penlight-test-page">
    <h1>ペンライトテストページ</h1>
    <p>このページが表示されれば、ルーティングは正常です。</p>
    <div class="test-info">
      <p>現在のURL: {{ currentUrl }}</p>
      <p>ルート名: {{ routeName }}</p>
      <p>ルートパス: {{ routePath }}</p>
    </div>
    <button @click="goBack">戻る</button>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const currentUrl = ref('')
const routeName = ref('')
const routePath = ref('')

const goBack = () => {
  router.push('/')
}

onMounted(() => {
  console.log('PenlightTest - Component mounted')
  currentUrl.value = window.location.href
  routeName.value = route.name
  routePath.value = route.path
  console.log('PenlightTest - Route info:', { name: route.name, path: route.path })
})
</script>

<style scoped>
.penlight-test-page {
  min-height: 100vh;
  background: #000;
  color: #fff;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 20px;
  text-align: center;
}

h1 {
  font-size: 3rem;
  margin-bottom: 20px;
  color: #ff0066;
}

p {
  font-size: 1.2rem;
  margin-bottom: 10px;
}

.test-info {
  background: rgba(255, 255, 255, 0.1);
  padding: 20px;
  border-radius: 10px;
  margin: 20px 0;
  border: 1px solid #333;
}

button {
  background: #ff0066;
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  font-size: 1.1rem;
  cursor: pointer;
  margin-top: 20px;
}

button:hover {
  background: #cc0052;
}
</style>

