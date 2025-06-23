<?php

namespace App\Repositories\Interfaces;
use Illuminate\Pagination\LengthAwarePaginator;

interface TaskRepositoryInterface
{
    // public function getAllPaginated();
    // public function find(int $id);
    public function getAllPaginated(int $userId, array $filters = [],int $perPage) : LengthAwarePaginator;
    public function create(array $data);
    public function update($task, array $data);
    public function delete($task);
    public function bulkDelete(array $taskIds, int $userId): int;

}
