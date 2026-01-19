<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $make = $this->faker->randomElement(['BMW', 'Audi', 'Mercedes', 'Toyota', 'Ford']);
    $model = $this->faker->bothify('??##');

    return [
        'name' => $make . ' ' . $model, // <- MUSI być
        'make' => $make,
        'model' => $model,
        'year' => $this->faker->optional()->numberBetween(1998, 2026),
        'vin' => $this->faker->optional()->regexify('[A-HJ-NPR-Z0-9]{17}'),
        'license_plate' => $this->faker->optional()->bothify('KR ####?'),
        'notes' => $this->faker->optional()->sentence(),
    ];
}

}
