<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Todo\Models\Todo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Todo>
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
        return [
            'title' => fake()->sentence(rand(3, 8)),
            'description' => fake()->paragraph(rand(1, 3)),
            'scheduled_at' => fake()->dateTimeBetween('-1 week', '+1 week')->format('Y-m-d'),
            'has_time' => false,
            'notify_at' => null,
        ];
    }
}
