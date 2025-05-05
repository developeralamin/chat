<template>
  <div>
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold">Chat</h2>
      <button 
        @click="logout" 
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

<script>
import axios from 'axios'

export default {
  data() {
    return {
      messages: [],
      message: '',
      user: null
    }
  },
  async mounted() {
    // Set up axios defaults
    const token = localStorage.getItem('token')
    if (token) {
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
      axios.defaults.withCredentials = true
    }

    try {
      console.log('Fetching user data...')
      const res = await axios.get('/api/me')
      this.user = res.data
      console.log('User data fetched:', this.user)

      console.log('Fetching messages...')
      const { data } = await axios.get('/api/messages')
      this.messages = data

      console.log('Initial messages loaded:', this.messages)

      console.log('Setting up Pusher connection...')
      console.log('Pusher config:', {
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER
      })

      // Subscribe to the chat channel
      const channel = window.Echo.channel('chat')
      
      // Listen for new messages
      channel.listen('MessageSent', (e) => {
        console.log('MessageSent event received:', e)
        console.log('Current user ID:', this.user.id)
        console.log('Message sender ID:', e.message.user.id)
        
        // Always add the message if it's not already in the array
        if (!this.messages.some(msg => msg.id === e.message.id)) {
          this.messages.push(e.message)
          console.log('New message added:', e.message)
        } else {
          console.log('Message already exists, skipping...')
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
      this.$router.push('/login')
    }
  },
  methods: {
    async sendMessage() {
      if (this.message.trim() === '') return
      try {
        console.log('Attempting to send message:', this.message)
        const { data } = await axios.post('/api/messages', { message: this.message })
        
        console.log('Message sent successfully, response:', data)
        
        this.messages.push(this.message)
        this.message = ''
      } catch (error) {
        console.error('Error sending message:', error)
        this.$router.push('/login')
      }
    },
    async logout() {
      try {
        await axios.post('/api/logout')
        localStorage.removeItem('token')
        delete axios.defaults.headers.common['Authorization']
        this.$router.push('/login')
      } catch (error) {
        console.error('Logout failed:', error)
      }
    }
  }
}
</script>
