<template>
  <div>
    <nav class="bg-gray-800 text-white p-4">
      <div class="container mx-auto">
        <router-link to="/" class="mr-4">Home</router-link>
        <router-link to="/chat">Chat</router-link>
      </div>
    </nav>
    <div class="container mx-auto p-4">
      <router-view></router-view>
    </div>
  </div>
</template> 