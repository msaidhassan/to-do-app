<?php

namespace App\Services;

use App\Repositories\Interfaces\TrashedTaskRepositoryInterface;

class TrashedTaskService
{
    protected $trashedtaskRepository;

    public function __construct(TrashedTaskRepositoryInterface $trashedtaskRepository)
    {
        $this->trashedtaskRepository = $trashedtaskRepository;
    }

    // public function getTrashedTasks(array $filters = [])
    // {
    //     return $this->trashedtaskRepository->getAllTrashed(auth()->id(), $filters);
    // }

    public function getTrashedTasksPaginated(array $filters = [])
    {
        return $this->trashedtaskRepository->getAllTrashedPaginated(auth()->id(), $filters ,10);
    }

    public function restoreTask($trashedTask)
    {
        return $this->trashedtaskRepository->restore($trashedTask);
    }

    public function restoreAllTasks(): bool
    {
        $userId = auth()->id();
        return $this->trashedtaskRepository->restoreAll($userId);
    }


    public function forceDeleteTask($trashedTask)
    {
        return $this->trashedtaskRepository->forceDelete($trashedTask);
    }

    public function forceDeleteAllTasks(): bool
    {
        $userId = auth()->id();
        return $this->trashedtaskRepository->forceDeleteAll($userId);
    }

    public function bulkForceDeleteTasks(array $taskIds): int
    {
        return $this->trashedtaskRepository->bulkForceDelete($taskIds, auth()->id());
    }

    public function bulkRestoreTasks(array $taskIds): int
    {
        return $this->trashedtaskRepository->bulkRestore($taskIds, auth()->id());
    }
}
