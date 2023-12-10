<?php
use App\Http\Controllers\Admin\EmployeeController;

use App\Http\Controllers\Admin\ReportController;
use App\Http\Managers\DashboardManager;
use App\Http\Managers\EmployeeManager;
use App\Http\Managers\ReportManager;
use App\Models\Employee;
use App\Models\Holiday;
use Illuminate\Support\Carbon;







Route::get('attendance/data/{id}',function($id){


    $employee = Employee::find($id);

    return $employee->attendanceByCutOff();

});


Route::get('attendance/{id}',function($id){


    $employee = Employee::find($id);



    return $employee->summarizeDaily();

});

Route::get('attendance/summary/{id}',function($id){
    $employee = Employee::find($id);

    return $employee->getAttendanceSummary(1,30,10,2023);

});


Route::get('holiday',function(){

    $holidays = Holiday::select('month', 'day', 'name', 'category')
    ->orderBy('month')
    ->orderBy('day')
    ->get()
    ->groupBy(function ($holiday) {
        return $holiday->month . '-' . $holiday->day;
    });


    return $holidays;


    
});

Route::get('pdf/{method}/{id}',[EmployeeController::class,'getAttendanceByCutOffPDF']);

Route::get('report/today',[ReportController::class,'index']);

Route::get('holiday/check',function(){

    $manager = new ReportManager();

    return $manager->isDateActive('2023-11-04');


});

Route::get('schedule/check',function(){

    $manager = new ReportManager();

    return $manager->isLate('2023-10-23 08:02:00');

});

Route::get('reports',function(){

    $manager = new ReportManager();
    $date = '2023-10-23';

    return $manager->getReport(function () use ($date) {
        $date = Carbon::parse($date);
        $start = $date->copy()->firstOfMonth(); // get the 1st day of the month
        $end = $date->copy()->lastOfMonth(); // get the last day of the month

        return [
            'start'=>$start,
            'end'=>$end
        ];

    });

});

Route::get('dashboard',function(){
    $manager = new DashboardManager();

    return $manager->attendanceRate( 2023,10,function ($year,$month){

        $date = Carbon::create($year,$month,1);




        return [
            'start'=>$date,
            'end'=>$date->copy()->lastOfMonth(),
            'date'=> $date->format('Y-m-d')
        ];

    });

});

Route::get('pdf',function(){


   



});