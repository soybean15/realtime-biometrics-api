<?php

 namespace App\Http\Services;
 use App\Actions\Employee\CreateNewEmployee;

 

class EmployeeService{

 
    protected CreateNewEmployee $createNewEmployee;

    public function __construct(CreateNewEmployee $createNewEmployee)
    {
        $this->createNewEmployee = $createNewEmployee;
    }


    public function store($data){

        try{
            return  $this->createNewEmployee->execute($data);
        }catch(\Exception $e) {
            $errors = json_decode($e->getMessage(), true); 
            return response()->json(['errors' => $errors], $e->getCode());
        }

    }
    
}