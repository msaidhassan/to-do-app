import { createStore } from 'vuex'
import api from '../services/api'
import axios from 'axios'

export default createStore({
  state: {
    user: (() => {
      const user = localStorage.getItem('user');
      try {
        return user ? JSON.parse(user) : null;
      } catch {
        return null;
      }
    })(),
    token: localStorage.getItem('token') || null,
    categories: [],
    filters: {
      status: 'all',
      category: 'all',
      search: ''
    },
    activeTasks: [],
    activePagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0
    },
    trashedTasks: [],
    trashedPagination: {
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0
    }
  },
  getters: {
    isAuthenticated: state => !!state.token,
    // Simplified getter that just returns tasks from API directly

    // Optional: Keep basic getters for UI state
    currentFilters: state => state.filters,
    hasActiveFilters: state => {
      return state.filters.status !== 'all' ||
        state.filters.category !== 'all' ||
        state.filters.search !== ''
    },
    // For active tasks
     currentPage: state => state.activePagination.current_page || 1,
     totalPages: state => state.activePagination.last_page || 1,

    // For trashed tasks
    trashedTasks: state => state.trashedTasks,
    trashedCurrentPage: state => state.trashedPagination.current_page,
    trashedTotalPages: state => state.trashedPagination.last_page,

      filteredActiveTasks: state => state.activeTasks.data || [],
  filteredTrashedTasks: state => state.trashedTasks.data || [],
  filteredTasks: (state, getters) => {
    return getters.filteredActiveTasks // Default, or implement logic to switch
  }
  },
  mutations: {
    SET_USER(state, user) {
      state.user = user
      localStorage.setItem('user', JSON.stringify(user))
    },
    SET_TOKEN(state, token) {
      state.token = token
      localStorage.setItem('token', token)
    },
    CLEAR_AUTH(state) {
      state.user = null
      state.token = null
      localStorage.removeItem('user')
      localStorage.removeItem('token')
    },
    SET_TASKS(state, tasks) {
      // Ensure tasks is always an array
      state.tasks = Array.isArray(tasks) ? tasks : [];
    },
    SET_CATEGORIES(state, categories) {
      state.categories = categories
    },
    ADD_TASK(state, task) {
      // Only add to local state if it matches current filters
      // or refresh from API instead
      state.tasks.unshift(task)
    },
    UPDATE_TASK(state, updatedTask) {
      const index = state.tasks.findIndex(task => task.id === updatedTask.id)
      if (index !== -1) {
        state.tasks.splice(index, 1, updatedTask)
      }
    },
    DELETE_TASK(state, taskId) {
      state.activeTasks.data = state.activeTasks.data.filter(task => task.id !== taskId)
          state.activePagination.total -= 1;

    },
        DELETE_TASKS(state, taskIds) {
      state.activeTasks.data = state.activeTasks.data.filter(task => !taskIds.includes(task.id));
      state.activePagination.total -= taskIds.length;

    },
    SET_FILTER(state, { type, value }) {
      state.filters[type] = value
    },
    SET_SEARCH_QUERY(state, query) {
      state.filters.search = query
    },
    SET_PAGINATION(state, { currentPage, totalPages }) {
      state.currentPage = currentPage
      state.totalPages = totalPages
    },
    SET_LOADING(state, isLoading) {
      state.loading = isLoading
    },
    SET_ERROR(state, error) {
      state.error = error
    },
    SET_FILTERS(state, filters) {
      state.filters = { ...state.filters, ...filters }
    },
    
    ADD_CATEGORY(state, category) {
      state.categories.push(category);
    },
    CLEAR_FILTERS(state) {
      state.filters = {
        status: 'all',
        category: 'all',
        search: ''
      }
    },
    SET_ACTIVE_TASKS(state, tasks) {
      state.activeTasks = tasks;
    },
    SET_ACTIVE_PAGINATION(state, pagination) {
      state.activePagination = pagination;
    },
    SET_TRASHED_TASKS(state, tasks) {
      state.trashedTasks = tasks;
    },
    SET_TRASHED_PAGINATION(state, pagination) {
      state.trashedPagination = pagination;
    },

    REMOVE_TRASHED_TASK(state, taskId) {
      state.trashedTasks.data = state.trashedTasks.data.filter(task => task.id !== taskId);
          state.trashedPagination.total -= 1;
   
    },
      
    REMOVE_TRASHED_TASKS(state, taskIds) {
      state.trashedTasks.data = state.trashedTasks.data.filter(task => !taskIds.includes(task.id));
      state.trashedPagination.total -= taskIds.length;

    },

    CLEAR_TRASHED_TASKS(state) {
      state.trashedTasks = [];
      state.trashedPagination = {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0
      };
    }
  },
  actions: {
    async login({ commit }, credentials) {
      try {
        const response = await api.post('/login', credentials);
        commit('SET_USER', response.data.data.user);
        commit('SET_TOKEN', response.data.data.token);
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.data.token}`;
        return response;
      } catch (error) {
        console.error('Login error:', error);
        throw error;
      }
    },

    async register({  commit }, userData) {
      try {
        const response = await api.post('/register', userData)
        commit('SET_USER', response.data.user)
        commit('SET_TOKEN', response.data.token)
        api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
        return response
      } catch (error) {
        throw error
      }
    },

    logout({ commit }) {
      commit('CLEAR_AUTH')
      delete api.defaults.headers.common['Authorization']
    },

    // Simplified filter update that immediately fetches from API
    async updateFilters({ commit, dispatch,state }, filters) {
      // Update filters in state
      state.activePagination.current_page=  1;

      Object.keys(filters).forEach(key => {
        if (key === 'search') {
          commit('SET_SEARCH_QUERY', filters[key])
        } else {
          commit('SET_FILTER', { type: key, value: filters[key] })
        }
      })

      // Reset to first page when filters change

      // Cancel any pending requests
      if (this.cancelToken) {
        this.cancelToken.cancel()
      }

      // Create new cancel token
      this.cancelToken = axios.CancelToken.source()

      // Fetch tasks with new filters (with debouncing for search)
      if (filters.search !== undefined) {
        // Debounce search requests
        clearTimeout(this.filterTimeout)
        this.filterTimeout = setTimeout(() => {
          dispatch('fetchTasks', { cancelToken: this.cancelToken.token })
        }, 400)
      } else {
        // Immediate fetch for status/category changes
        dispatch('fetchTasks', { cancelToken: this.cancelToken.token })
      }
    },

    async fetchTasks({ commit, state }, { cancelToken } = {}) {
      commit('SET_LOADING', true)
      commit('SET_ERROR', null)

      try {
        // Build API parameters from current filters
        const params = {
              page: state.activePagination.current_page,
      per_page: state.activePagination.per_page
    }

        // Add filters only if they're not 'all'
        if (state.filters.status !== 'all') {
          params.status = state.filters.status
        }

        if (state.filters.category !== 'all') {
          params.category_id = state.filters.category
        }

        if (state.filters.search && state.filters.search.trim() !== '') {
          params.search = state.filters.search.trim()
        }

        //console.log('Fetching tasks with API params:', params)

        const response = await api.get('/tasks', { params, cancelToken })

        // Set tasks directly from API response
    commit('SET_ACTIVE_TASKS', response.data.data);

        commit('SET_ACTIVE_PAGINATION', {
      current_page: Number(response.data.data.current_page),
      last_page: Number(response.data.data.last_page),
      per_page: Number(response.data.data.per_page),
      total: Number(response.data.data.total)
    });

     //   console.log('Tasks loaded from API:', response.data.data?.data?.length || 0, 'items')

      } catch (error) {
        if (!axios.isCancel(error)) {
          console.error('Error fetching tasks:', error)
          commit('SET_ERROR', error.response?.data?.message || 'Failed to load tasks')
          commit('SET_TASKS', [])
        }
      } finally {
        commit('SET_LOADING', false)
      }
    },

    async fetchCategories({ commit }) {
      try {
        const response = await api.get('/categories')
        commit('SET_CATEGORIES', response.data.data)
        return response.data.data
      } catch (error) {
        console.error('Error fetching categories:', error)
        throw error
      }
    },

    async createTask({  dispatch }, taskData) {
      try {
        const response = await api.post('/tasks', taskData)
        // Refresh tasks from API instead of just adding to local state
        // This ensures the new task appears correctly with current filters
        dispatch('fetchTasks')
        return response
      } catch (error) {
        throw error
      }
    },

    async updateTask({  dispatch }, { id, data }) {
      try {
        const response = await api.put(`/tasks/${id}`, data)
        // Refresh tasks from API to ensure consistency
        dispatch('fetchTasks')
        return response
      } catch (error) {
        throw error
      }
    },

    async deleteTask({  commit, dispatch}, id) {
      try {
        const response = await api.delete(`/tasks/${id}`)
        if (!response.data.success) {
          throw new Error(response.data.message)
        }
        // Refresh tasks from API
        commit('DELETE_TASK', id)  
        await dispatch('fetchTrashedTasks')
      } catch (error) {
        throw error
      }
    },
    async bulkDeleteTasks({ commit, dispatch }, taskIds) {
      try {
        commit('SET_LOADING', true)
        const response = await api.delete('/tasks/bulk', {
          data: { task_ids: taskIds }
        })

        if (!response.data.success) {
          throw new Error(response.data.message)
        }

        // Refresh tasks from API
       // await dispatch('fetchTasks')
        commit('DELETE_TASKS', taskIds)  
        await dispatch('fetchTrashedTasks')

        return response
      } catch (error) {
        commit('SET_ERROR', error.response?.data?.message || 'Failed to delete tasks')
        throw error
      } finally {
        commit('SET_LOADING', false)
      }
    },
 async updateTrashedFilters({ commit, dispatch,state }, filters) {
    // Update trashed-specific filters
    state.trashedPagination.current_page = 1; // Reset to first page
    Object.keys(filters).forEach(key => {
      if (key === 'search') {
        commit('SET_SEARCH_QUERY', filters[key] );
      } else {
        commit('SET_FILTER', { type: key, value: filters[key] });
      }
    });
    
    // Reset to first page when filters change
    commit('SET_TRASHED_PAGINATION', { currentPage: 1 });
    
    // Fetch with new filters
    await dispatch('fetchTrashedTasks');
  },
    async fetchTrashedTasks({ commit,state }) {
      commit('SET_LOADING', true);
      try {
        const params = {
          page:state.trashedPagination.current_page,
          per_page: state.trashedPagination.per_page
        };
        if (state.filters.status !== 'all') {
          params.status = state.filters.status
        }

        if (state.filters.category !== 'all') {
          params.category_id = state.filters.category
        }

        if (state.filters.search && state.filters.search.trim() !== '') {
          params.search = state.filters.search.trim()
        }

        const response = await api.get('/trashed/tasks', {
          params
        });

        // Make sure we're accessing the data correctly
         commit('SET_TRASHED_TASKS', response.data.data);
    commit('SET_TRASHED_PAGINATION', {
      current_page: response.data.data.current_page,
      last_page: response.data.data.last_page,
      per_page: response.data.data.per_page,
      total: response.data.data.total
    });

        //return tasks;
      } catch (error) {
        commit('SET_ERROR', error.response?.data?.message || 'Failed to load trashed tasks');
        throw error;
      } finally {
        commit('SET_LOADING', false);
      }
    },

    // Search trashed tasks
    async searchTrashedTasks({ commit }, searchQuery) {
      try {
        const response = await api.get('/trashed/tasks/search', {
          params: { search: searchQuery }
        });
        commit('SET_TRASHED_TASKS', response.data.data);
        return response.data;
      } catch (error) {
        console.error('Error searching trashed tasks:', error);
        throw error;
      }
    },

    // Restore single task
    async restoreTask({ commit,dispatch }, taskId) {
      try {
        await api.post(`/trashed/tasks/${taskId}/restore`);
        commit('REMOVE_TRASHED_TASK', taskId);
        await dispatch('fetchTasks'); // Refresh active tasks
        return true;
      } catch (error) {
        console.error('Error restoring task:', error);
        throw error;
      }
    },

    // Bulk restore tasks
    async bulkRestoreTasks({ commit, dispatch }, taskIds) {
      try {
        await api.post('/trashed/tasks/bulk/restore', { task_ids: taskIds });
        commit('REMOVE_TRASHED_TASKS', taskIds);
        await dispatch('fetchTasks'); // Refresh active tasks
        return true;
      } catch (error) {
        console.error('Error bulk restoring tasks:', error);
        throw error;
      }
    },

    // Restore all trashed tasks
    async restoreAllTasks({ commit, dispatch }) {
      try {
        console.log('Restoring all trashed tasks...');
        await api.post('/trashed/tasks/restore-all');
        commit('SET_TRASHED_TASKS', []);
        await dispatch('fetchTasks'); // Refresh active tasks
        return true;
      } catch (error) {
        console.error('Error restoring all tasks:', error);
        throw error;
      }
    },

    // Permanently delete single task
    async forceDeleteTask({ commit }, taskId) {
      try {
        await api.delete(`/trashed/tasks/${taskId}`);
        commit('REMOVE_TRASHED_TASK', taskId);
        return true;
      } catch (error) {
        console.error('Error permanently deleting task:', error);
        throw error;
      }
    },

    // Bulk permanently delete tasks
    async bulkForceDeleteTasks({ commit }, taskIds) {
      try {
        await api.delete('/trashed/tasks/bulk', {
          data: { task_ids: taskIds }
        });
        commit('REMOVE_TRASHED_TASKS', taskIds);
        return true;
      } catch (error) {
        console.error('Error bulk permanently deleting tasks:', error);
        throw error;
      }
    },

    // Permanently delete all trashed tasks
    async forceDeleteAllTasks({ commit }) {
      try {
        await api.delete('/trashed/tasks');
        commit('SET_TRASHED_TASKS', []);
        return true;
      } catch (error) {
        console.error('Error permanently deleting all tasks:', error);
        throw error;
      }
    },

    // Get single trashed task details
    async fetchTrashedTaskDetails( taskId) {
      try {
        const response = await api.get(`/trashed/tasks/${taskId}`);
        return response.data;
      } catch (error) {
        console.error('Error fetching trashed task details:', error);
        throw error;
      }
    },

    async createCategory({ commit }, categoryData) {
      try {
        const response = await api.post('/categories', categoryData);
        commit('ADD_CATEGORY', response.data.data);
        return response.data.data;
      } catch (error) {
        console.error('Error creating category:', error);
        throw error;
      }
    },

    // Simplified filter actions
    async updateFilter({ dispatch }, { type, value }) {
      dispatch('updateFilters', { [type]: value })
    },

    async updateSearchQuery({ dispatch }, query) {
      dispatch('updateFilters', { search: query })
    },

    changePage({ state,dispatch }, { page, type = 'active' }) {
      console.log('Changing page:', page, 'for type:', type);
      if (type === 'active') {
              state.activePagination.current_page=  page;
        dispatch('fetchTasks');
      } else {
        state.trashedPagination.current_page = page;
        dispatch('fetchTrashedTasks');

      }
    },

    async clearFilters({ commit, dispatch }) {
      commit('CLEAR_FILTERS')
      dispatch('fetchTasks')
    }
  }
})