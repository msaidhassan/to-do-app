import axios from 'axios'

const api = axios.create({
    baseURL: 'http://localhost:8000/api',
    withCredentials: true, // This is crucial
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    }
})

// Ensure CSRF token is properly set before each request
api.interceptors.request.use(async (config) => {
    // Only get new CSRF token if we don't have one
    if (!document.cookie.match(/XSRF-TOKEN=[^;]+/)) {
        await axios.get('http://localhost:8000/sanctum/csrf-cookie', {
            withCredentials: true
        })
    }
    
    // Get token from cookies
    const token = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1]
        
    if (token) {
        config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token)
    }
    
    // Add auth token if exists
    const authToken = localStorage.getItem('token')
    if (authToken) {
        config.headers['Authorization'] = `Bearer ${authToken}`
    }
    
    return config
}, error => {
    return Promise.reject(error)
})

export default api