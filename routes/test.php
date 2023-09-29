<?php
use App\Models\Employee;







Route::get('attendance/cutoff',function(){


    $employee = Employee::find(2);

    return $employee->attendanceByCutOff();

});