<?php


namespace App\Http\Managers;

use App\Models\Employee;
use Carbon\Carbon;

class ReportManager{



    public function reportByDate(){

        
        $report  = Employee::with(['attendanceToday'])
        ->whereHas('attendanceToday')
        ->get();

        return $report;

    }
}