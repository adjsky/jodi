<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Event\Models\Event;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string,mixed>
     */
    public function definition(): array
    {
        $startsAt = CarbonImmutable::instance(fake()->dateTimeBetween('-1 week', '+1 week'));

        return [
            'title' => fake()->sentence(rand(3, 8)),
            'description' => fake()->paragraph(rand(1, 3)),
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHours(fake()->numberBetween(1, 8)),
            'notify_at' => $startsAt->subHour(),
            'notify_status' => 'waiting',
        ];
    }
}
