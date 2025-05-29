<template>
  <section class="chat-wrapper">
    <div class="sidebar" :class="{ 'mobile-hide': selectedUser && isMobile }">
      <div class="sidebar-header">
        <div class="current-user">
          <img class="avatar"  :src="`https://ui-avatars.com/api/?name=${currentUser.name}`"  alt="avatar">
          <div>
            <h4>{{ currentUser.name }}</h4>
          </div>
        </div>
      </div>
      <div class="sidebar-search">
        <input type="text" v-model="searchQuery" placeholder="Search Here..." />
      </div>
      <div class="user-list" v-if="filteredUsers.length > 0">
        <div v-for="user in filteredUsers" :key="user.id" :class="['user-item', { active: selectedUser && selectedUser.id === user.id }]" @click="selectUser(user)">
          <img class="avatar" :src="`https://ui-avatars.com/api/?name=${user.name}`" alt="avatar">
          <div class="user-info">
            <div class="user-row">
              <span class="user-name">{{ user.name }}</span>
            </div>
            <div class="user-row">
              <span class="user-last">{{ user.last_message || '...' }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="user-list" v-else>
        <div class="user-item">
          <span class="user-name">No users found</span>
        </div>
      </div>
    </div>
    <div class="chat-area" v-if="selectedUser">
      <div class="chat-header">
        <img class="avatar" :src="`https://ui-avatars.com/api/?name=${selectedUser.name}`" alt="avatar">
        <div class="chat-user-info">
          <h4>{{ selectedUser.name }}</h4>
        </div>
        <div class="chat-header-actions">
          <i class="fa fa-phone"></i>
          <i class="fa fa-video-camera"></i>
          <i class="fa fa-times"></i>
        </div>
        <button v-if="isMobile" class="back-btn" @click="selectedUser = null">
          <i class="fa fa-arrow-left"></i> User List
        </button>
      </div>
      <div class="chat-body" ref="chatBodyRef">
        <div v-for="(message, index) in messages" :key="message.id">
          <div :class="['chat-message', message.sender_id === currentUser.id ? 'sent' : 'received']">
            <img class="avatar" :src="`https://ui-avatars.com/api/?name=${message.sender_id === currentUser.id ? currentUser.name : selectedUser.name}`" alt="avatar">
            <div class="bubble">
             
              <p>{{ message.message }}</p>
              <span class="time">{{ dayjs(message.created_at).format('MMM D, YYYY h:mm A') }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="chat-input-fixed-container">
        <div class="chat-input-area">
          <div class="input-wrapper">
            <i class="fa fa-microphone"></i>
            <i class="fa fa-image"></i>
            <i class="fa fa-sticky-note"></i>
            <span>GIF</span>
            <textarea
              ref="chatInputRef"
              v-model="newMessage"
              placeholder="Write Something..."
              @input="autoResize"
              @keyup.enter.exact.prevent="sendMessage"
              rows="1"
              class="chat-textarea"
            ></textarea>
            <!-- <input type="text" v-model="newMessage" placeholder="Write Something..." @keyup.enter="sendMessage" /> -->
            <i class="fa fa-smile-o"></i>
            <span class="green-dot"></span>
            <i class="fa fa-thumbs-up"></i>
          </div>
          <button class="send-btn-rect" @click="sendMessage">
            <i class="fa fa-paper-plane"></i>
            <span>Send</span>
          </button>
        </div>
      </div>
    </div>
    <div class="chat-area empty" v-else>
      <div class="empty-message">Select a user to start chatting</div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import { useAuthenticateStore } from '@/stores/authenticate'
import axios from 'axios'
import dayjs from 'dayjs'

const authenticate = useAuthenticateStore()
const currentUser = ref(authenticate.user)
const users = ref([])
const messages = ref([])
const selectedUser = ref(null)
const newMessage = ref('')
const searchQuery = ref('')
const chatBodyRef = ref(null)
const isMobile = ref(window.innerWidth <= 768)

// Filter users based on search query
const filteredUsers = computed(() => {
  return users.value.filter(user => 
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})


// Select user and load messages
const selectUser = async (user) => {
  selectedUser.value = user
  try {
    const response = await axios.get(`/api/chats/${user.id}`)
    messages.value = response.data
  } catch (error) {
    console.error('Error loading messages:', error)
  }
}

// auto ressize text area
const chatInputRef = ref(null)

const autoResize = () => {
  const el = chatInputRef.value
  if (el) {
    el.style.height = 'auto'
    el.style.height = el.scrollHeight + 'px'
  }
}

// Send message 
const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedUser.value) return
  try {
    const response = await axios.post('/api/chats', {
      receiver_id: selectedUser.value.id,
      message: newMessage.value
    })
    messages.value.push(response.data)
    newMessage.value = ''
  } catch (error) {
    console.error('Error sending message:', error)
  }
}

//here
window.Echo.channel(`chat.${currentUser.value.id}`)
    .listen('PrivateMessageSent', (e) => {
      if(selectedUser.value && selectedUser.value.id == e.chat.sender.id){
        e.chat.isNew = true;
        messages.value.push(e.chat)
        scrollToBottom()
      }else{
        fetchUsers()
      }
})

// Load users
onMounted(async () => {
    fetchUsers()
})

const  fetchUsers = async() => {
    try {
    const response = await axios.get('/api/users')
    users.value = response.data.data.sort((a, b) => {
      // Sort users based on last message time
      const timeA = a.last_message_time || 0;
      const timeB = b.last_message_time || 0;
      return timeB - timeA;
    });
  } catch (error) {
    console.error('Error loading users:', error)
  }
  scrollToBottom()
}

// Scroll to bottom function
const scrollToBottom = () => {
  nextTick(() => {
    if (chatBodyRef.value) {
      chatBodyRef.value.scrollTop = chatBodyRef.value.scrollHeight
    }
  })
}

watch(messages, () => {
  scrollToBottom()
})

window.addEventListener('resize', () => {
  isMobile.value = window.innerWidth <= 768
})
</script>

<style scoped>
.chat-wrapper {
  display: flex;
  height: calc(100vh - 60px); /* Adjust 60px based on your navbar height */
  overflow: hidden;
}

.sidebar {
  width: 300px; /* Adjust as needed */
  overflow-y: auto;
  border-right: 1px solid #eee;
}

.chat-area {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.chat-body {
  flex-grow: 1;
  overflow-y: auto;
  padding: 10px;
}

.chat-input-fixed-container {
  padding: 10px;
  border-top: 1px solid #eee;
}

/* Add more styles as needed */

@media (max-width: 768px) {
  .sidebar.mobile-hide {
    display: none;
  }
  .chat-area {
    width: 100%;
  }
}
</style> 