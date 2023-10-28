<?php

namespace App\Http\Managers;

use App\Models\Employee;
use App\Models\User;
use App\Traits\HasSchedule;
use App\Traits\WorkDayChecker;
use Illuminate\Support\Facades\DB;

class DashboardManager
{

    use HasSchedule, WorkDayChecker;
    public function index()
    {
        $userCount = User::query()->count();
        $employeeCount = Employee::query()->count();


        return response()->json([
            'employee_count' => $employeeCount,
            'user_count' => $userCount
        ]);


    }

    public function attendanceRate($callback)
    {

        $dates = $callback();
        $total_lates = 0;
        $total_attendance = 0;
        $departments = [];

        $data = Employee::select('id')->with([
            'attendance' => function ($query) use ($dates) {
                $query->select(
                    'employee_id',
                    DB::raw('DATE(timestamp) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('MAX(CASE WHEN type = "Time in" THEN timestamp END) as time_in'),
                    DB::raw('MAX(CASE WHEN type = "Break out" THEN timestamp END) as break_out'),
                    DB::raw('MAX(CASE WHEN type = "Break in" THEN timestamp END) as break_in'),
                    DB::raw('MAX(CASE WHEN type = "Time out" THEN timestamp END) as time_out')
                )
                    ->whereYear('timestamp', $dates['year'])
                    ->whereMonth('timestamp', $dates['month'])
                    ->groupBy('employee_id', 'date');

            }
            ,
            'departments'
        ])->get()
            ->each(function ($employee) use (&$total_lates, &$total_attendance, &$departments) {

                $employeeLates = 0;
              
                foreach ($employee->attendance as $attendance) {

                    if ($attendance->time_in) {

                        if ($this->isLate($attendance->time_in)) {
                            $total_lates++;

                            $employeeLates++;

                        }

                    }
                }

               $attended  = sizeof($employee->attendance);

        
                foreach ($employee->departments as $department) {
                    $departmentName = $department['name'];

                    if (!isset($departments[$departmentName])) {
                        // Initialize the 'lates' array for the department if it doesn't exist
                        $departments[$departmentName] = ['lates' => 0,'attendance' => 0];
        
                    }

                    // Append the 'lates' data to the department's array
                    $departments[$departmentName]['lates'] +=  $employeeLates;
                    $departments[$departmentName]['attendance'] +=  $attended;
                }
               
                $total_attendance += $attended;


            });

        $total_employee = sizeof($data);

        return [
            'dates' => $dates,
            'total_employee' => $total_employee,
            'total_lates' => $total_lates,
            'total_attendance' => $total_attendance,
            'departments' => $departments,
         

        ];

    }








}