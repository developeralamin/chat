import { createRouter, createWebHistory } from 'vue-router'
import Login from '../components/Login.vue'
import PrivateChat from '../components/PrivateChat.vue'
import Register from '../components/Register.vue'

const routes = [
    { path: '/', redirect: '/login' },
    { path: '/login',  name: "login", component: Login },
    { path: '/register', component: Register },
    { 
        path: '/chat', 
        component: PrivateChat,
        meta: { requiresAuth: true }
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !localStorage.getItem('token')) {
        next('/login')
    } else {
        next()
    }
})

export default router
