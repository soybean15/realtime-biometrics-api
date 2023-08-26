<?php

 namespace App\Http\Services;
 use App\Actions\Employee\CreateNewEmployee;

 

class EmployeeService{


    public function store($data){
        
        $create = new CreateNewEmployee();
        return $create->execute($data);

    }
    
}