<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    //

    public function index(){
        return response()->json([
            'departments'=> Department::all()
        ]);
    }
}
