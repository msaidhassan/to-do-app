<?php
namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MassDataSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks and events for faster seeding
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Task::unsetEventDispatcher();

        $this->command->info('Creating 100 users with tasks...');
        $progressBar = $this->command->getOutput()->createProgressBar(100);
        $progressBar->start();

        // Use a transaction for all users
        DB::transaction(function () use ($progressBar) {
            // Create users in chunks
            User::factory()
                ->count(100)
                ->create()
                ->each(function ($user) use ($progressBar) {
                    // Create default categories for user
                    $defaultCategories = ['Uncategorized', 'Work', 'Personal', 'Urgent', 'Fun'];

                    $categories = collect($defaultCategories)->map(function ($name) use ($user) {
                        return Category::factory()
                            ->defaultCategory($name)
                            ->create(['user_id' => $user->id]);
                    })->all();

                    // Create tasks using factory in optimized way
                    $tasks = Task::factory()
                    ->count(1000)
                    ->make([
                        'user_id' => $user->id,
                        'category_id' => null,
                    ])
                    ->map(function ($task) use ($categories) {
                $task->category_id = $categories[array_rand($categories)]->id;

                        return [
                            'title' => $task->title,
                            'description' => $task->description,
                            'status' => $task->status,
                            'due_date' => $task->due_date ? $task->due_date->format('Y-m-d H:i:s') : null,
                            'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                            'updated_at' => $task->updated_at->format('Y-m-d H:i:s'),
                            'deleted_at' => $task->deleted_at ? $task->deleted_at->format('Y-m-d H:i:s') : null,
                            'user_id' => $task->user_id,
                            'category_id' => $task->category_id,
                        ];
                    })
                    ->all();
                       // \Log::info($tasks);

                    // Insert tasks in batches
                    $chunkSize = 500;
                    foreach (array_chunk($tasks, $chunkSize) as $chunk) {
                        Task::insert($chunk);
                    }

                    $progressBar->advance();
                });
        });

        $progressBar->finish();
        $this->command->newLine();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Mass data creation completed!');
    }
}
