<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_name' => $this->faker->name,
            'address' => $this->faker->address,
            'contact_person' => $this->faker->name,
            'contact_number' => $this->faker->phoneNumber,
            'email' => $this->faker->email,
            'terms' => $this->faker->paragraph(2),
            'is_active' =>'1'
        ];
    }
}
