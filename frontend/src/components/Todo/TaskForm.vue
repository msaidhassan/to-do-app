<template>
  <form @submit.prevent="submitForm">
         <!-- Mode Indicator -->
    <div class="mb-3 d-flex justify-content-between align-items-center">
      <h5 class="modal-title mb-0">
        {{ mode === 'add' ? 'Add New Task' : mode === 'edit' ? 'Edit Task' : 'Task Details' }}
      </h5>
      <button 
        v-if="mode === 'show'"
        type="button" 
        class="btn btn-sm btn-primary"
        @click="switchToEditMode"
      >
        Edit
      </button>
    </div>
<div class="mb-3">
      <label for="title" class="form-label">Title</label>
      <input 
        v-if="mode !== 'show'"
        type="text" 
        class="form-control" 
        id="title" 
        v-model="form.title" 
        required
      >
      <div v-else class="form-control-plaintext">{{ form.title }}</div>
      <div v-if="errors.title" class="text-danger">{{ errors.title }}</div>
    </div>

    <!-- Description Field -->
    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea 
        v-if="mode !== 'show'"
        class="form-control" 
        id="description" 
        v-model="form.description" 
        rows="3"
      ></textarea>
      <div v-else class="form-control-plaintext">{{ form.description || 'No description' }}</div>
      <div v-if="errors.description" class="text-danger">{{ errors.description }}</div>
    </div>

    <!-- Category Field -->
    <div class="mb-3">
      <div class="d-flex justify-content-between align-items-center">
        <label for="category" class="form-label">Category</label>
          <button 
          v-if="mode === 'add' || mode === 'edit'"
          type="button" 
          class="btn btn-sm btn-outline-primary" 
          @click="showAddCategoryModal = true"
        >
          + Add New
        </button>
      </div>
      <select 
        v-if="mode !== 'show'"
        class="form-select" 
        id="category" 
        v-model="form.category_id"
      >
        <option value="">Select Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
      <div v-else class="form-control-plaintext">
        {{ getCategoryName(form.category_id) || 'No category' }}
      </div>
      <div v-if="errors.category_id" class="text-danger">{{ errors.category_id }}</div>
    </div>

    <!-- Status Field -->
    <div class="mb-3">
      <label for="status" class="form-label">Status</label>
      <select 
        v-if="mode !== 'show'"
        class="form-select" 
        id="status" 
        v-model="form.status"
      >
        <option value="pending">Pending</option>
        <option value="completed">Completed</option>
      </select>
      <div v-else class="form-control-plaintext">
        {{ form.status ? form.status.charAt(0).toUpperCase() + form.status.slice(1) : '' }}
      </div>
      <div v-if="errors.status" class="text-danger">{{ errors.status }}</div>
    </div>

    <!-- Due Date Field -->
    <div class="mb-3">
      <label for="due_date" class="form-label">Due Date</label>
      <input 
        v-if="mode !== 'show'"
        type="datetime-local" 
        class="form-control" 
        id="due_date" 
        v-model="form.due_date"
      >
      <div v-else class="form-control-plaintext">
        {{ formatDisplayDate(form.due_date) || 'No due date' }}
      </div>
      <div v-if="errors.due_date" class="text-danger">{{ errors.due_date }}</div>
    </div>

    <!-- Form Buttons -->
    <div v-if="mode !== 'show'" class="d-flex justify-content-between">
      <button type="button" class="btn btn-secondary" @click="$emit('cancel')">Cancel</button>
      <button type="submit" class="btn btn-primary" :disabled="loading">
        {{ loading ? 'Saving...' : (mode === 'add' ? 'Add Task' : 'Update Task') }}
      </button>
    </div>

    <!-- View Mode Buttons -->
     <div v-else class="d-flex justify-content-between">
      <button type="button" class="btn btn-secondary" @click="$emit('cancel')">Close</button>
      <button 
        type="button" 
        class="btn btn-danger"
        @click="$emit('delete', task.id)"
      >
        Delete
      </button>
    </div>
    <!-- Add Category Modal -->
    <div v-if="showAddCategoryModal" class="modal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5)">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add New Category</h5>
            <button type="button" class="btn-close" @click="showAddCategoryModal = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="newCategoryName" class="form-label">Category Name</label>
              <input type="text" class="form-control" id="newCategoryName" v-model="newCategoryName" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="showAddCategoryModal = false">Cancel</button>
            <button type="button" class="btn btn-primary" @click="addNewCategory">Save</button>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import { ref, reactive, onMounted,watch } from 'vue'
import { useStore } from 'vuex'

export default {
  name: 'TaskForm',
  props: {
    task: {
      type: Object,
      default: null
    },
    categories: {
      type: Array,
      required: true,
            default: () => []  // Ensure default empty array

    },
    mode: {
      type: String,
      required: true,
      validator: value => ['add', 'edit', 'show'].includes(value)
    }
  },
  emits: ['submit', 'cancel', 'category-added', 'delete', 'edit-requested'],
  setup(props, { emit }) {
    const form = reactive({
      title: '',
      description: '',
      category_id: '',
      status: 'pending',
      due_date: ''
    })

    const errors = reactive({
      title: '',
      description: '',
      category_id: '',
      status: '',
      due_date: ''
    })
    const store = useStore()
    const loading = ref(false)
    const showAddCategoryModal = ref(false)
    const newCategoryName = ref('')

    onMounted(() => {
      if (props.task) {

        // Format date for datetime-local input
        let dueDateTime = '';
        if (props.task.due_date) {
          const date = new Date(props.task.due_date);
          // Convert to local datetime string in correct format (YYYY-MM-DDTHH:mm)
          const offset = date.getTimezoneOffset() * 60000;
          dueDateTime = new Date(date.getTime() - offset).toISOString().slice(0, 16);
        }

        // Initialize form with task data
        form.title = props.task.title || '';
        form.description = props.task.description || '';
        form.category_id = props.task.category_id || '';
        form.status = props.task.status || 'pending';
        form.due_date = dueDateTime;

       // console.log('Form initialized with:', form); // Debug log
      } else if (props.categories.length > 0) {
        form.category_id = props.categories[0]?.id || '';
      }
    });
    watch(() => props.task, (newTask) => {
    if (!newTask) {
      // Reset form when switching to add mode
      form.title = '';
      form.description = '';
      form.category_id = props.categories[0]?.id || '';
      form.status = 'pending';
      form.due_date = '';
    } else {
      // Handle edit mode initialization
     // let dueDateTime = '';
      if (newTask.due_date) {
        // const date = new Date(newTask.due_date);
        // const offset = date.getTimezoneOffset() * 60000;
        // dueDateTime = new Date(date.getTime() - offset).toISOString().slice(0, 16);
          form.due_date = newTask.due_date.replace(' ', 'T').substring(0, 16);

      }

      form.title = newTask.title || '';
      form.description = newTask.description || '';
      form.category_id = newTask.category_id || '';
      form.status = newTask.status || 'pending';
      //form.due_date = dueDateTime;
    }
  }, { immediate: true });
    const validateForm = () => {
      let valid = true

      // Reset errors
      Object.keys(errors).forEach(key => {
        errors[key] = ''
      })

      if (!form.title.trim()) {
        errors.title = 'Title is required'
        valid = false
      }

      if (!form.category_id) {
        errors.category_id = 'Category is required'
        valid = false
      }

      return valid
    }
    const switchToEditMode = () => {
      emit('edit-requested')
    }
       const getCategoryName = (categoryId) => {
      const category = props.categories.find(c => c.id === categoryId)
      return category ? category.name : ''
    }
      const formatDisplayDate = (dateString) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return date.toLocaleString()
    }
    const addNewCategory = async () => {
      if (!newCategoryName.value.trim()) return;

      try {
        loading.value = true;
        // Dispatch the createCategory action from the store
        const newCategory = await store.dispatch('createCategory', {
          name: newCategoryName.value
        });

        // Update the form with the new category
        form.category_id = newCategory.id;
        showAddCategoryModal.value = false;
        newCategoryName.value = '';
      } catch (error) {
        console.error('Error adding category:', error);
        // You could add error handling here to show the user
      } finally {
        loading.value = false;
      }
    }

    const submitForm = () => {
      if (!validateForm()) return

      loading.value = true

      try {
        emit('submit', { ...form })
      } catch (error) {
        console.error('Error submitting form:', error)
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      errors,
      loading,
      showAddCategoryModal,
      newCategoryName,
      submitForm,
      addNewCategory,
      switchToEditMode,
      getCategoryName,
      formatDisplayDate
    }
  }
}
</script>
<style scoped>
.form-control-plaintext {
  padding: 0.375rem 0.75rem;
  margin-bottom: 0;
  line-height: 1.5;
  background-color: #f8f9fa;
  border-radius: 0.25rem;
  min-height: calc(1.5em + 0.75rem + 2px);
}
</style>