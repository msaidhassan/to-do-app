<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrashedTaskController;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Protected routes that require authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // Categories
    Route::apiResource('categories', CategoryController::class);

    // Tasks
    //Route::get('/tasks/search', [TaskController::class, 'search']);
    Route::delete('tasks/bulk', [TaskController::class, 'bulkDelete']);
    Route::apiResource('tasks', TaskController::class);
    Route::prefix('trashed/tasks')->middleware('auth:sanctum')->group(function () {
    //    Route::get('/search', [TrashedTaskController::class, 'search']);
        Route::delete('bulk', [TrashedTaskController::class, 'bulkForceDelete']);
        Route::post('bulk/restore', [TrashedTaskController::class, 'bulkRestore']);
        Route::get('/', [TrashedTaskController::class, 'index']);
        Route::get('/{taskTrashed}', [TrashedTaskController::class, 'show']);
        Route::post('/{taskTrashed}/restore', [TrashedTaskController::class, 'restore']);
        Route::post('/restore-all', [TrashedTaskController::class, 'restoreAll']);
        Route::delete('/{taskTrashed}', [TrashedTaskController::class, 'forceDelete']);
        Route::delete('/', [TrashedTaskController::class, 'forceDeleteAll']);
    });
});

Route::get('/current-time', function() {
    return [
        'timezone' => config('app.timezone'),
        'current_time' => now()->format('Y-m-d H:i:s'),
        'utc_time' => now()->utc()->format('Y-m-d H:i:s')
    ];
});
