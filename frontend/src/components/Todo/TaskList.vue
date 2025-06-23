  <template>
    <div class="task-list">
      <div class="d-flex justify-content-between mb-3">
        <div>
          <button v-if="selectedTasks.length === 0" @click="startBulkSelect" class="btn btn-outline-secondary me-2">
            Select Tasks
          </button>

          <button v-if="selectedTasks.length > 0" @click="selectAllOnPage" class="btn btn-outline-primary me-2">
            Select All on Page
          </button>
        </div>

        <div v-if="selectedTasks.length > 0">
          <button v-if="isTrashedView" @click="bulkRestoreTasks" class="btn btn-success me-2"
            :disabled="selectedTasks.length === 0">
            Restore Selected ({{ selectedTasks.length }})
          </button>
          <button @click="bulkDeleteTasks" class="btn btn-danger me-2" :disabled="selectedTasks.length === 0">
            Delete Selected ({{ selectedTasks.length }})
          </button>
          <button @click="cancelBulkSelect" class="btn btn-outline-secondary">
            Cancel
          </button>
        </div>
      </div>

      <div v-if="loading" class="alert alert-info">
        Loading tasks...
      </div>
      <div v-else-if="filteredTasks.length === 0" class="alert alert-info">
        No tasks found matching your criteria.
      </div>
      <div v-else class="list-group">
        <transition-group name="fade" tag="div" class="list-group">

          <TaskItem v-for="task in filteredTasks" :key="task.id" :task="task"
            :bulk-select-mode="selectedTasks.length > 0" :is-selected="selectedTasks.includes(task.id)"
            @show="$emit('show-task', task)" @edit="$emit('edit-task', task)" @delete="$emit('delete-task', task.id)"
            @restore="$emit('restore-task', task.id)" @complete="$emit('complete-task', task)" @select="selectTask"
            @deselect="deselectTask" @shift-select="handleShiftSelect" />
        </transition-group>

      </div>
    </div>
  </template>

<script>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useStore } from 'vuex'
import TaskItem from './TaskItem.vue'

export default {
  name: 'TaskList',
  components: {
    TaskItem
  },
  props: {
    tasks: {
      type: Array,
      required: true,
      default: () => []
    },
    isTrashedView: {
      type: Boolean,
      default: false
    },
    currentPage: {
      type: Number,
      default: 1
    },
    lastPage: {
      type: Number,
      default: 1
    }
  },
  emits: [
    'show-task',
    'edit-task',
    'delete-task',
    'restore-task',
    'complete-task',
    'bulk-delete'
  ],
  setup(props, { emit }) {
    const store = useStore()
    const loading = computed(() => store.state.loading)
    const filteredTasks = computed(() => {
      // Ensure tasks is an array and filter out null/undefined
      // const tasksArray = Array.isArray(props.tasks) ? props.tasks : 
      //                 (props.tasks?.data || []);
      // return tasksArray.filter(task => task != null)
      return props.tasks?.filter(task => task != null) || []

    })
    const selectedTasks = ref([])
    const lastSelectedIndex = ref(null)
    const shiftPressed = ref(false)

    // Track Shift key state
    const handleKeyDown = (e) => {
      if (e.key === 'Shift') shiftPressed.value = true

    }

    const handleKeyUp = (e) => {
      if (e.key === 'Shift') shiftPressed.value = false

    }

    onMounted(() => {
      window.addEventListener('keydown', handleKeyDown)
      window.addEventListener('keyup', handleKeyUp)
    })

    onUnmounted(() => {
      window.removeEventListener('keydown', handleKeyDown)
      window.removeEventListener('keyup', handleKeyUp)
    })
    const startBulkSelect = () => {
      selectedTasks.value = []
      lastSelectedIndex.value = null

    }

    const cancelBulkSelect = () => {
      selectedTasks.value = []
      lastSelectedIndex.value = null

    }

    const selectTask = (taskId) => {
      if (!selectedTasks.value.includes(taskId)) {
        selectedTasks.value.push(taskId)
        lastSelectedIndex.value = props.tasks.findIndex(t => t.id === taskId)

      }
    }

    const deselectTask = (taskId) => {
      selectedTasks.value = selectedTasks.value.filter(id => id !== taskId)
    }
    const handleShiftSelect = (taskId) => {
      const currentIndex = props.tasks.findIndex(task => task.id === taskId)

      if (lastSelectedIndex.value === null) {
        selectTask(taskId)
        return
      }

      const start = Math.min(lastSelectedIndex.value, currentIndex)
      const end = Math.max(lastSelectedIndex.value, currentIndex)

      const tasksToSelect = props.tasks.slice(start, end + 1)
      tasksToSelect.forEach(task => {
        if (!selectedTasks.value.includes(task.id)) {
          selectTask(task.id)
        }
      })

      lastSelectedIndex.value = currentIndex
    }

    const selectAllOnPage = () => {
      const pageTaskIds = props.tasks.map(task => task.id)
      // Add only those not already selected
      const newSelections = pageTaskIds.filter(id => !selectedTasks.value.includes(id))
      selectedTasks.value = [...selectedTasks.value, ...newSelections]
      lastSelectedIndex.value = props.tasks.length - 1

    }
    const bulkRestoreTasks = async () => {
      if (selectedTasks.value.length === 0) {
        alert('Please select at least one task to restore')
        return
      }

      if (confirm(`Are you sure you want to restore ${selectedTasks.value.length} selected tasks?`)) {
        try {
          emit('bulk-restore', [...selectedTasks.value]) // Note: Changed to emit restore-task
          if (props.tasks.length === selectedTasks.value.length) {
            console.log(props.currentPage)

            if (props.currentPage > 1 && props.lastPage === props.currentPage) {
              emit('page-changed', props.currentPage - 1)
            } else if(props.lastPage > 1) {
              emit('page-changed', props.currentPage)
            }
          }
           selectedTasks.value = []
          lastSelectedIndex.value = null
        } catch (error) {
          console.error('Bulk restore failed:', error)
          alert('Failed to restore tasks. Please try again.')
        }
      }
    }
    const bulkDeleteTasks = async () => {
      if (selectedTasks.value.length === 0) {
        alert('Please select at least one task to delete')
        return
      }

      if (confirm(`Are you sure you want to delete ${selectedTasks.value.length} selected tasks?`)) {
        try {
          emit('bulk-delete', [...selectedTasks.value])

          console.log('Bulk delete tasks:', props.tasks.length, selectedTasks.value.length)
          if (props.tasks.length === selectedTasks.value.length) {
            console.log(props.currentPage)

            if (props.currentPage > 1 && props.lastPage === props.currentPage) {
              emit('page-changed', props.currentPage - 1)
            } else if(props.lastPage > 1) {
              emit('page-changed', props.currentPage)
            }
          }
          selectedTasks.value = []
          lastSelectedIndex.value = null
        } catch (error) {
          console.error('Bulk delete failed:', error)
          alert('Failed to delete tasks. Please try again.')
        }
      }
    }
    const handleRestore = (taskId) => {
      emit('restore-task', taskId)
    }
    return {
      loading,
      filteredTasks,
      selectedTasks,
      startBulkSelect,
      cancelBulkSelect,
      selectTask,
      deselectTask,
      handleShiftSelect,
      selectAllOnPage,
      bulkDeleteTasks,
      shiftPressed,
      handleRestore,
      bulkRestoreTasks
    }
  }
}
</script>

<style scoped>
.task-list {
  margin-top: 20px;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>