<?php
namespace App\Services;

use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryService
{
    protected $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getUserCategories()
    {
        return $this->categoryRepository->findByUser(auth()->id());
    }

    public function createCategory(array $data)
    {
        $data['user_id'] = auth()->id();
        return $this->categoryRepository->create($data);
    }

    public function updateCategory($category, array $data)
    {
        return $this->categoryRepository->update($category, $data);
    }

    public function deleteCategory($category)
    {
        return $this->categoryRepository->delete($category->id);
    }
}
