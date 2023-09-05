<?php

 namespace App\Http\Services;
 use App\Actions\Employee\CreateNewEmployee;
 use App\Actions\Employee\DeleteEmployee;
 use App\Models\Employee;


 

class EmployeeService{

 
    protected CreateNewEmployee $createNewEmployee;
    protected DeleteEmployee $deleteEmployee;

    public function __construct(CreateNewEmployee $createNewEmployee, DeleteEmployee $deleteEmployee)
    {
        $this->createNewEmployee = $createNewEmployee;
        $this->deleteEmployee = $deleteEmployee;
    }


    public function getEmployee($id){


        try{
            
            $employee = Employee::find($id);
            $employee->load(['departments', 'positions','user']);
            return response()->json(['employee' => $employee]);

        }catch(\Exception $e){
            return response()->json(['error' => "Employee Cannot found"], 418);
        }

    }


    public function store($data,$file){

        try{
            return  $this->createNewEmployee->execute($data,$file);
          
        }catch(\Exception $e) {
            if($e->getCode()==410){
                $errors = json_decode($e->getMessage(), true); 
                return response()->json(['errors' => $errors], $e->getCode());
            }
            return response()->json($e->getMessage(), 501);
          
        }

    }

    public function delete($employee_id){

        try{
            return $this->deleteEmployee->execute($employee_id);
         
        }catch(\Exception $e) {
          
            return response()->json(['errors' => $e->getMessage()], $e->getCode());
        }  
      

    }

    public function update($attributes,$employee_id ){
        $employee = Employee::find($employee_id);
        if (!$employee) {
            return null;// will throw an error
        }
    
        foreach ($attributes as $attribute => $value) {
            $employee->$attribute = $value;
        }
    
        $employee->save();
    

        return $employee;

    }

    public function restore($id){


        $employee = Employee::onlyTrashed()->find( $id);

        if ($employee) {
            $employee->restore(); 
            $employee->generateBiometricsId();

            return response()->json(['message' => 'Employee Restored','employee'=>$employee]);
        } else {
            throw new \Exception('Employee not found in the trash', 404);
        }
    }

    public function upload($id, $file){

        $employee = Employee::find($id);


        $employee->load(['departments', 'positions','user']);


        if ($file) {
            $employee->restoreImage('images/users', $file);
        }

       

        return response()->json([
            'image'=> $employee->image,

        ]);
    }


    
}