import axios from "axios";
import { defineStore } from "pinia";
import { computed, ref } from 'vue';

export const useAuthenticateStore = defineStore('authenticate', () => {
  const token = ref(localStorage.getItem("token") || "");
  const user = ref(JSON.parse(localStorage.getItem("user")) || null);

  const isAuthenticated = computed(() => !!token.value);
  const getToken = computed(() => token.value);
  const getUser = computed(() => user.value);

  function login(newToken) {
    localStorage.setItem("token", newToken);
    token.value = newToken;
    axios.defaults.headers.common["Authorization"] = `Bearer ${newToken}`;
    axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
    axios.defaults.withCredentials = true;
  }

  function setUser(newUser) {
    localStorage.setItem("user", JSON.stringify(newUser));
    user.value = newUser;
  }

  function logout() {
    localStorage.removeItem("token");
    localStorage.removeItem("user");
    token.value = "";
    user.value = null;
    delete axios.defaults.headers.common["Authorization"];
  }

  return {
    token,
    user,
    isAuthenticated,
    getToken,
    getUser,
    login,
    setUser,
    logout
  };
}); 