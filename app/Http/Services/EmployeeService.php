<?php

 namespace App\Http\Services;
 use App\Actions\Employee\CreateNewEmployee;
 use App\Actions\Employee\DeleteEmployee;


 

class EmployeeService{

 
    protected CreateNewEmployee $createNewEmployee;
    protected DeleteEmployee $deleteEmployee;

    public function __construct(CreateNewEmployee $createNewEmployee, DeleteEmployee $deleteEmployee)
    {
        $this->createNewEmployee = $createNewEmployee;
        $this->deleteEmployee = $deleteEmployee;
    }


    public function store($data){

        try{
            return  $this->createNewEmployee->execute($data);
          
        }catch(\Exception $e) {
            $errors = json_decode($e->getMessage(), true); 
            return response()->json(['errors' => $errors], $e->getCode());
        }

    }

    public function delete($employee_id){


        try{
            return $this->deleteEmployee->execute($employee_id);
         
        }catch(\Exception $e) {
          
            return response()->json(['errors' => $e->getMessage()], $e->getCode());
        }
       
          
      

    }
    
}