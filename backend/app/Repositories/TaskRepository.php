<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskRepository implements TaskRepositoryInterface
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    public function getAllPaginated(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->forUser($userId);

        if (isset($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->byCategory($filters['category_id']);
        }

        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }

        $startDate = $filters['start_date'] ?? null;
        $endDate = $filters['end_date'] ?? null;

        if ($startDate || $endDate) {
            $query->dueBetween($startDate, $endDate);
        }

        // Custom sorting
        if (isset($filters['sort_by'])) {
            $direction = $filters['sort_order'] ?? 'asc';
            $query->withoutGlobalScope('default_sort')
                  ->orderBy($filters['sort_by'], $direction);
        }

        return $query->with('category')->paginate($perPage);
    }

    

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($task, array $data)
    {
        //$task = $this->model->find($id);
        $task->update($data);
        return $task;
    }

    public function delete($task): bool
    {
        return $this->model->destroy($task);
    }

    public function bulkDelete(array $taskIds, int $userId): int
    {
        return $this->model
            ->whereIn('id', $taskIds)
            ->forUser($userId)
            ->delete();
    }
}
