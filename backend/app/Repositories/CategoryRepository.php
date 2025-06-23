<?php

namespace App\Repositories;

use App\Models\Category;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $model;

    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function findByUser($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($category, array $data)
    {
        //$category = $this->model->find($id);
        $category->update($data);
        //dd($category);

        return $category;
    }

    public function delete($category)
    {
        return $this->model->destroy($category);
    }
}
