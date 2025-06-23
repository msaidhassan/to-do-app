<?php
// app/Http/Controllers/CategoryController.php
namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Responses\ApiResponse;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;

       $this->authorizeResource(Category::class, 'category');


    }

    public function index()
    {
        $categories = $this->categoryService->getUserCategories();
        return ApiResponse::success($categories);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory(['name'=>$request->name]);
        return ApiResponse::success($category);
    }

    public function show(Category $category)
    {
      //  $category = $this->categoryService->getCategoryById($category);
        return ApiResponse::success($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->updateCategory($category, ['name'=>$request->name]);
        return ApiResponse::success($category);
    }

    public function destroy(Category $category)
    {
        $this->categoryService->deleteCategory($category);
        // Check if the category is empty before deleting

        return ApiResponse::success(null, 'Category deleted successfully');
    }
}
