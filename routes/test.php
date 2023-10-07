<?php
use App\Models\Employee;







Route::get('attendance/data/{id}',function($id){


    $employee = Employee::find($id);

    return $employee->attendanceByCutOff();

});


Route::get('attendance/{id}',function($id){


    $employee = Employee::find($id);



    return $employee->unprocessedData();

});