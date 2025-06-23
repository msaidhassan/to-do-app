<template>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">Register</div>
          <div class="card-body">
            <div v-if="error" class="alert alert-danger">{{ error }}</div>
            
            <form @submit.prevent="submitForm">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="name" 
                  v-model="form.name" 
                  required
                >
              </div>
              
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
                  v-model="form.password" 
                  required
                >
              </div>
              
              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input 
                  type="password" 
                  class="form-control" 
                  id="password_confirmation" 
                  v-model="form.password_confirmation" 
                  required
                >
              </div>
              
              <button type="submit" class="btn btn-primary" :disabled="loading">
                {{ loading ? 'Registering...' : 'Register' }}
              </button>
            </form>
            
            <div class="mt-3">
              <p>Already have an account? <router-link to="/login">Login</router-link></p>
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
    name: 'RegisterApp',
    setup() {
      const store = useStore()
      const router = useRouter()
      
      const form = ref({
        name: '',
        email: '',
        password: '',
        password_confirmation: ''
      })
      const loading = ref(false)
      const error = ref('')
      
      const submitForm = async () => {
        loading.value = true
        error.value = ''
        
        try {
          await store.dispatch('register', form.value)
          router.push('/dashboard')
        } catch (err) {
          if (err.response && err.response.data && err.response.data.message) {
            error.value = err.response.data.message
          } else if (err.response && err.response.data && err.response.data.errors) {
            const errorMessages = Object.values(err.response.data.errors).flat()
            error.value = errorMessages.join(', ')
          } else {
            error.value = 'An error occurred during registration. Please try again.'
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
  