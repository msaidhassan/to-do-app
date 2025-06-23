<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Category;

class UserObserver
{
    private array $defaultCategories = [
        [
            'name' => 'Uncategorized'
        ],
        [
            'name' => 'Work',
        ],
        [
            'name' => 'Personal'
        ],
        [
            'name' => 'Urgent'        ],
            [
                'name' => 'Fun'        ]

    ];

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        foreach ($this->defaultCategories as $categoryData) {
            $category = Category::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'name' => $categoryData['name'],
                ]
            );

        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
