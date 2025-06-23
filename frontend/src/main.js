import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import axios from 'axios' // Optional if you want to use axios globally
//console.log('Base URL:', import.meta.env.VITE_BASE_URL)

const app = createApp(App)
app.use(router)
app.use(store)

// Optional axios global configuration
app.config.globalProperties.$axios = axios

app.mount('#app')