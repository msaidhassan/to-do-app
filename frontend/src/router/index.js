import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Dashboard from '../views/Dashboard.vue'
import Login from '../components/Auth/Login.vue'
import Register from '../components/Auth/Register.vue'
import NotFound from '../views/NotFound.vue'
//import Tasks from '../components/Tasks.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/register',
    name: 'Register',
    component: Register
  },
  // {
  //   path: '/tasks',
  //   name: 'Tasks',
  //   component: Tasks,
  //   meta: { requiresAuth: true }
  // },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.VITE_APP_BASE_URL|| '/'),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  const isAuthenticated = token && token !== 'undefined';

  if (to.matched.some(record => record.meta.requiresAuth) && !isAuthenticated) {
    next('/login');
  } else {
    next();
  }
})

export default router
