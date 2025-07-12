<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couses = [
            [
                'name' => 'Introduction to Programming',
                'course_code' => 'CS101',
                'description' => 'Learn the basics of programming using Python.',
                'price' => 100.00,
                'time_description' => '3 months',
                'syllabus' => 'syllabus/intro_to_programming.pdf',
                'credits' => 3,
                'instructor_id' => 1,
                'category_id' => 1,
                'image' => 'courses/intro_to_programming.jpg',
                'time_stamps' => [
                    ['day' => 'Monday', 'start_time' => '10:00:00', 'end_time' => '12:00:00'],
                    ['day' => 'Wednesday', 'start_time' => '10:00:00', 'end_time' => '12:00:00'],
                ],
            ],
            [
                'name' => 'Web Development Fundamentals',
                'course_code' => 'WD101',
                'description' => 'A comprehensive course on HTML, CSS, and JavaScript.',
                'price' => 150.00,
                'time_description' => '4 months',
                'syllabus' => 'syllabus/web_dev_fundamentals.pdf',
                'credits' => 4,
                'instructor_id' => 2,
                'category_id' => 2,
                'image' => 'courses/web_dev_fundamentals.jpg',
                'time_stamps' => [
                    ['day' => 'Tuesday', 'start_time' => '14:00:00', 'end_time' => '16:00:00'],
                    ['day' => 'Thursday', 'start_time' => '14:00:00', 'end_time' => '16:00:00'],
                ],
            ],
            [
                'name' => 'Data Science with Python',
                'course_code' => 'DS101',
                'description' => 'Explore data analysis, visualization, and machine learning using Python.',
                'price' => 200.00,
                'time_description' => '5 months',
                'syllabus' => 'syllabus/data_science_python.pdf',
                'credits' => 5,
                'instructor_id' => 3,
                'category_id' => 3,
                'image' => 'courses/data_science_python.jpg',
                'time_stamps' => [
                    ['day' => 'Friday', 'start_time' => '09:00:00', 'end_time' => '11:00:00'],
                    ['day' => 'Saturday', 'start_time' => '09:00:00', 'end_time' => '11:00:00'],
                ],
            ]
        ];
        // Insert or update courses
        foreach ($couses as $course) {
            $courseDB = Course::updateOrCreate(
                ['course_code' => $course['course_code']],
                [
                    'name' => $course['name'],
                    'description' => $course['description'],
                    // 'price' => $course['price'],
                    'time_description' => $course['time_description'],
                    'syllabus' => $course['syllabus'],
                    'credits' => $course['credits'],
                    'instructor_id' => $course['instructor_id'],
                    'category_id' => $course['category_id'],
                    'image' => $course['image'],
                ]
            );

            // Insert or update time stamps
            if (isset($course['time_stamps'])) {
                foreach ($course['time_stamps'] as $time_stamp) {
                    $courseDB->timeStamps()->updateOrCreate(
                        ['day' => $time_stamp['day']],
                        [
                            'start_time' => $time_stamp['start_time'],
                            'end_time' => $time_stamp['end_time'],
                        ]
                    );
                }
            }
        }
    }
}
