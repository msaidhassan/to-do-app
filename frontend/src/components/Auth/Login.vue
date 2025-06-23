<template>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Login</div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            
            <form @submit.prevent="submitForm">
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input 
                  type="email" 
                  class="form-control" 
                  id="email" 
                  v-model="form.email" 
                  required
                >
              </div>
              
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password" 
                  autocomplete="current-password"
                  v-model="form.password" 
                  required
                >
              </div>
              
              <button type="submit" class="btn btn-primary" :disabled="loading">
                {{ loading ? 'Logging in...' : 'Login' }}
              </button>
            </form>
            
            <div class="mt-3">
              <p>Don't have an account? <router-link to="/register">Register</router-link></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { ref } from 'vue'
  import { useStore } from 'vuex'
  import { useRouter } from 'vue-router'
  
  export default {
    name: 'LoginApp',
    setup() {
      const store = useStore()
      const router = useRouter()
      
      const form = ref({
        email: '',
        password: ''
      })
      const loading = ref(false)
      const error = ref('')
      
      const submitForm = async () => {
        loading.value = true
        error.value = ''
        
        try {
          await store.dispatch('login', form.value)
          router.push('/dashboard')
        } catch (err) {
          if (err.response && err.response.data && err.response.data.message) {
            error.value = err.response.data.message
          } else {
            error.value = 'An error occurred during login. Please try again.'
          }
        } finally {
          loading.value = false
        }
      }
      
      return {
        form,
        loading,
        error,
        submitForm
      }
    }
  }
  </script>