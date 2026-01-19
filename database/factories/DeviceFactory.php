<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'name' => $this->faker->words(2, true),
        'brand' => $this->faker->optional()->randomElement(['Apple','Samsung','Dell','Lenovo','Sony']),
        'model' => $this->faker->optional()->bothify('Model-###'),
        'serial_number' => $this->faker->optional()->bothify('SN-########'),
        'purchase_date' => $this->faker->optional()->date(),
        'notes' => $this->faker->optional()->sentence(),
    ];
}

}
