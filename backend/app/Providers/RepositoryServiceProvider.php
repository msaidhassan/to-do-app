<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\TaskRepository;
use App\Repositories\AuthRepository;
use App\Repositories\Interfaces\TrashedTaskRepositoryInterface;
use App\Repositories\TrashedTaskRepository;


class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(TrashedTaskRepositoryInterface::class, TrashedTaskRepository::class);

    }
}
