# Laravel Todo List API

A RESTful API for a Todo List application built with Laravel, featuring user authentication, task management with categories, and advanced filtering.

## Features

- User authentication with Laravel Sanctum
- Task management (CRUD operations with soft delete)
- Category management
- Task filtering by status, category, and due date
- Task search functionality
- RESTful API with pagination
- Policy-based authorization with Laravel Gates

## Project Structure

The project follows Laravel best practices with a clean separation of concerns:

- **Controllers**: Handle HTTP requests and responses
- **Models**: Define database structure and relationships
- **Requests**: Handle validation rules
- **Policies**: Define authorization rules
- **Services**: Contain business logic
- **Responses**: Standardize API responses

## Technical Implementation

### Authentication

- Uses Laravel Sanctum for API token authentication
- Includes registration, login, and logout endpoints

### Authorization

- Implements Laravel Policies and Gates
- Ensures users can only access their own resources

### Task Management

- Complete CRUD operations with soft delete
- Filtering by status, category, and due date
- Search functionality by title or description
- Pagination for efficient data loading

### API Standardization

- Consistent response format across all endpoints
- Proper HTTP status codes
- Detailed error messages

## Installation

1. Clone the repository
```bash
git clone 
cd todo-list-api
```

2. Install dependencies
```bash
composer install
```

3. Configure environment
```bash
cp .env.example .env
php artisan key:generate
```

4. Update the `.env` file with your database credentials
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=todo_app
DB_USERNAME=root
DB_PASSWORD=
```

5. Run migrations and seeders
```bash
php artisan migrate
php artisan db:seed
```

6. Start the development server
```bash
php artisan serve
```

## API Documentation

### Authentication Endpoints

#### Register a new user
```
POST /api/register
```
**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```
POST /api/login
```
**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": { ... },
    "token": "your-auth-token"
  }
}
```

#### Logout
```
POST /api/logout
```
**Headers:**
```
Authorization: Bearer your-auth-token

### Category Endpoints

#### Get all categories
```
GET /api/categories
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "name": "Work",
      "created_at": "2023-10-20T12:00:00.000000Z",
      "updated_at": "2023-10-20T12:00:00.000000Z"
    },
    ...
  ]
}
```

#### Create a new category
```
POST /api/categories
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Request Body:**
```json
{
  "name": "Shopping"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Resource created successfully",
  "data": {
    "id": 4,
    "user_id": 1,
    "name": "Shopping",
    "created_at": "2023-10-21T10:30:00.000000Z",
    "updated_at": "2023-10-21T10:30:00.000000Z"
  }
}
```

#### Get a specific category
```
GET /api/categories/{id}
```
**Headers:**
```
Authorization: Bearer your-auth-token
```

#### Update a category
```
PUT /api/categories/{id}
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Request Body:**
```json
{
  "name": "Shopping List"
}
```

#### Delete a category
```
DELETE /api/categories/{id}
```
**Headers:**
```
Authorization: Bearer your-auth-token
```

### Task Endpoints

#### Get all tasks (with pagination and filtering)
```
GET /api/tasks
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Query Parameters:**
- `status`: Filter by status (`pending`, `in_progress`, `completed`)
- `category_id`: Filter by category ID
- `due_date`: Filter by due date (YYYY-MM-DD)
- `sort_by`: Field to sort by (`title`, `status`, `due_date`, `created_at`)
- `sort_order`: Sort direction (`asc` or `desc`)
- `page`: Page number for pagination

**Response:**
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "category_id": 1,
        "title": "Complete Project",
        "description": "Finish the Laravel To-Do API project",
        "status": "pending",
        "due_date": "2023-12-31",
        "created_at": "2023-10-20T12:00:00.000000Z",
        "updated_at": "2023-10-20T12:00:00.000000Z",
        "deleted_at": null,
        "category": {
          "id": 1,
          "user_id": 1,
          "name": "Work",
          "created_at": "2023-10-20T12:00:00.000000Z",
          "updated_at": "2023-10-20T12:00:00.000000Z"
        }
      },
      ...
    ],
    "first_page_url": "http://localhost:8000/api/tasks?page=1",
    "from": 1,
    "last_page": 2,
    "last_page_url": "http://localhost:8000/api/tasks?page=2",
    "links": [
      ...
    ],
    "next_page_url": "http://localhost:8000/api/tasks?page=2",
    "path": "http://localhost:8000/api/tasks",
    "per_page": 10,
    "prev_page_url": null,
    "to": 10,
    "total": 15
  }
}
```

#### Create a new task
```
POST /api/tasks
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Request Body:**
```json
{
  "title": "Buy groceries",
  "description": "Milk, eggs, bread",
  "status": "pending",
  "due_date": "2023-10-25",
  "category_id": 4
}
```
**Response:**
```json
{
  "success": true,
  "message": "Resource created successfully",
  "data": {
    "id": 21,
    "user_id": 1,
    "category_id": 4,
    "title": "Buy groceries",
    "description": "Milk, eggs, bread",
    "status": "pending",
    "due_date": "2023-10-25",
    "created_at": "2023-10-21T12:00:00.000000Z",
    "updated_at": "2023-10-21T12:00:00.000000Z",
    "deleted_at": null
  }
}
```

#### Get a specific task
```
GET /api/tasks/{id}
```
**Headers:**
```
Authorization: Bearer your-auth-token
```

#### Update a task
```
PUT /api/tasks/{id}
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Request Body:**
```json
{
  "title": "Buy groceries",
  "description": "Milk, eggs, bread, cheese",
  "status": "completed",
  "due_date": "2023-10-25",
  "category_id": 4
}
```

#### Soft delete a task
```
DELETE /api/tasks/{id}
```
**Headers:**
```
Authorization: Bearer your-auth-token
```

#### Restore a soft-deleted task
```
POST /api/tasks/{id}/restore
```
**Headers:**
```
Authorization: Bearer your-auth-token
```

#### Search tasks
```
GET /api/tasks/search?query=keyword
```
**Headers:**
```
Authorization: Bearer your-auth-token
```
**Query Parameters:**
- `query`: Search keyword (minimum 2 characters)

## Error Handling

All API endpoints use consistent error responses:

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error detail"]
  }
}
```

Common status codes:
- `200`: Success
- `201`: Resource created
- `400`: Bad request
- `401`: Unauthorized
- `403`: Forbidden
- `404`: Not found
- `422`: Validation error
- `500`: Server error

