<?php

namespace App\Actions\Employee;

use App\Models\Employee;


class DeleteEmployee
{

    public function execute($employee_id)
    {

        $employee = Employee::where('id', $employee_id)->first();


        if ($employee) {
            $employee->delete(); 
            return response()->json(['message' => 'Employee Deleted']);
        } else {
            throw new \Exception('Something went wrong', 411);
        }







    }

}