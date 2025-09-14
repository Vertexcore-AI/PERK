<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Engine Parts', 'description' => 'Engine components and accessories'],
            ['name' => 'Brake System', 'description' => 'Brake pads, rotors, and brake components'],
            ['name' => 'Electrical', 'description' => 'Electrical components and wiring'],
            ['name' => 'Suspension', 'description' => 'Suspension parts and shock absorbers'],
            ['name' => 'Transmission', 'description' => 'Transmission components and parts'],
            ['name' => 'Cooling System', 'description' => 'Radiators, hoses, and cooling components'],
            ['name' => 'Exhaust System', 'description' => 'Mufflers, pipes, and exhaust components'],
            ['name' => 'Body Parts', 'description' => 'Exterior and interior body components'],
            ['name' => 'Filters', 'description' => 'Air, oil, and fuel filters'],
            ['name' => 'Lighting', 'description' => 'Headlights, taillights, and bulbs'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
