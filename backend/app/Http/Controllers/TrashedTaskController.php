<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Services\TrashedTaskService;
use App\Http\Requests\Task\BulkTaskRequest;
use App\Http\Requests\Task\SearchTaskRequest;
use App\Http\Requests\Task\TaskFilterRequest;

use Illuminate\Http\Request;

class TrashedTaskController extends Controller
{
    protected $trashedTaskService;

    public function __construct(TrashedTaskService $trashedTaskService)
    {
        $this->trashedTaskService = $trashedTaskService;

       // Explicitly tell Laravel to use TrashedTaskPolicy
        $this->authorizeResource(Task::class, 'taskTrashed');

    }

    public function index(TaskFilterRequest $request)
    {
        //$filters = $request->only(['status', 'category_id', 'due_date', 'sort_by', 'sort_order']);
        $trashedTasks = $this->trashedTaskService->getTrashedTasksPaginated($request->validated());
        return ApiResponse::success($trashedTasks);
    }

    public function show(Task $trashedTask)
    {

   return ApiResponse::success($trashedTask->load('category'));
    }

    public function restore(Task $trashedTask)
    {
        $this->authorize('restore', $trashedTask);
        $restoredTrashedTask = $this->trashedTaskService->restoreTask($trashedTask);
        return ApiResponse::success($restoredTrashedTask, 'Task restored successfully');
    }

    public function forceDelete(Task $trashedTask)
    {
        $this->authorize('forceDelete', $trashedTask);

        $this->trashedTaskService->forceDeleteTask($trashedTask);
        return ApiResponse::success(null, 'Task permanently deleted');
    }

    public function restoreAll()
    {
        $this->authorize('restoreAll', Task::class);
        $this->trashedTaskService->restoreAllTasks();
        return ApiResponse::success(null, 'All tasks restored successfully');
    }

    public function forceDeleteAll()
    {
        $this->authorize('forceDeleteAll', Task::class);
        $this->trashedTaskService->forceDeleteAllTasks();
        return ApiResponse::success(null, 'All tasks permanently deleted');
    }

    public function bulkRestore(BulkTaskRequest $request)
    {
        $this->authorize('bulkRestore', [Task::class, $request->task_ids]);

        $count = $this->trashedTaskService->bulkRestoreTasks($request->task_ids);
        return ApiResponse::success([
            'restored_count' => $count
        ], 'Tasks restored successfully');
    }

    public function bulkForceDelete(BulkTaskRequest $request)
    {
        $this->authorize('bulkForceDelete', [Task::class, $request->task_ids]);

        $count = $this->trashedTaskService->bulkForceDeleteTasks($request->task_ids);
        return ApiResponse::success([
            'deleted_count' => $count
        ], 'Tasks permanently deleted');
    }

    public function search(SearchTaskRequest $request)
    {
        $tasks = $this->trashedTaskService->searchTrashedTasks(
            $request->input('query')
        );
        return ApiResponse::success($tasks);
    }
}
