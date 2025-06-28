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
            [
                'name' => 'Programming Languages',
                'description' => 'Courses related to programming languages such as Python, Java, C++, etc.',
            ],
            [
                'name' => 'Web Development',
                'description' => 'Courses focused on building websites and web applications.',
            ],
            [
                'name' => 'Data Science',
                'description' => 'Courses covering data analysis, machine learning, and AI.',
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Courses for developing mobile applications for Android and iOS.',
            ],
            [
                'name' => 'Cloud Computing',
                'description' => 'Courses on cloud services, deployment, and management.',
            ],
        ];

        //insert or update categories
        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                [
                    'description' => $category['description'],
                ]
            );
        }
    }
}
