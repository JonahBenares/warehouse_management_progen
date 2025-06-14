<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ItemSubCategory>
 */
class ItemSubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_category_id'=>$this->faker->numberBetween(1,5),
            'subcat_code'=>'subcat-code-'.rand(10,50),
            'subcat_prefix'=>'subcat-prefix-'.rand(10,50),
            'subcat_name'=>$this->faker->word,
        ];
    }
}
