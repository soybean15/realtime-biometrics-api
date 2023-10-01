<?php
use App\Models\Employee;







Route::get('attendance',function(){


    $employee = Employee::find(2);

    return $employee->unprocessedData();

});


Route::get('attendance/{id}',function($id){


    $employee = Employee::find($id);



    return $employee->summarizeDaily();

});