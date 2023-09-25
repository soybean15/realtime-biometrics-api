<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;
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
    protected $id = 1;
 
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
            'user_id'=>12,
            'biometrics_id' =>$this->id++


        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Employee $employee) {
          $employee->departments()->attach( mt_rand(1, 20));
          $employee->positions()->attach( mt_rand(1, 5));
       
          // ...
          
          Attendance::create([
              'serial_number' => fake()->uuid(),
              'employee_id' => $employee->id,
              'timestamp' => Carbon::now()->setTime(8, 0), // Set the time to 8:00
              'state' => 1,
              'type' => 255
          ]);
          
          Attendance::create([
              'serial_number' => fake()->uuid(),
              'employee_id' => $employee->id,
              'timestamp' => Carbon::now()->setTime(12, 0), // Set the time to 12:00
              'state' => 1,
              'type' => 255
          ]);
          
          Attendance::create([
              'serial_number' => fake()->uuid(),
              'employee_id' => $employee->id,
              'timestamp' => Carbon::now()->setTime(13, 0), // Set the time to 13:00
              'state' => 1,
              'type' => 255
          ]);
          
          Attendance::create([
              'serial_number' => fake()->uuid(),
              'employee_id' => $employee->id,
              'timestamp' => Carbon::now()->setTime(17, 0), // Set the time to 17:00
              'state' => 1,
              'type' => 255
          ]);
          





          
        });
    }
}
