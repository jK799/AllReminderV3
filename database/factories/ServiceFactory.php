<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isRecurring = $this->faker->boolean(70);

        return [
            'title' => $this->faker->randomElement([
                'Wymiana oleju', 'Przegląd', 'Serwis', 'Czyszczenie', 'Wymiana filtra'
            ]),
            'description' => $this->faker->optional()->sentence(),
            'last_done_at' => $this->faker->optional()->date(),
            'next_due_at' => $this->faker->optional()->dateTimeBetween('now', '+90 days'),
            'interval_value' => $isRecurring ? $this->faker->randomElement([1, 3, 6, 12]) : null,
            'interval_unit' => $isRecurring ? $this->faker->randomElement(['days','weeks','months','years']) : null,
            'is_active' => true,
        ];
    }
}
