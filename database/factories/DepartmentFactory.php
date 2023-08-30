<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */


class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $departmentIndex = 0;
    public function definition(): array
    {

        $departments = [
            "Computer Science",
            "Mathematics",
            "Physics",
            "Chemistry",
            "Biology",
            "English Literature",
            "History",
            "Psychology",
            "Economics",
            "Political Science",
            "Sociology",
            "Business Administration",
            "Engineering (Mechanical)",
            "Engineering (Electrical)",
            "Civil Engineering",
            "Architecture",
            "Fine Arts",
            "Music",
            "Environmental Science",
            "Linguistics"
        ];
        $currentDepartment = $departments[$this->departmentIndex];

        // Increment the counter and wrap around if it exceeds the department count
        $this->departmentIndex = ($this->departmentIndex + 1) % count($departments);

        return [
            'name' => $currentDepartment
            
        ];
    }
}
