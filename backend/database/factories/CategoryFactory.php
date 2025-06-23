<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    private array $defaultCategories = [
        'Uncategorized',
        'Work',
        'Personal',
        'Urgent',
        ''
    ];

    public function definition(): array
    {
        $created_at = $this->faker->dateTimeBetween('-1 year', 'now');

        return [
            'name' => $this->faker->randomElement($this->defaultCategories),
            'created_at' => $created_at,
            'updated_at' => $this->faker->dateTimeBetween($created_at, 'now'),
        ];
    }

    /**
     * Configure the factory to create a specific default category
     */
    public function defaultCategory(string $name): static
    {
        return $this->state(function (array $attributes) use ($name) {
            return [
                'name' => $name,
            ];
        });
    }
}
