<?php

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructors = [
            [
                'name' => 'John Doe',
                'email' => 'john@gmail.com',
                'address' => '123 Main St, City, Country',
                'phone' => '1234567890',
                'image' => 'instructors/1.jpg',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@gmail.com',
                'address' => '456 Elm St, City, Country',
                'phone' => '0987654321',
                'image' => 'instructors/2.jpg',
            ],
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@gmail.com',
                'address' => '789 Oak St, City, Country',
                'phone' => '1122334455',
                'image' => 'instructors/3.jpg',
            ],
        ];
        // Insert or update instructors
        foreach ($instructors as $instructor) {
            Instructor::updateOrCreate(
                ['email' => $instructor['email']],
                [
                    'name' => $instructor['name'],
                    'address' => $instructor['address'],
                    'phone' => $instructor['phone'],
                    'image' => $instructor['image'],
                ]
            );
        }
    }
}
