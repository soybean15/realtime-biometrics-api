<?php

namespace App\Actions\Employee;

use App\Models\Attendance;
use App\Models\Employee;


class CreateAttendance
{


    public function execute($data)
    {

    
            $employee = Employee::where('biometrics_id', $data['id'])->firstOrFail();
            if (!$employee) {
                abort(404, 'Employee not found');
            }

            return Attendance::create([
                'serial_number' => $data['uid'],
                'employee_id' => $employee->id,
                'timestamp' => $data['timestamp'],
                'state' => $data['state'],
                'type' => $data['type']
            ]);
     





    }
}