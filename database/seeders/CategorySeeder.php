<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Plumbing', 'icon_name' => 'plumbing', 'color_hex' => '#2196F3'],
            ['name' => 'Carpentry', 'icon_name' => 'handyman', 'color_hex' => '#795548'],
            ['name' => 'Electrical', 'icon_name' => 'electrical_services', 'color_hex' => '#FFC107'],
            ['name' => 'Masonry', 'icon_name' => 'construction', 'color_hex' => '#9E9E9E'],
            ['name' => 'Cleaning', 'icon_name' => 'cleaning_services', 'color_hex' => '#00BCD4'],
            ['name' => 'Painting', 'icon_name' => 'format_paint', 'color_hex' => '#E91E63'],
            ['name' => 'Landscaping', 'icon_name' => 'park', 'color_hex' => '#4CAF50'],
            ['name' => 'Roofing', 'icon_name' => 'roofing', 'color_hex' => '#FF5722'],
            ['name' => 'HVAC', 'icon_name' => 'hvac', 'color_hex' => '#03A9F4'],
            ['name' => 'Appliance Repair', 'icon_name' => 'microwave', 'color_hex' => '#607D8B'],
            ['name' => 'Pest Control', 'icon_name' => 'pest_control', 'color_hex' => '#8D6E63'],
            ['name' => 'Tailoring', 'icon_name' => 'checkroom', 'color_hex' => '#E040FB'],
            ['name' => 'Photography', 'icon_name' => 'camera_alt', 'color_hex' => '#9C27B0'],
            ['name' => 'Mechanic', 'icon_name' => 'car_repair', 'color_hex' => '#F44336'],
            ['name' => 'Welding', 'icon_name' => 'engineering', 'color_hex' => '#546E7A'],
            ['name' => 'Other', 'icon_name' => 'category', 'color_hex' => '#9E9E9E'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
