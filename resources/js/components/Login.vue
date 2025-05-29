<template>
  <div class="flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to your account
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="onFinish">
        <div class="">
          <div>
            <label for="email" class="sr-only">Email address</label>
            <input
              id="email"
              v-model="login.email"
              name="email"
              type="email"
              @keyup="login.errors.email = null"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Email address"
            />
          </div>
           <p class="extra" v-if="login.errors.email"> {{ login.errors.email[0] }}</p>
          <br/>
          <div>
            <label for="password" class="sr-only">Password</label>
            <input
              id="password"
              v-model="login.password"
              name="password"
              type="password"
              @keyup="login.errors.password = null"
              class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              placeholder="Password"
            />
          </div>
           <p class="extra" v-if="login.errors.password"> {{ login.errors.password[0] }}</p>
        </div>

        <div>
          <button
            type="submit"
            :disabled="loading"
            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            {{ loading ? 'Signing in...' : 'Sign in' }}
          </button>
        </div>

        <div>
          <router-link to="/register">
            <button class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
               Don't have an account ? Register
            </button>
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref ,reactive} from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuthenticateStore } from '@/stores/authenticate'

const router = useRouter()
const authenticate = useAuthenticateStore()
const loading = ref(false)

const login = reactive({
  email: "",
  password: "",
  errors: {},
});


//login
const onFinish = () => {
  loading.value = true
   axios.post('/api/login',login)
      .then((response)=>{
          authenticate.login(response.data.data.token)
          authenticate.setUser(response.data.data.user)
          loading.value = false
          router.push('/chat')
    }).catch((error)=>{
       if (error.response.data.status == "fail") {
           alert(error.response.data.message)
       }
       if (error.response?.data?.errors) {
        login.errors = error.response.data.errors;
       }
      login.password = "";
      loading.value = false;
    })
}

</script> 

<style scoped>
.extra{
  color:red
}
</style>