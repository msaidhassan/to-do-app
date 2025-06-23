<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Support\Facades\Gate;
use App\Http\Responses\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;

class TaskService
{
    protected $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getTasksPaginated(array $filters = [])
    {
        return $this->taskRepository->getAllPaginated(auth()->id(), $filters,10);
    }

    public function createTask(array $data)
    {
      //  dd($data);
        if (!Gate::allows('use-category', $data['category_id'])) {
            throw new AuthorizationException('You are not authorized to use this category');
              }
        $data['user_id'] = auth()->id();
        return $this->taskRepository->create($data);
    }

    public function updateTask($task, array $data)
    {
        if ($data['category_id'] && !Gate::allows('use-category', $data['category_id']) ){
            throw new AuthorizationException('You are not authorized to use this category');
        }
        return $this->taskRepository->update($task, $data);
    }

    public function softDeleteTask($task)
    {
        return $this->taskRepository->delete($task);
    }

    

    public function bulkDeleteTasks(array $taskIds): int
    {
        return $this->taskRepository->bulkDelete($taskIds, auth()->id());
    }
}
