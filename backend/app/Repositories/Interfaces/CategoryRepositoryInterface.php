<?php

namespace App\Repositories\Interfaces;

interface CategoryRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByUser($userId);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}
