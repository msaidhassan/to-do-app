<template>
  <div
    v-if="task"
    class="list-group-item"
    :class="{ 'opacity-75': isDeleted, 'bg-light': isSelected }"
     >
    <div class="d-flex justify-content-between align-items-center">
      <div class="form-check">
        <input
          :id="`task-${task.id}`"
          type="checkbox"
          class="form-check-input"
          :checked="isSelected"
          @click="handleCheckboxClick"

        >
        <label
          class="form-check-label"
          :for="`task-${task.id}`"
          :class="{ 'text-decoration-line-through': task.status === 'completed' }"
         

        >
          {{ task.title }}
        </label>
      </div>
      
      <div class="d-flex">
        <span
          class="badge rounded-pill me-2"
          :class="categoryClass"
        >
          {{ task.category?.name || 'Uncategorized' }}
        </span>
        
        <span
          class="badge rounded-pill me-2"
          :class="statusClass"
        >
          {{ task.status }}
        </span>
        
        <div class="btn-group">
           <button
            v-if="!isDeleted &&!bulkSelectMode"
            class="btn btn-sm btn-outline-primary"
            @click="$emit('show', task)"
          >
            Show
          </button>
          <button
            v-if="!isDeleted && !bulkSelectMode"
            class="btn btn-sm btn-outline-primary"
            @click="$emit('edit', task)"
          >
            Edit
          </button>
          
         
          <button
            v-if="isDeleted && !bulkSelectMode"
            class="btn btn-sm btn-outline-success"
            @click="$emit('restore', task.id)"
          >
            Restore
          </button>
           <button
            v-if="!bulkSelectMode"
            class="btn btn-sm btn-outline-danger"
            @click="confirmDelete(task.id)"
          >
            Delete
          </button>
          
        </div>
      </div>
    </div>
    
    <div class="mt-2">
      <p class="mb-1">
        {{ task.description }}
      </p>
      
      <div
        v-if="task.due_date"
        class="text-muted small"
      >
        <strong>Due:</strong> {{ formatDate(task.due_date) }}
      </div>
    </div>
  </div>
  <div
    v-else
    class="list-group-item"
  >
    <p class="text-muted mb-0">
      No task data available
    </p>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'TaskItem',
  props: {
    task: {
      type: Object,
      required: true,
          validator: (value) => value !== null && typeof value === 'object'

    },
    bulkSelectMode: {
      type: Boolean,
      default: false
    },
    isSelected: {
      type: Boolean,
      default: false
    }
  },
  emits: ['show','edit', 'delete', 'restore', 'select', 'deselect','shift-select'],
  setup(props, { emit }) {

     const confirmDelete= computed(() => taskId => {
    if (confirm('Are you sure you want to delete this task?')) {
      emit('delete', props.task.id)
    }
  } )
    const isDeleted = computed(() => props.task?.deleted_at)

    const statusClass = computed(() => {
      if (!props.task) return 'bg-secondary'
      switch (props.task.status) {
        case 'completed':
          return 'bg-success'
        case 'pending':
          return 'bg-warning'
        default:
          return 'bg-secondary'
      }
    })
    
    const categoryClass = computed(() => {
      if (!props.task?.category?.name) return 'bg-secondary'
      switch (props.task.category.name) {
        case 'Work':
          return 'bg-primary'
        case 'Personal':
          return 'bg-info'
        case 'Urgent':
          return 'bg-danger'
        default:
          return 'bg-secondary'
      }
    })
    
   const handleCheckboxClick = (event) => {
    //  if (isDeleted.value) return
      if (event.shiftKey) {
        emit('shift-select', props.task.id)
      } else if (event.target.checked) {
        
        emit('select', props.task.id)
      } else {
        emit('deselect', props.task.id)
      }
    }
    
    

    
    
    
    const formatDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleDateString()
    }
    
    return {
      isDeleted,
      statusClass,
      categoryClass,
      handleCheckboxClick,
      formatDate,
      confirmDelete
      
    }
  }
}
</script>
<style scoped>
.list-group-item {
  cursor: pointer;
  user-select: none; /* Prevent text selection during multi-select */
}
</style>