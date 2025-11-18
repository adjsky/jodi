<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Todo>
 */
class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string,mixed>
     */
    public function definition(): array
    {
        $categories = ['Work', 'Personal', 'Shopping', 'Health', 'Errands', 'Home'];

        return [
            'title' => fake()->sentence(rand(3, 8)),
            'description' => fake()->optional(0.7)->paragraph(rand(1, 3)),
            'category' => fake()->randomElement($categories),
            'todo_date' => now()->format('Y-m-d'),
        ];
    }
}
