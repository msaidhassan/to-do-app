# Todo List Vue Frontend

This is a Vue.js frontend for the Todo List application that connects to a Laravel API backend. 

## Features

- User authentication (login/register)
- Create, read, update, and delete tasks
- Categorize tasks (Work, Personal, Urgent)
- Filter tasks by status and category
- Search tasks by title or description
- Pagination for task listing
- Soft delete and restore functionality
- Due date assignment and tracking

## Setup Instructions

### Prerequisites

- Node.js (v14 or later)
- npm or yarn
- Laravel backend API running (usually at http://localhost:8000)

### Installation

1. Clone this repository
```
git clone https://github.com/yourusername/todo-vue-frontend.git
cd todo-vue-frontend
```

2. Install dependencies
```
npm install
```

3. Configure the API endpoint

Open `src/services/api.js` and make sure the `baseURL` is set to your Laravel API endpoint (default is `http://localhost:8000/api`).

4. Start the development server
```
npm run serve
```

The application will be available at `http://localhost:8080`.

### Building for Production

```
npm run build
```

This will create a `dist` folder with the compiled assets ready for deployment.

## API Endpoints Used

The frontend interacts with the following API endpoints:

- `POST /api/register` - Register a new user
- `POST /api/login` - Authenticate user
- `GET /api/tasks` - Get all tasks (with pagination, filtering, and search)
- `POST /api/tasks` - Create a new task
- `PUT /api/tasks/{id}` - Update an existing task
- `DELETE /api/tasks/{id}` - Soft delete a task
- `PUT /api/tasks/{id}/restore` - Restore a soft-deleted task

## Technology Stack

- Vue.js 3
- Vue Router for navigation
- Vuex for state management
- Axios for API requests
- Bootstrap 5 for styling

## Project Structure

- `src/components/Auth` - Authentication components (Login, Register)
- `src/components/Todo` - Todo-related components (TaskList, TaskForm, TaskItem, TaskFilter)
- `src/views` - Page components (Home, Dashboard, NotFound)
- `src/router` - Vue Router configuration
- `src/store` - Vuex store for state management
- `src/services` - API service for backend communication