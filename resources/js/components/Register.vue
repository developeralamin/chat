<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Create your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="onRegister">
        <div class="">
          <div>
            <label for="name" class="sr-only">Name</label>
            <input
              id="name"
              v-model="register.name"
              name="name"
              type="text"
              @keyup="register.errors.name = null"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Name"
            />
            <p class="extra" v-if="register.errors.name"> {{ register.errors.name[0] }}</p>
          </div>
           <br/>
          <div>
            <label for="email" class="sr-only">Email address</label>
            <input
              id="email"
              v-model="register.email"
              name="email"
              type="email"
              @keyup="register.errors.email = null"
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Email address"
            />
            <p class="extra" v-if="register.errors.email"> {{ register.errors.email[0] }}</p>
          </div>
           <br/>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input
              id="password"
              v-model="register.password"
              name="password"
              type="password"
              @keyup="register.errors.password = null"
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Password"
            />
            <p class="extra" v-if="register.errors.password"> {{ register.errors.password[0] }}</p>
          </div>
          <br/>
          <div>
            <label for="password_confirmation" class="sr-only">Confirm Password</label>
            <input
              id="password_confirmation"
              v-model="register.password_confirmation"
              name="password_confirmation"
              type="password"
              @keyup="register.errors.password = null"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              placeholder="Confirm Password"
            />
            <p class="extra" v-if="register.errors.password"> {{ register.errors.password[0] }}</p>
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ loading ? 'Registering...' : 'Register' }}
          </button>
        </div>

        <div>
          <router-link to="/login">
            <button class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Already have an account? Login
            </button>
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const router = useRouter()
const loading = ref(false)

const register = reactive({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  errors: {}
})

// register
const onRegister = async () => {
  loading.value = true
  axios.post('/api/register',register)
    .then((response)=>{
      loading.value = false
      router.push('/login')
  }).catch((error)=>{
    if (error.response?.data?.errors) {
     register.errors = error.response.data.errors;
    }
    loading.value = false;
  })
}
</script>

<style scoped>
.extra {
  color: red;
  font-size: 0.875rem;
}
</style>
