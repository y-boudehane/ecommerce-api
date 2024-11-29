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
            ['name' => 'Electronics'],
            ['name' => 'Fashion'],
            ['name' => 'Sports, Arts & Outdoors'],
            ['name' => 'Home, Furniture & Appliance'],
            ['name' => 'Health & Beauty'],
            ['name' => 'Agriculture & Food'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
