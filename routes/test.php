<?php
use App\Models\Employee;
use App\Models\Holiday;







Route::get('attendance/data/{id}',function($id){


    $employee = Employee::find($id);

    return $employee->attendanceByCutOff();

});


Route::get('attendance/{id}',function($id){


    $employee = Employee::find($id);



    return $employee->unprocessedData();

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