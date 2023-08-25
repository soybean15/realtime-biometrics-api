<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
class EmployeeController extends Controller
{
    //
    public function index(){
        $employees = Employee::paginate(20);

        return response()->json([
            'employees'=> $employees
        ]);
    }
}
