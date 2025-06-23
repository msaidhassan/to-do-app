<!-- ActiveTasks.vue -->
<template>
  <div class="active-tasks">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="mb-0">Active Tasks</h1>
      <button 
        class="btn btn-primary"
        @click="$emit('add-task')"
      >
        Add New Task
      </button>
    </div>

    <TaskFilter 
      :categories="categories" 
      @update-filters="handleFilterUpdate" 
    />

    <TaskList 
      :tasks="filteredTasks" 
      :is-trashed-view="false"
      :current-page="currentPage"
      :last-page="totalPages"
      @show-task="showTask" 
      @edit-task="editTask"
      @delete-task="deleteTask"
      @complete-task="completeTask"
      @bulk-delete="bulkDeleteTasks"
      @page-changed="changePage"
    />

    <PaginationControls
      :current-page="currentPage"
      :total-pages="totalPages"
      @page-changed="changePage"
    />
  </div>
   
</template>

<script>
import { mapActions } from 'vuex'
import TaskList from './TaskList.vue'
import TaskFilter from './TaskFilter.vue'
import PaginationControls from '../../PaginationControls.vue'

export default {
  components: { TaskList, TaskFilter,PaginationControls },
  props: ['categories'],
  computed: {
    currentPage() {
      return this.$store.getters.currentPage
    },
    totalPages() {
      return this.$store.getters.totalPages
    },
    filteredTasks() {
      return this.$store.state.activeTasks.data || []
    }
  },
  methods: {
    ...mapActions([
      'updateFilters',
      'changePage',
      'deleteTask',
      'bulkDeleteTasks',
    ]),
    handleFilterUpdate(filters) {
      this.updateFilters(filters)
    },
    
    showTask(task) {
      this.$emit('show-task', task)
    },
    editTask(task) {
      this.$emit('edit-task', task)
    },
    completeTask(task) {
      this.$emit('complete-task', task)
    },
    
    changePage(page) {
      console.log('Changing page to:', page);
    this.$store.dispatch('changePage', { page, type: 'active' });
  }
  }
}
</script>