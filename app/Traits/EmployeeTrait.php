<?php

namespace App\Traits;


use App\Models\Employee;

use Illuminate\Support\Facades\Validator;

trait EmployeeTrait
{
    use HasSettings;

    public function generateBiometricsId()
    {
        $existingIds = Employee::pluck('biometrics_id')->toArray();



        $this->biometrics_id = min(array_diff(range(1, count($existingIds) + 1), $existingIds));

        $this->save();


    }
    public function validate($attributes)
    {


        foreach ($attributes as $attribute => $value) {
            switch ($attribute) {
                case 'email':
                    $validationRules[$attribute] = 'required|email';
                    break;

                case 'firstname':
                    $validationRules[$attribute] = 'required|string';
                    break;

                case 'lastname':
                    $validationRules[$attribute] = 'required|string';
                    break;

                case 'contact_number':
                    $validationRules[$attribute] = 'required|regex:/^[0-9]{10}$/';
                    break;

                case 'birthdate':
                    $validationRules[$attribute] = 'required|date|before:today|after:1900-01-01';
                    break;



                // Add more cases for other attributes as needed

                default:
                    return true;

            }
        }

        $validator = Validator::make($attributes, $validationRules);



        if ($validator->fails()) {
            throw new \Exception($validator->errors(), 412);
        } else {
            return true;
        }

    }

}