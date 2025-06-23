<template>
  <div class="task-filter mb-4">
    <div class="row g-3">
      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text">Search</span>
          <input type="text" class="form-control" placeholder="Search tasks..." v-model="searchQuery"
            @input="debounceSearch">
        </div>
      </div>

      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text">Status</span>
          <select class="form-select" v-model="statusFilter" @change="debounceFilters">
            <option value="all">All</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="in_progress">in_progress</option>
            <option value="overdue">overdue</option>

          </select>
        </div>
      </div>

      <div class="col-md-4">
        <div class="input-group">
          <span class="input-group-text">Category</span>
          <select v-model="categoryFilter" @change="debounceFilters">
            <option value="all">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-secondary w-100 h-100" @click="resetFilters">
          <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'TaskFilter',
  props: {
    categories: {
      type: Array,
      required: true
    }
  },
  emits: ['update-filters'],
  setup(props, { emit }) {
    const searchQuery = ref('')
    const statusFilter = ref('all')
    const categoryFilter = ref('all')
    // let searchTimeout = null
    // let filterTimeout = null
    let debounceTimer = null

    // const updateFilters = () => {
    //   emit('update-filter', { type: 'status', value: statusFilter.value })
    //   emit('update-filter', { type: 'category', value: categoryFilter.value })
    // }
    // const debounceFilters = () => {
    //   clearTimeout(filterTimeout)
    //   filterTimeout = setTimeout(updateFilters, 300)
    // }

    // const debounceSearch = () => {
    //   clearTimeout(searchTimeout)
    //   searchTimeout = setTimeout(() => {
    //     emit('update-search', searchQuery.value)
    //   }, 1000)
    // }
    const debounceUpdate = () => {
    //  console.log('Debounce update called')
      clearTimeout(debounceTimer)
      debounceTimer = setTimeout(() => {
        emit('update-filters', {
          search: searchQuery.value,
          status: statusFilter.value,
          category: categoryFilter.value
        })
      }, 2000) // Slightly longer debounce
    }
  const resetFilters = () => {
      searchQuery.value = ''
      statusFilter.value = 'all'
      categoryFilter.value = 'all'
      // Emit immediately when resetting
      emit('update-filters', {
        search: '',
        status: 'all',
        category: 'all'
      })
    }

    return {
      searchQuery,
      statusFilter,
      categoryFilter,
      // updateFilters,
      // debounceFilters,
      // debounceSearch,
      debounceSearch: debounceUpdate,
      debounceFilters: debounceUpdate,
      resetFilters
    }
  }
}
</script>