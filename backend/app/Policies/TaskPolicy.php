<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Log;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Different logic for trashed vs non-trashed
        // if (request()->is('api/trashed/*')) {
        //     return true; // Or add your specific logic
        // }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @throws AuthorizationException
     */
    public function view(User $user, Task $task): bool
    {

        // For trashed controller, we allow viewing trashed items
        // if (request()->is('api/trashed/*')) {
        //     if (!$task->trashed()) {
        //         throw new AuthorizationException('This task is not in trash');
        //     }
        //    // return $user->id === $task->user_id;
        // }
        // Standard view check for non-trashed items
        if ($user->id !== $task->user_id) {
            throw new AuthorizationException('You are not authorized to view this task.');
        }

        return true;
    }
    // public function viewTrashed(User $user, Task $task): bool
    // {
    //     if (!$task->trashed()) {
    //         throw new AuthorizationException('This task is not in trash');
    //     }

    //     // Then check ownership
    //     if ($user->id !== $task->user_id) {
    //         throw new AuthorizationException('You are not authorized to view this trashed task');
    //     }

    //     return true;
    //     }

    // public function viewAnyTrashed(User $user): bool
    // {
    //     return true; // Or add your logic
    // }
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @throws AuthorizationException
     */
    public function update(User $user, Task $task): bool
    {
        if ($user->id !== $task->user_id) {
            throw new AuthorizationException('You are not authorized to update this task.');
        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @throws AuthorizationException
     */
    public function delete(User $user, Task $task): bool
    {
        if ($user->id !== $task->user_id) {
            throw new AuthorizationException('You are not authorized to delete this task.');
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @throws AuthorizationException
     */
    public function restore(User $user, Task $task): bool
    {
        if ($user->id !== $task->user_id) {
            throw new AuthorizationException('You are not authorized to restore this task.');
        }

        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @throws AuthorizationException
     */
    public function forceDelete(User $user, Task $task): bool
    {
        if ($user->id !== $task->user_id) {
            throw new AuthorizationException('You are not authorized to permanently delete this task.');
        }

        return true;
    }

    public function restoreAll(User $user): bool
    {
        return true;
    }

    public function forceDeleteAll(User $user): bool
    {
        return true;
    }

    public function search(User $user): bool
    {
        return true;
    }

    public function bulkDelete(User $user, array $taskIds): bool
    {


        $unauthorizedCount = Task::whereIn('id', $taskIds)
            ->where('user_id', '!=', $user->id)
            ->count();

        if ($unauthorizedCount > 0) {
            throw new AuthorizationException('You are not authorized to delete some of these tasks.');
        }

        return true;
    }
    public function bulkRestore(User $user, array $taskIds): bool
{
    $unauthorizedCount = Task::onlyTrashed()
        ->whereIn('id', $taskIds)
        ->where('user_id', '!=', $user->id)
        ->count();
    //    \Log::info('Unauthorized Count: ' . $unauthorizedCount);
    if ($unauthorizedCount > 0) {
        throw new AuthorizationException('You are not authorized to restore some of these tasks.');
    }

    return true;
}

public function bulkForceDelete(User $user, array $taskIds): bool
{
    $unauthorizedCount = Task::onlyTrashed()
        ->whereIn('id', $taskIds)
        ->where('user_id', '!=', $user->id)
        ->count();

    if ($unauthorizedCount > 0) {
        throw new AuthorizationException('You are not authorized to delete some of these tasks.');
    }

    return true;
}
}
