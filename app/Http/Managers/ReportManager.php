<?php


namespace App\Http\Managers;

use App\Models\Employee;
use Carbon\Carbon;

class ReportManager{



    // public function index(){

        
    //     $report  = Employee::with(['attendanceToday'])
    //     ->whereHas('attendanceToday')
    //     ->get();

    //     return $report;

    // }

    public function getReportByDate($date=null){
        if($date==null){

            $date = Carbon::now();

        }else{
            $date = Carbon::parse($date);

        }

     

        $report = Employee::with(['attendance'=>function ($query) use ($date){
            $query->whereDate('timestamp', $date);
        }]) 
        ->whereHas('attendance',function($query) use ($date){
            $query->whereDate('timestamp', $date);
        })
       
        ->get();

        return $report;

    }
}