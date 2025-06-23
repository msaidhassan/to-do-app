<template>
    <div class="dashboard">
        <h1>Your Tasks</h1>
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link" :class="{ active: activeTab === 'active' }" @click="activeTab = 'active'">
                    Active Tasks
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" :class="{ active: activeTab === 'trashed' }" @click="activeTab = 'trashed'">
                    Trash
                </button>
            </li>
        </ul>
        <!-- Add tabs navigation -->
        <div v-if="activeTab === 'active'">
            <ActiveTasks :categories="categories" @add-task="addNewTask" @show-task="showTask" @edit-task="editTask"
                @complete-task="completeTask" />
        </div>
        <div v-if="activeTab === 'trashed'">
            <TrashedTasks />
        </div>

        <div class="modal" tabindex="-1" :class="{ 'd-block': showTaskModal, 'bg-dark bg-opacity-50': showTaskModal }">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ currentMode === 'add' ? 'Add Task' : currentMode === 'edit' ?
                            'EditTask' : 'Task Details' }}</h5>
                        <button type="button" class="btn-close" @click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <TaskForm :task="currentTask" :categories="categories" :mode="currentMode" @submit="saveTask"
                            @cancel="closeModal" @delete="handleDelete" @edit-requested="switchToEditMode"
                            @category-added="handleCategoryAdded" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import TaskForm from '../components/Todo/TaskForm.vue'
import TrashedTasks from '../components/Todo/TrashedTasks.vue'
import ActiveTasks from '../components/Todo/ActiveTasks.vue'

export default {
    name: 'DashboardApp',
    components: {
        TaskForm,
        TrashedTasks,
        ActiveTasks,
    },
    setup() {
        const activeTab = ref('active') // 'active' or 'trashed'

        const store = useStore()

        // Refs
        const showAddTaskModal = ref(false)
        const editingTask = ref(null)
        const showTaskModal = ref(false)
        const currentTask = ref(null)
        const currentMode = ref('show')

        // Computed
        const allTasks = computed(() => store.state.activeTasks)
        const filteredTasks = computed(() => store.getters.filteredTasks)
        const categories = computed(() => store.state.categories)
        const currentPage = computed(() => store.state.currentPage)
        const totalPages = computed(() => store.state.totalPages)

       

        // Methods
        const showTask = async (task) => {
            if (categories.value.length === 0) {
                await store.dispatch('fetchCategories')
            }
            currentTask.value = task
            currentMode.value = 'show'
            showTaskModal.value = true
        }

        const editTask = (task) => {
            currentTask.value = task
            currentMode.value = 'edit'
            showTaskModal.value = true
        }

        const switchToEditMode = () => {
            currentMode.value = 'edit'
        }

        const addNewTask = () => {
              if (categories.value.length=== 0) {
                alert('Please wait while we load categories...')
                return
            }
            currentTask.value = null
            currentMode.value = 'add'
            showTaskModal.value = true
        }

        const closeModal = () => {
            showTaskModal.value = false
            currentTask.value = null
        }

        const handleDelete = (taskId) => {
            if (confirm('Are you sure you want to delete this task?')) {
                store.dispatch('deleteTask', taskId)
                closeModal()
            }
        }

        const deleteTask = (taskId) => {
            if (confirm('Are you sure you want to delete this task?')) {
                store.dispatch('deleteTask', taskId)
            }
        }

        const bulkDeleteTasks = async (taskIds) => {
            console.log('Bulk delete tasks:', taskIds)
            if (taskIds.length === 0) {
                alert('Please select at least one task to delete')
                return
            }

            if (confirm(`Are you sure you want to delete ${taskIds.length} selected tasks?`)) {
                try {
                    await store.dispatch('bulkDeleteTasks', taskIds)
                } catch (error) {
                    console.error('Bulk delete failed:', error)
                    alert('Failed to delete tasks. Please try again.')
                }
            }
        }

        const restoreTask = (taskId) => {
            store.dispatch('restoreTask', taskId)
        }

        const completeTask = (task) => {
            store.dispatch('updateTask', {
                id: task.id,
                data: { ...task, status: 'completed' }
            })
        }

        const saveTask = async (taskData) => {
            try {
                if (currentMode.value === 'edit' && currentTask.value?.id) {
                    // Update existing task
                    await store.dispatch('updateTask', {
                        id: currentTask.value.id,
                        data: taskData
                    })
                } else {
                    // Create new task
                    await store.dispatch('createTask', taskData)
                }
                closeModal()
            } catch (error) {
                console.error('Error saving task:', error)
            }
        }

        const handleFilterUpdate = (filters) => {
            store.dispatch('updateFilters', {
                search: filters.search,
                status: filters.status,
                category: filters.category
            })
        }

        const handleCategoryAdded = (newCategory) => {
            store.dispatch('addCategory', newCategory)
        }
        const changePage = (page) => {
            if (page < 1 || page > totalPages.value) return
            store.dispatch('changePage', page)
        }

        // Lifecycle hooks
        onMounted(async () => {
            try {
                await store.dispatch('fetchCategories')
                await store.dispatch('fetchTasks')
                await store.dispatch('fetchTrashedTasks')

                //  console.log('Dashboard tasks:', allTasks.value)
            } catch (error) {
                console.error('Initialization error:', error)
            }
        })

        return {
            // Refs
            showAddTaskModal,
            showTaskModal,
            currentTask,
            currentMode,
            editingTask,

            // Computed
            filteredTasks,
            categories,
            currentPage,
            totalPages,
            allTasks,


            // Methods
            showTask,
            editTask,
            deleteTask,
            bulkDeleteTasks,
            restoreTask,
            completeTask,
            saveTask,
            closeModal,
            handleDelete,
            switchToEditMode,
            addNewTask,
            handleFilterUpdate,
            changePage,
            handleCategoryAdded,
            activeTab,
        }
    }
}
</script>