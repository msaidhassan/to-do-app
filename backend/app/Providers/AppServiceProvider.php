<?php

namespace App\Providers;
use App\Models\User;
use App\Observers\UserObserver;
use App\Services\CategoryService;
use App\Services\TaskService;
use App\Services\TrashedTaskService;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\TrashedTaskRepositoryInterface;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;

use App\Models\Task;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(ExceptionHandlerContract::class, Handler::class);

        // $this->app->singleton(TaskService::class, function ($app) {
        //     return new TaskService($app->make(TaskRepositoryInterface::class));
        // });

        // $this->app->singleton(CategoryService::class, function ($app) {
        //     return new CategoryService($app->make(CategoryRepositoryInterface::class));
        // });

        // $this->app->singleton(TrashedTaskService::class, function ($app) {
        //     return new TrashedTaskService($app->make(TrashedTaskRepositoryInterface::class));
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        //
        User::observe(UserObserver::class);

        Model::preventSilentlyDiscardingAttributes(false);

        Route::bind('taskTrashed', function ($value) {
            return Task::onlyTrashed()->findOrFail($value);
        });

        // This is crucial for authorization to work with trashed models
    }
}
