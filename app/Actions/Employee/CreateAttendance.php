<?php

namespace App\Actions\Employee;

use App\Models\Attendance;
use App\Models\Employee;


class CreateAttendance
{

    use AttendanceType;

    public function execute($data)
    {

    
            $employee = Employee::where('biometrics_id', $data['id'])->firstOrFail();
            if (!$employee) {
                abort(404, 'Employee not found');
            }

         
            
            $time = \Carbon\Carbon::parse($data['timestamp']);
         //  $time = $carbonDateTime->format('h:i A'); for testing

          // return $this->getType($employee ,$time);for testing
           $type= $this->getType($employee ,$time);


            $type =  $this->getType($employee ,$time);
            if($type != 'Invalid'){
               return  Attendance::create([
                    'serial_number' => $data['uid'],
                    'employee_id' => $employee->id,
                    'timestamp' =>\Carbon\Carbon::parse($data['timestamp']) ,
                    'state' => $data['state'],
                    'type' =>$type 
                ]);

            }
            

          //  return $employee;
    


    }
}