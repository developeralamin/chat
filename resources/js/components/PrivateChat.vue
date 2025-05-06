<template>
  <section class="chat-wrapper">
    <div class="sidebar">
      <div class="sidebar-header">
        <div class="current-user">
          <img class="avatar"  :src="`https://ui-avatars.com/api/?name=${currentUser.name}`"  alt="avatar">
          <div>
            <h4>{{ currentUser.name }}</h4>
            <span class="role">{{ currentUser.role || 'User' }}</span>
          </div>
        </div>
        <span class="edit-icon"><i class="fa fa-pencil"></i></span>
      </div>
      <div class="sidebar-search">
        <input type="text" v-model="searchQuery" placeholder="Search Here..." />
      </div>
      <div class="user-list">
        <div v-for="user in filteredUsers" :key="user.id" :class="['user-item', { active: selectedUser && selectedUser.id === user.id }]" @click="selectUser(user)">
          <img class="avatar" :src="`https://ui-avatars.com/api/?name=${user.name}`" alt="avatar">
          <div class="user-info">
            <div class="user-row">
              <span class="user-name">{{ user.name }}</span>
              <span class="user-time">10:35 AM</span>
            </div>
            <div class="user-row">
              <span class="user-last">{{ user.last_message || '...' }}</span>
            </div>
          </div>
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
          <i class="fa fa-search"></i>
          <i class="fa fa-heart-o"></i>
          <i class="fa fa-user-o"></i>
        </div>
      </div>
      <div class="chat-body" ref="chatBodyRef">
        <div v-for="(message, index) in messages" :key="message.id">
          <!-- <div v-if="shouldShowDateDivider(index)" class="date-divider">
            <span>{{ formatDate(message.created_at) }}</span>
          </div> -->
          <div :class="['chat-message', message.sender_id === currentUser.id ? 'sent' : 'received']">
            <img  class="avatar" :src="`https://ui-avatars.com/api/?name=${message.sender_id === currentUser.id ? currentUser.name : selectedUser.name}`" 
  alt="avatar">
            <div class="bubble">
              <p>{{ message.message }}</p>
              <span class="time">{{ formatTime(message.created_at) }}</span>
            </div>
          </div>
        </div>
      </div>
      <div class="chat-input-area">
        <div class="input-wrapper">
          <i class="fa fa-microphone"></i>
          <input type="text" v-model="newMessage" placeholder="Write Something..." @keyup.enter="sendMessage" />
          <i class="fa fa-paperclip"></i>
          <i class="fa fa-smile-o"></i>
        </div>
        <button class="send-btn-rect" @click="sendMessage">
          <i class="fa fa-paper-plane"></i>
          <span>Send</span>
        </button>
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
// import dayjs from 'dayjs'

const authenticate = useAuthenticateStore()
const currentUser = ref(authenticate.user)
const users = ref([])
const messages = ref([])
const selectedUser = ref(null)
const newMessage = ref('')
const searchQuery = ref('')
const activeTab = ref('Open')
const chatBodyRef = ref(null)

// Filter users based on search query
const filteredUsers = computed(() => {
  return users.value.filter(user => 
    user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

// Format message time
const formatTime = (date) => {
  // return dayjs(date).format('h:mm a')
}

// Format message date
const formatDate = (date) => {
  // return dayjs(date).format('MMMM D, YYYY')
}


const conversationUser = ref(null);



// Check if we should show date divider
const shouldShowDateDivider = (index) => {
  if (index === 0) return true
  // const currentDate = dayjs(messages.value[index].created_at).format('YYYY-MM-DD')
  // const previousDate = dayjs(messages.value[index - 1].created_at).format('YYYY-MM-DD')
  // return currentDate !== previousDate
}

// Select user and load messages
const selectUser = async (user) => {
  selectedUser.value = user
  try {
    const response = await axios.get(`/api/chats/${user.id}`)
    messages.value = response.data
    // subscribeToPrivateChannel(user.id)
  } catch (error) {
    console.error('Error loading messages:', error)
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


window.Echo.channel(`chat.${currentUser.value.id}`)
    .listen('PrivateMessageSent', (e) => {
      console.log('Private message received:', e)

      if(selectedUser.value.id == e.chat.sender.id){
        messages.value.push(e.chat)
        scrollToBottom()
      }else{
        fetchUsers()
      }
})

// Load users on component mount
onMounted(async () => {
    fetchUsers()
})

const  fetchUsers = async() => {
    try {
    const response = await axios.get('/api/users')
    users.value = response.data
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
</script>

<style scoped>
.chat-wrapper {
  display: flex;
  height: 100vh;
  background: #f4f7fa;
  border-radius: 20px;
  overflow: hidden;
}
.sidebar {
  width: 320px;
  background: #f7f8fa;
  border-top-left-radius: 20px;
  border-bottom-left-radius: 20px;
  display: flex;
  flex-direction: column;
  box-shadow: 2px 0 8px rgba(0,0,0,0.04);
}
.sidebar-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 20px 12px 20px;
}
.current-user {
  display: flex;
  align-items: center;
  gap: 12px;
}
.current-user .avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
}
.role {
  font-size: 12px;
  color: #888;
}
.edit-icon {
  color: #b0b0b0;
  cursor: pointer;
}
.sidebar-search {
  padding: 0 20px 16px 20px;
}
.sidebar-search input {
  width: 100%;
  padding: 10px 16px;
  border-radius: 20px;
  border: none;
  background: #e9eef6;
  font-size: 15px;
  outline: none;
}
.user-list {
  flex: 1;
  overflow-y: auto;
  padding-bottom: 20px;
}
.user-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  cursor: pointer;
  border-left: 4px solid transparent;
  transition: background 0.2s, border 0.2s;
}
.user-item.active {
  background: #e6f0ff;
  border-left: 4px solid #4a90e2;
}
.user-item .avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
}
.user-info {
  flex: 1;
}
.user-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.user-name {
  font-weight: 600;
  color: #222;
}
.user-time {
  font-size: 12px;
  color: #b0b0b0;
}
.user-last {
  font-size: 13px;
  color: #888;
  margin-top: 2px;
}
.user-status {
  width: 8px;
  height: 8px;
  background: #4cd964;
  border-radius: 50%;
  margin-left: 8px;
}

.chat-area {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #fff;
  border-top-right-radius: 20px;
  border-bottom-right-radius: 20px;
  box-shadow: 0 0 10px rgba(0,0,0,0.04);
  position: relative;
}
.chat-area.empty {
  align-items: center;
  justify-content: center;
  display: flex;
}
.empty-message {
  color: #aaa;
  font-size: 20px;
}
.chat-header {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 24px 32px 16px 32px;
  border-bottom: 1px solid #f0f0f0;
}
.chat-header .avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
}
.chat-user-info {
  flex: 1;
}
.chat-user-info h4 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #222;
}
.online-status {
  display: inline-block;
  width: 10px;
  height: 10px;
  background: #4cd964;
  border-radius: 50%;
  margin-left: 8px;
}
.chat-header-actions {
  display: flex;
  gap: 18px;
  color: #b0b0b0;
  font-size: 20px;
}
.chat-body {
  flex: 1;
  overflow-y: auto;
  padding: 32px 32px 24px 32px;
  background: #f7f8fa;
  display: flex;
  flex-direction: column;
}
.date-divider {
  text-align: center;
  margin: 18px 0 18px 0;
  color: #b0b0b0;
  font-size: 13px;
  position: relative;
}
.date-divider span {
  background: #f7f8fa;
  padding: 0 16px;
  position: relative;
  z-index: 1;
}
.date-divider:before {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  top: 50%;
  height: 1px;
  background: #e0e0e0;
  z-index: 0;
}
.chat-message {
  display: flex;
  align-items: flex-end;
  margin-bottom: 18px;
}
.chat-message.sent {
  flex-direction: row-reverse;
}
.chat-message .avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  margin: 0 10px;
}
.bubble {
  max-width: 60%;
  padding: 14px 18px;
  border-radius: 18px;
  font-size: 15px;
  position: relative;
  background: #f1f1f1;
  color: #222;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}
.chat-message.sent .bubble {
  background: #e6f0ff;
  color: #222;
  border-bottom-right-radius: 6px;
  border-bottom-left-radius: 18px;
}
.chat-message.received .bubble {
  background: #fff;
  color: #222;
  border-bottom-left-radius: 6px;
  border-bottom-right-radius: 18px;
}
.bubble .time {
  display: block;
  font-size: 11px;
  color: #b0b0b0;
  margin-top: 6px;
  text-align: right;
}
.chat-input-area {
  display: flex;
  align-items: center;
  padding: 18px 32px;
  background: #e6f0ff;
  border-bottom-right-radius: 20px;
}
.input-wrapper {
  flex: 1;
  display: flex;
  align-items: center;
  background: #fff;
  border-radius: 30px;
  padding: 0 18px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
  gap: 12px;
}
.input-wrapper input {
  border: none;
  outline: none;
  background: transparent;
  flex: 1;
  padding: 12px 0;
  font-size: 15px;
}
.input-wrapper i {
  color: #b0b0b0;
  font-size: 20px;
  cursor: pointer;
}
.send-btn-rect {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #3366e7;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 10px 0;
  font-size: 20px;
  font-weight: 500;
  width: 220px;
  height: 48px;
  margin-left: 18px;
  cursor: pointer;
  transition: background 0.2s, box-shadow 0.2s;
  box-shadow: 0 2px 8px rgba(51,102,231,0.10);
}
.send-btn-rect:hover, .send-btn-rect:focus {
  background: #2451b7;
}
</style> 