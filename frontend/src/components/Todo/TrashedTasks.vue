<template>
  <div class="trashed-tasks">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="mb-0">Trash</h1>
      <div>
        <button class="btn btn-outline-success me-2" @click="restoreallTasks"
          :disabled="loading || filteredTrashedTasks.length === 0">
          Restore All
        </button>
        <button class="btn btn-outline-danger" @click="confirmDeleteAll"
          :disabled="loading || filteredTrashedTasks.length === 0">
          Empty Trash
        </button>
      </div>
    </div>

    <TaskFilter :categories="categories" @update-filters="handleFilterUpdate" />

    <!-- <div class="d-flex justify-content-between mb-3">
      <div>
        <button v-if="selectedTasks.length > 0" @click="bulkRestoreTasks" class="btn btn-success me-2"
          :disabled="loading">
          Restore Selected ({{ selectedTasks.length }})
        </button>
        <button v-if="selectedTasks.length > 0" @click="confirmBulkDelete" class="btn btn-danger" :disabled="loading">
          Delete Selected ({{ selectedTasks.length }})
        </button>
      </div>
    </div> -->

    <TaskList :tasks="filteredTrashedTasks" :is-trashed-view="true" :current-page="currentPage"
      :last-page="totalPages" @show-task="showTaskDetails" @restore-task="restoretask" @delete-task="confirmDelete"
      @bulk-delete="confirmBulkDelete" @bulk-restore="bulkRestoretasks" @page-changed="changePage" />

    <PaginationControls :current-page="currentPage" :total-pages="totalPages" @page-changed="changePage" />

    <!-- Task Details Modal -->
    <div class="modal fade" id="taskDetailsModal" tabindex="-1" aria-hidden="true">
      <!-- Modal content remains the same -->
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import TaskList from './TaskList.vue'
import TaskFilter from './TaskFilter.vue'
import PaginationControls from '../../PaginationControls.vue'

export default {
  name: 'TrashedTasks',
  components: {
    TaskList,
    TaskFilter,
    PaginationControls
  },
  data() {
    return {
      selectedTask: null,
      searchQuery: '',
      sortField: 'deleted_at',
      sortDirection: 'desc',
    }
  },
  computed: {
    ...mapState({
      loading: state => state.loading,
      error: state => state.error,
      categories: state => state.categories
    }),
    ...mapGetters([
      'trashedTasks',
      'currentPage',
      'totalPages',
      'filteredTrashedTasks'
    ]),

    currentPage() {
      console.log('Current page:', this.$store.state.trashedPagination.current_page)
      return this.$store.state.trashedPagination.current_page || 1
    },
    totalPages() {
      return this.$store.state.trashedPagination.last_page || 1
    },
  },
  methods: {
    ...mapActions([
      'fetchTrashedTasks',
      'restoreTask',
      'bulkRestoreTasks',
      'restoreAllTasks',
      'forceDeleteTask',
      'bulkForceDeleteTasks',
      'forceDeleteAllTasks',
      'updateTrashedFilters',
      'changePage'
    ]),
    changePage(page) {
      console.log('Changing page to:', page)
      this.$store.dispatch('changePage', { page, type: 'trashed' });

    },


    handleFilterUpdate(filters) {
      this.updateTrashedFilters(filters)
    },

    async restoretask(taskId) {
      try {
        console.log('Restoring task:', taskId)
        await this.restoreTask(taskId)
        this.showToast('Task restored successfully', 'success')
      } catch (error) {
        this.showToast('Failed to restore task', 'danger')
      }
    },

    async bulkRestoretasks(taskIds) {
      try {
        console.log('Restoring tasks:', taskIds)
        await this.bulkRestoreTasks(taskIds)
        this.showToast(`${taskIds.length} tasks restored`, 'success')
      } catch (error) {
        this.showToast('Failed to restore tasks', 'danger')
      }
    },

    async restoreallTasks() {
      try {
        if (confirm('Are you sure you want to restore ALL trashed tasks?')) {
          await this.restoreAllTasks()
          this.showToast('All tasks restored', 'success')
          this.changePage(1);
        }
      } catch (error) {
        this.showToast('Failed to restore all tasks', 'danger')
      }
    },

    confirmDelete(taskId) {
      if (confirm('Are you sure you want to permanently delete this task? This cannot be undone.')) {
        this.deleteTask(taskId)
      }
    },

    async deleteTask(taskId) {
      this.loading = true;

      try {
        await this.forceDeleteTask(taskId)
        this.showToast('Task permanently deleted', 'success')
        // await this.loadTasks();
        // if (this.filteredTrashedTasks.length === 0 && this.currentPage > 1) {
        //  this.changePage(this.currentPage - 1);
        //    }
      } catch (error) {
        this.showToast('Failed to delete task', 'danger')
      }
    },

    confirmBulkDelete(taskIds) {
      if (confirm(`Are you sure you want to permanently delete ${taskIds.length} selected tasks? This cannot be undone.`)) {
        this.bulkDeleteTasks(taskIds)
      }
    },

    async bulkDeleteTasks(taskIds) {
      try {
        await this.bulkForceDeleteTasks(taskIds)
        this.showToast(`${taskIds.length} tasks permanently deleted`, 'success')
        //  await this.loadTasks();

        // Handle empty page after deletion
        if (this.filteredTrashedTasks.length === 0 && this.currentPage > 1) {
          this.changePage(this.currentPage - 1);
        }
      } catch (error) {
        this.showToast('Failed to delete tasks', 'danger')
      }
    },

    confirmDeleteAll() {
      if (confirm('Are you sure you want to permanently delete ALL trashed tasks? This cannot be undone.')) {
        this.deleteAllTasks()
      }
    },

    async deleteAllTasks() {
      try {
        await this.forceDeleteAllTasks()
        this.showToast('All trashed tasks permanently deleted', 'success')
        this.changePage(1);

      } catch (error) {
        this.showToast('Failed to empty trash', 'danger')
      }
    },

    showTaskDetails(task) {
      this.selectedTask = task
      // Show modal logic here
    },

    showToast(message, type = 'success') {
      this.$root.$emit('show-toast', { type, message })
    }
  },

}
</script>

<style scoped>
.trashed-tasks {
  padding: 20px;
}
</style>