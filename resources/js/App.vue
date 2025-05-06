<template>
  <div>
    <nav class="bg-gray-800 text-white p-4">
      <div class="container mx-auto flex justify-between items-center">
        <div>
          <router-link to="/" class="mr-4">Home</router-link>
          <router-link to="/chat">Chat</router-link>
        </div>
        <button 
          v-if="authenticate.isAuthenticated"
          @click="logout()"
          class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
        >
          Logout
        </button>
      </div>
    </nav>
    <div class="container mx-auto p-4">
      <router-view></router-view>
    </div>
  </div>
</template> 

<script setup>
import { useAuthenticateStore } from '@/stores/authenticate'
import { useRouter } from 'vue-router'

const authenticate = useAuthenticateStore()
const router = useRouter()
//logout
const logout = () => {
  authenticate.logout()
  router.push('/login')
}
</script>