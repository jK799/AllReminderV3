<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(['polisa.pdf','faktura.pdf','zdjecie.jpg','umowa.pdf']);
        $isJpg = str_ends_with($name, '.jpg');

        return [
            'title' => $this->faker->optional()->sentence(3),
            'notes' => $this->faker->optional()->sentence(),
            'original_name' => $name,
            'path' => 'documents/'.$this->faker->uuid.'-'.$name,
            'mime_type' => $isJpg ? 'image/jpeg' : 'application/pdf',
            'size' => $this->faker->numberBetween(50_000, 5_000_000),
        ];
    }
}
