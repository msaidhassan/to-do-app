# Full Stack To-Do Application

A modern, containerized to-do application built with Vue.js 3 + Vite for the frontend and Laravel for the backend API.

## Features

- User authentication (registration and login)
- Task management (create, read, update, delete)
  - Task title and description
  - Due date management
  - Task status tracking (pending, completed)
  - Category assignment
- Category organization for tasks
  - Custom category creation
  - Category color coding
  - Category-based task filtering
- Task filtering by category and status
- Soft deletion support for tasks
  - Trash bin for deleted tasks
  - Task restoration capability
- Pagination for task lists
- Modern and responsive UI
- Docker containerization for easy deployment

## Tech Stack

### Frontend
- Vue.js 3
- Vuex for state management
- Vue Router
- Vite as build tool
- Axios for API communication

### Backend
- Laravel
- MySQL database
- Laravel Sanctum for authentication
- Repository pattern implementation
- Policy-based authorization

### Infrastructure
- Docker & Docker Compose
- Nginx web server
- PHP-FPM

## Prerequisites

- Docker
- Docker Compose
- Git

## Installation & Setup

1. Clone the repository:
```bash
git clone <repository-url>
cd to-do-app
```

2. Create environment files:

For backend (`.env`):
```bash
cp backend/.env.example backend/.env
```

3. Start the Docker containers:
```bash
docker-compose up -d
```

4. Install backend dependencies:
```bash
docker-compose exec backend composer install
```

5. Generate application key:
```bash
docker-compose exec backend php artisan key:generate
```

6. Run database migrations:
```bash
docker-compose exec backend php artisan migrate
```

7. Install frontend dependencies:
```bash
docker-compose exec frontend npm install
```

## Running the Application

The application will be available at:
- Frontend: http://localhost:5173
- Backend API: http://localhost:8000

## API Endpoints

### Authentication
- POST `/api/register` - Register a new user
- POST `/api/login` - Login user
- POST `/api/logout` - Logout user

### Tasks
- GET `/api/tasks` - List all tasks (with pagination)
- POST `/api/tasks` - Create a new task
- GET `/api/tasks/{id}` - Get a specific task
- PUT `/api/tasks/{id}` - Update a task
- DELETE `/api/tasks/{id}` - Delete a task
- GET `/api/tasks/trashed` - List soft-deleted tasks

### Categories
- GET `/api/categories` - List all categories
- POST `/api/categories` - Create a new category
- PUT `/api/categories/{id}` - Update a category
- DELETE `/api/categories/{id}` - Delete a category

## Development

### Frontend Development
```bash
docker-compose exec frontend npm run dev
```

### Backend Development
```bash
# Run tests
docker-compose exec backend php artisan test

# Create new migration
docker-compose exec backend php artisan make:migration migration_name

# Create new controller
docker-compose exec backend php artisan make:controller ControllerName
```

## Project Structure

```
├── frontend/          # Vue.js frontend application
│   ├── src/
│   │   ├── components/  # Vue components
│   │   ├── views/      # Page components
│   │   ├── store/      # Vuex store modules
│   │   ├── router/     # Vue Router configuration
│   │   └── services/   # API services
│   └── ...
├── backend/           # Laravel backend application
│   ├── app/
│   │   ├── Http/      # Controllers, Middleware, Requests
│   │   ├── Models/    # Eloquent models
│   │   ├── Services/  # Business logic services
│   │   └── Repositories/  # Data access layer
│   └── ...
└── docker/           # Docker configuration files
```

## Troubleshooting

### Common Issues

1. CSRF Token Mismatch
   - Ensure your frontend is sending the CSRF token in the request header
   - Check if the CSRF cookie is being set properly
   - Verify CORS settings in `backend/config/cors.php`

2. Authentication Issues
   - Clear browser storage and cookies
   - Ensure your API requests include the authentication token
   - Check if token is being stored correctly in Vuex store

3. Docker Setup Issues
   - Ensure all required ports are available
   - Check Docker logs: `docker-compose logs -f`
   - Rebuild containers if needed: `docker-compose up -d --build`

### Development Tips

1. API Debugging
   - Use browser DevTools Network tab to monitor API requests
   - Check Laravel logs: `docker-compose exec backend tail -f storage/logs/laravel.log`
   - Enable debug mode in `.env` for detailed error messages

2. Frontend Development
   - Use Vue DevTools for component debugging
   - Check Vuex store state in browser DevTools
   - Run frontend tests: `docker-compose exec frontend npm run test`

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is open-sourced software licensed under the MIT license.
