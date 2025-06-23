<template>
    <div class="home">
      <div class="text-center py-5">
        <h1>Welcome to TO-DO Manager </h1>
        <p class="lead">Organize your tasks with our simple and intuitive task management system</p>
        <div v-if="!isAuthenticated">
          <router-link to="/login" class="btn btn-primary me-2">Login</router-link>
          <router-link to="/register" class="btn btn-outline-primary">Register</router-link>
        </div>
        <div v-else>
          <router-link to="/dashboard" class="btn btn-primary">Go to Dashboard</router-link>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { computed } from 'vue'
  import { useStore } from 'vuex'
  
  export default {
    name: 'HomeApp',
    setup() {
      const store = useStore()
      const isAuthenticated = computed(() => store.getters.isAuthenticated)
      
      return {
        isAuthenticated
      }
    }
  }
  </script>