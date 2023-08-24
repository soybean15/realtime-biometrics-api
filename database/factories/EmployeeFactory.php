<?php

namespace Database\Factories;

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
            'firstname'=>fake()->firstName(),
            'lastname'=>fake()->lastName(),
            'middlename'=>fake()->lastName(),
            'birthdate'=>fake()->dateTimeThisDecade(),
            'contact_number'=> fake()->phoneNumber(),
            'email'=>fake()->email(),
            'address'=>fake()->address()


        ];
    }
}
