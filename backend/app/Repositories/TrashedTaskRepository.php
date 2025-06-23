<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TrashedTaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TrashedTaskRepository implements TrashedTaskRepositoryInterface
{
    protected $model;

    public function __construct(Task $model)
    {
        $this->model = $model;
    }


    public function getAllTrashedPaginated(int $userId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->onlyTrashed()
        ->forUser($userId)
        ->with('category');

        // Apply filters using model scopes
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
  $sortBy = $filters['sort_by'] ?? 'deleted_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
            return $query->orderBy($sortBy, $sortOrder)
                    ->paginate($perPage);
    }
    // public function findTrashed(int $id)
    // {
    //     return $this->model->onlyTrashed()->find($id);
    // }

    public function restore($trashedTask)
    {
        $trashedTask->restore();
        return $trashedTask->fresh();

    }

    public function restoreAll(int $userId): bool
    {
        return $this->model->onlyTrashed()
        ->forUser($userId)
        ->restore();
    }

    public function forceDelete($trashedTask): bool
    {
       // $task = $this->findTrashed($id);
        return  $trashedTask->forceDelete();
    }



    public function forceDeleteAll(int $userId): bool
    {
        return $this->model->onlyTrashed()
        ->forUser($userId)
        ->forceDelete();

    }

    public function bulkRestore(array $taskIds, int $userId): int
    {
          return $this->model->onlyTrashed()
        ->whereIn('id', $taskIds)
        ->forUser($userId)
        ->restore();

    }

    public function bulkForceDelete(array $taskIds, int $userId): int
    {
        return $this->model->onlyTrashed()
            ->whereIn('id', $taskIds)
            ->forUser($userId)
            ->forceDelete();
    }





}
