<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Managers\DashboardManager;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    //
    protected DashboardManager $manager;
    public function __construct(DashboardManager $manager){
        $this->manager = $manager;
    }

    public function index() {
        return $this->manager->index();
    }

    public function summary(){
        $date = Carbon::now();
        $year = $date->year;
        $month = $date->month;

        return $this->manager->attendanceRate($year,$month,function($year, $month){
            $date = Carbon::create($year,$month,1);




            return [
                'start'=>$date,
                'end'=>$date->copy()->lastOfMonth(),
                'date'=> $date->format('Y-m-d')
            ];


        });
    }

    public  function getAttendanceDescrepancy(){
        $descrepancy = DailyReport::with('employee')->attendanceDescrepancy()->get();


        return response()->json($descrepancy);
    }
}
