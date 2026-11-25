<?php

namespace Database\Factories;

use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'purpose' => fake()->randomElement(['venda', 'aluguel']),
            'type' => fake()->randomElement(['Casa', 'Apartamento', 'Cobertura', 'Comercial']),
            'price' => fake()->randomFloat(2, 5000, 5000000),
            'condo_fee' => fake()->randomFloat(2, 0, 3200),
            'iptu' => fake()->randomFloat(2, 0, 1500),
            'area' => fake()->randomFloat(2, 40, 500),
            'lot_area' => fake()->optional()->randomFloat(2, 100, 900),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 6),
            'parking' => fake()->numberBetween(0, 4),
            'suite' => fake()->numberBetween(0, 3),
            'address' => fake()->streetAddress(),
            'neighborhood' => fake()->words(2, true),
            'city' => fake()->city(),
            'state' => fake()->randomElement(['SP', 'RJ', 'MG']),
            'zip_code' => fake()->postcode(),
            'latitude' => fake()->latitude(-25, -20),
            'longitude' => fake()->longitude(-50, -40),
            'description' => fake()->paragraph(),
            'features' => fake()->randomElements(
                ['Piscina', 'Varanda gourmet', 'Academia', 'Jardim', 'Portaria 24h', 'Mobiliado'],
                fake()->numberBetween(2, 4),
            ),
            'photos' => [
                'https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=1400&q=85',
            ],
            'featured' => fake()->boolean(25),
            'status' => 'disponivel',
        ];
    }
}
