<?php


namespace App\Http\Managers;

use App\Models\Employee;
use Carbon\Carbon;

class ReportManager{



    public function index(){

        
        $report  = Employee::with(['attendanceToday'])
        ->whereHas('attendanceToday')
        ->get();

        return $report;

    }

    public function getReportByDate($date){

        $date = Carbon::parse($date);


        $report = Employee::whereDate('timestamp',$date)      
        ->with('attendance')
        ->whereHas('attendance')
        ->get();

        return $report;

    }
}