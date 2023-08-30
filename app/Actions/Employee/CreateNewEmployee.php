<?php

namespace App\Actions\Employee;
use App\Models\Employee;

use Illuminate\Support\Facades\Validator;

class CreateNewEmployee  

{


    public function execute($data, $file){

    
        $validator = Validator::make($data, [
            'firstname' => 'required|max:255',
            'lastname' => 'required|max:255',
            'gender' => 'required|in:Male,Female', // Example validation for gender
            'birthdate' => 'required|date',
            // Add more validation rules as needed
        ]);

        if ($validator->fails()) {
            throw new \Exception(json_encode($validator->errors()), 410);
            //return response(['errors' => $validator->errors()], 403);
        }
        
        $employee = Employee::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'middlename' => $data['middlename'],
            'gender' => $data['gender'],
            'birthdate' => $data['birthdate'],
            'contact_number' => $data['contact_number'],
            'email' => $data['email'],
            'address' => $data['address'],
        ]);


        if ($file) {
            $employee->storeImage('images/users', $file);
        }

       return $employee;
    }

}