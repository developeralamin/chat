<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Chat</h2>
      <button 
         @click="logout()"
        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
      >
        Logout
      </button>
    </div>
    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
      <div v-for="msg in messages" :key="msg.id" class="mb-2">
        <strong class="text-blue-600">{{ msg.user.name }}:</strong> 
        <span class="text-gray-800">{{ msg.message }}</span>
      </div>
    </div>
    <div class="flex">
      <input 
        v-model="message" 
        @keyup.enter="sendMessage" 
        placeholder="Type your message..." 
        class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
      />
      <button 
        @click="sendMessage"
        class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
      >
        Send
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthenticateStore } from '@/stores/authenticate'

const authenticate = useAuthenticateStore()
const router = useRouter()
const messages = ref([])
const message = ref('')
const user = ref(null)

const sendMessage = async () => {
  if (message.value.trim() === '') return
  try {
    console.log('Attempting to send message:', message.value)
    const { data } = await axios.post('/api/messages', { message: message.value })
    
    console.log('Message sent successfully, response:', data)
    
    messages.value.push(data)
    message.value = ''
  } catch (error) {
    console.error('Error sending message:', error)
    router.push('/login')
  }
}

onMounted(async () => {
  // Check if user is authenticated
  if (!authenticate.isAuthenticated) {
    router.push('/login')
    return
  }

  try {
    const { data } = await axios.get('/api/messages')
    messages.value = data


    // Subscribe to the chat channel
    const channel = window.Echo.channel('chat')
    
    // Listen for new messages
    channel.listen('MessageSent', (e) => {
      // console.log('MessageSent event received:', e)
      // console.log('Current user ID:', user.value.id)
      // console.log('Message sender ID:', e.message.user.id)
      
      // Always add the message if it's not already in the array
      if (!messages.value.some(msg => msg.id === e.message.id)) {
        messages.value.push(e.message)
        // console.log('New message added:', e.message)
      } else {
        // console.log('Message already exists, skipping...')
      }
    })

    // Handle connection errors
    channel.error((error) => {
      console.error('Pusher connection error:', error)
    })

    // Handle successful subscription
    channel.subscribed(() => {
      console.log('Successfully subscribed to chat channel')
    })

    console.log('Pusher connection setup completed')
  } catch (error) {
    console.error('Error in mounted:', error)
    router.push('/login')
  }
})


//logout
const logout = () => {
  authenticate.logout()
  router.push('/login')
}
</script>
