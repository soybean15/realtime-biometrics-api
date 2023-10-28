<?php

namespace App\Http\Managers;
use App\Models\Employee;
use App\Models\User;

class DashboardManager{


    public function index(){

        $employeeCount = Employee::count();
        $userCount = User::count();


        return response()->json([
            'employee_count'=> $employeeCount,
            'user_count'=>$userCount
        ]);





        

    }



    




}