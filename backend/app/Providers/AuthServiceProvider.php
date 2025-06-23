<?php
// app/Providers/AuthServiceProvider.php
namespace App\Providers;

use App\Models\Category;
use App\Models\Task;
use App\Policies\CategoryPolicy;
use App\Policies\TaskPolicy;
use App\Policies\TrashedTaskPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Task::class => TaskPolicy::class,
        Category::class => CategoryPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Keep only the gate that's actually used
        Gate::define('use-category', function ($user, $categoryId) {
            if (!$categoryId) return true;
            $category = Category::find($categoryId);
            return $category && $user->id === $category->user_id;
        });
    }
}
