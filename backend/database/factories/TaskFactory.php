<?php
namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    protected function withFaker()
    {
        $faker = parent::withFaker();
        $faker->setDefaultTimezone(config('app.timezone'));
        return $faker;
    }

    public function definition(): array
    {
        $statuses = ['pending', 'in_progress', 'completed'];

        // Format dates consistently for MySQL
        $created = $this->faker->dateTimeBetween('-1 month', 'now');
        $created_at = Carbon::parse($created)->format('Y-m-d H:i:s');

        $updated = $this->faker->dateTimeBetween($created, 'now');
        $updated_at = Carbon::parse($updated)->format('Y-m-d H:i:s');

        // Format due_date if it exists
        $due_date = $this->faker->optional(0.7)->dateTimeBetween('now', '+1 month');
        if ($due_date) {
            $due_date = Carbon::parse($due_date)->format('Y-m-d H:i:s');
        }

        // Format deleted_at if it exists
        $deleted_at = null;
        if ($this->faker->boolean(20)) { // 20% chance of being deleted
            $deleted = $this->faker->dateTimeBetween($updated, 'now');
            $deleted_at = Carbon::parse($deleted)->format('Y-m-d H:i:s');
        }

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement($statuses),
            'due_date' => $due_date,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'deleted_at' => $deleted_at
        ];
    }
}
