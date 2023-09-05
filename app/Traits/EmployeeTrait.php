<?php

namespace App\Traits;
use App\Models\Employee;

trait EmployeeTrait
{

    public function generateBiometricsId(){
        $existingIds = Employee::pluck('biometrics_id')->toArray();

       

        $this->biometrics_id =  min(array_diff(range(1, count($existingIds) + 1), $existingIds));

        $this->save();
        

    }
   
}
