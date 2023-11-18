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

    public function attendanceRate($year, $month, $callback)
    {

        
        $start = microtime(true);

        $data = [];
         
       


        for ($i = 12; $i > 0; $i--) {
            $_date = $callback($year, $month);
            $date = $_date['date'];
            $departments = [];


            $totalWorkingDays = $this->getWorkingDays($_date['start'], $_date['end'], function ($dateStr)  {
          
                if ($this->isDateActive($dateStr)) {
                    return 1;
                }
    
                return 0;
               
            });

            $total_lates = 0;
            $total_attendance = 0;
            $total_absent =0;
            if ($month == 0) {
                $month = 12;
                $year--;
            }


            $record = Employee::select('id')->with([
                'attendance' => function ($query) use ($year, $month) {
                    $query->select(
                        'employee_id',
                        DB::raw('DATE(timestamp) as date'),
                        DB::raw('COUNT(*) as count'),
                        DB::raw('MAX(CASE WHEN type = "Time in" THEN timestamp END) as time_in'),
                        DB::raw('MAX(CASE WHEN type = "Break out" THEN timestamp END) as break_out'),
                        DB::raw('MAX(CASE WHEN type = "Break in" THEN timestamp END) as break_in'),
                        DB::raw('MAX(CASE WHEN type = "Time out" THEN timestamp END) as time_out')
                    )
                        ->whereYear('timestamp', $year)
                        ->whereMonth('timestamp', $month)
                        ->groupBy('employee_id', 'date');

                }
                ,
                'departments'
            ])->get()
                ->each(function ($employee) use (&$total_lates,&$total_absent, &$total_attendance, &$departments,&$totalWorkingDays) {

                    $employeeLates = 0;

                    foreach ($employee->attendance as $attendance) {

                        if ($attendance->time_in) {

                            if ($this->isLate($attendance->time_in)) {
                                $total_lates++;

                                $employeeLates++;

                            }

                        }
                    }

                    $attended = sizeof($employee->attendance);


                    foreach ($employee->departments as $department) {
                        $departmentName = $department['name'];

                        if (!isset($departments[$departmentName])) {
                            // Initialize the 'lates' array for the department if it doesn't exist
                            $departments[$departmentName] = ['lates' => 0, 'attendance' => 0,'absents'=>0];

                        }

                        // Append the 'lates' data to the department's array
                        $departments[$departmentName]['lates'] += $employeeLates;
                        $departments[$departmentName]['attendance'] += $attended;
                        $departments[$departmentName]['absents'] += ($totalWorkingDays - $attended );
                    }

                    $total_attendance += $attended;
                    $total_absent= ($totalWorkingDays - $attended ) +$total_absent;


                });

                $total_employee = sizeof($record);


                $employee_workdays= $total_employee *$totalWorkingDays;
                $absentee_rate =( $total_absent /$employee_workdays) *100;
          
            if (!isset($data[$date])) {
                $data[$date] = [];
            }

            $data[$date] = [
                'year' => $year,
                'month' => $month,
                'total_employee' => $total_employee,
                'total_lates' => $total_lates,
                'total_attendance' => $total_attendance,
                'departments' => $departments,
                'total_working_days'=>$totalWorkingDays,
                'absentee_rate'=>$absentee_rate,
                'total_absent'=>$total_absent


            ];

            $month--;

        }

        $end = microtime(true);
        $executionTime = ($end - $start) * 1000; 


        return response()->json([
            'data'=> $data,
            'speed'=>$executionTime,
            'date'=>$date
        ]);
    }








}