<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Services\EmployeeService;
use Illuminate\Http\Request;
use App\Models\Employee;
class EmployeeController extends Controller
{
    //

    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }


    public function index(){
        $employees = Employee::with(['departments', 'positions','user'])->orderBy('created_at', 'desc')->paginate(20);
        $trashed = Employee::onlyTrashed()->paginate(10);
   

        return response()->json([
            'employees'=> $employees,
            'trashed'=>$trashed,
        ]);
    }

    public function get(String $id){

        return $this->employeeService->getEmployee($id);

    }

    public function store(Request $request){


        $id = $this->employeeService->store($request->all(),$request->file('image'))->id;
        $employee =  Employee::find($id);
        
        $employee->load(['departments', 'positions','user']);
        return response()->json([
           
            'employee'=>$employee
        

        ]);
    }

    public function update(Request $request){

        return response()->json([
            'request'=> [$request['attribute']=>$request['value']],
            'employee' => $this->employeeService->update([$request['attribute']=>$request['value']],$request['id'])
        ]);
    }

    public function delete(Request $request){

        return $this->employeeService->delete($request['id']);

    }

    public function restore(Request $request){
        return $this->employeeService->restore($request['id']);

    }

   
}
