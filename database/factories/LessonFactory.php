<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class LessonFactory extends Factory
{


    public function definition(): array
    {
        return [
            'name' => $this->faker->regexify('[a-zA-Z\s_-]{5,20}'),
            // 'description' => $this->faker->lor('[a-zA-Z\s_-]{5,20}'), TODO: Lorem ipsum
            'date' => now()->add($this->faker->randomDigit(), 'days')->startOf('day')->toISOString(),
            'start_time' => now()->setTime(10, 00)->format('H:i'),
            'end_time' => now()->setTime(12, 00)->format('H:i'),
            'is_online' => false,
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
            'url' => ''
        ];
    }

    public function active(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => true,
            ];
        });
    }

    /**
     * Add a group of users to the lesson students list
     */
    public function withStudents($students): Factory
    {
        return $this->state(function (array $attributes) {
            return [];
        })->afterCreating(function (Lesson $lesson) use ($students) {
            $lesson->students()->save($students);
        });
    }
}