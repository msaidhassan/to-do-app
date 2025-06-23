<?php
// app/Http/Controllers/TaskController.php
namespace App\Http\Controllers;

use App\Http\Requests\Task\SearchTaskRequest;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Http\Requests\Task\BulkTaskRequest;
use App\Http\Requests\Task\TaskFilterRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
        $this->authorizeResource(Task::class, 'task');
    }

    public function index(TaskFilterRequest $request)
    {
        $tasks = $this->taskService->getTasksPaginated($request->validated());
        return ApiResponse::success($tasks);
    }

    

    public function show(Task $task)
    {
        return ApiResponse::success($task->load('category'));
    }

    public function store(StoreTaskRequest $request)
    {

        $task = $this->taskService->createTask($request->validated());
        return ApiResponse::success($task);
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {

        $updatedTask = $this->taskService->updateTask($task, $request->validated());
        return ApiResponse::success($updatedTask->load('category'));
    }

    public function destroy(Task $task)
    {
        $this->taskService->softDeleteTask($task->id);
        return ApiResponse::success(null, 'Task deleted successfully');
    }

    public function bulkDelete(BulkTaskRequest $request)
    {
        $this->authorize('bulkDelete', [Task::class, $request->task_ids]);

        $count = $this->taskService->bulkDeleteTasks($request->task_ids);
        return ApiResponse::success([
            'deleted_count' => $count
        ], 'Tasks deleted successfully');
    }
}
