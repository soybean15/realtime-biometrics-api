<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'employee_id' => date('y') . '-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT) . '-' . date('m'),
            'firstname'=>fake()->firstName(),
            'lastname'=>fake()->lastName(),
            'middlename'=>fake()->lastName(),
            'birthdate'=>fake()->dateTimeThisDecade(),
            'gender'=>fake()->randomElement(['Male', 'Female']),
            'contact_number'=> fake()->phoneNumber(),
            'email'=>fake()->email(),
            'address'=>fake()->address() ,
            'user_id'=>12


        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Employee $employee) {
          $employee->departments()->attach( mt_rand(1, 20));
          $employee->positions()->attach( mt_rand(1, 5));
        });
    }
}
