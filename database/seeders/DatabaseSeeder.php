<?php

namespace Database\Seeders;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EnduseTableSeeder::class,
            ItemStatusTableSeeder::class,
            LocationTableSeeder::class,
            RackTableSeeder::class,
            RestockReasonTableSeeder::class,
            WarehouseTableSeeder::class,
        ]);
        \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\Group::factory(5)->create();
        \App\Models\Uom::factory(5)->create();
        \App\Models\Supplier::factory(15)->create();
        \App\Models\Department::factory(5)->create();
        \App\Models\Purpose::factory(10)->create();
        \App\Models\ItemCategory::factory(5)->create();
        \App\Models\ItemSubCategory::factory(10)->create();
        \App\Models\User::factory(1)->create();
        \App\Models\Item::factory(20)->create();
       
    }
}
