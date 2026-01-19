<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->sentence(),
            'due_at' => $this->faker->optional()->dateTimeBetween('-5 days', '+30 days'),
            'completed_at' => null,
            'is_active' => true,
        ];
    }
}
