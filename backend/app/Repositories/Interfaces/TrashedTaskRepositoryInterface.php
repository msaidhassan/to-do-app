<?php

namespace App\Repositories\Interfaces;
use Illuminate\Pagination\LengthAwarePaginator;

interface TrashedTaskRepositoryInterface
{
    // public function findTrashed(int $id);
    public function restore($trashedTask);
    public function forceDelete($trashedTask): bool;
    //public function searchTrashed(int $userId, string $query);
    public function restoreAll(int $userId): bool;
    public function forceDeleteAll(int $userId): bool;
    public function bulkRestore(array $taskIds, int $userId): int;
    public function bulkForceDelete(array $taskIds, int $userId): int;
    public function getAllTrashedPaginated(int $userId, array $filters = [],int $perPage) : LengthAwarePaginator;
} 
