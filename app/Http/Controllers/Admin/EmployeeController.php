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
        $employees = Employee::paginate(20);

        return response()->json([
            'employees'=> $employees
        ]);
    }

    public function store(Request $request){

        return $this->employeeService->store($request->all()); 

        
    }
}
