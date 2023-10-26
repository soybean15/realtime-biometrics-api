<?php


namespace App\Http\Managers;

use App\Models\Employee;
use App\Traits\HasSchedule;
use App\Traits\WorkDayChecker;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportManager
{
    use HasSchedule, WorkDayChecker;



    // public function index(){


    //     $report  = Employee::with(['attendanceToday'])
    //     ->whereHas('attendanceToday')
    //     ->get();

    //     return $report;

    // }

    public function getReportByDate($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();

        $report = Employee::with([
            'attendance' => function ($query) use ($date) {
                $query->whereDate('timestamp', $date);
            }
        ])
            ->whereHas('attendance', function ($query) use ($date) {
                $query->whereDate('timestamp', $date);
            })

            ->paginate(20);


        $paginationData = $report->items();
        $lates = 0;


        foreach ($paginationData as $record) {


            foreach ($record->attendance as $attendance) {
                switch ($attendance->type) {
                    case 'Time in': {
                            if (!$record->time_in) {
                                if ($this->isLate($attendance->timestamp)) {
                                    $record->late = true;
                                    $lates++;
                                }
                                $record->time_in = $attendance->timestamp;
                            }

                            break;
                        }
                    case 'Break out': {
                            $record->break_out = $attendance->timestamp;
                            break;

                        }
                    case 'Break in': {
                            $record->break_in = $attendance->timestamp;
                            break;

                        }
                    case 'Time out': {
                            $record->time_out = $attendance->timestamp;
                            break;
                        }

                }

            }
            unset($record->attendance);



        }

        $summary = $this->computeAttendance(
            sizeof($paginationData),
            $lates
        );


        return [
            'reports' => $report,

            'date' => $date->format('Y-m-d'),
            $summary
        ];

    }

    protected function computeAttendance($presents, $lates)
    {

        $count = Employee::withTrashed()->count();
        $absents = $count - $presents;


        $presentPercentage = $count == 0 ? $count : ($presents / $count) * 100;

        $latePercentage = $presents == 0 ? $presents : ($lates / $presents) * 100;

        return [
            'total' => $count,
            'present' => $presents,
            'absents' => $absents,
            'lates' => $lates,
            'late_percentage' => $latePercentage,
            'present_percentage' => $presentPercentage
        ];


    }

    public function getReport($callback)
    {
        
        $start = microtime(true);
        $data = $callback();

        $startDate = $data['start']->copy();
        $endDate = $data['end']->copy();
        $totalLates = 0;
        $totalAttendance = 0;
        $totalAbsents=0;
        
        $totalWorkingDays = $this->getWorkingDays($startDate, $endDate, function ($dateStr)  {
          
            if ($this->isDateActive($dateStr)) {
                return 1;
            }

            return 0;
           
        });

        $report = Employee::with([
            'attendance' => function ($query) use ($data) {
                $query->select(
                    'employee_id',
                    DB::raw('DATE(timestamp) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('MAX(CASE WHEN type = "Time in" THEN timestamp END) as time_in'),
                    DB::raw('MAX(CASE WHEN type = "Break out" THEN timestamp END) as break_out'),
                    DB::raw('MAX(CASE WHEN type = "Break in" THEN timestamp END) as break_in'),
                    DB::raw('MAX(CASE WHEN type = "Time out" THEN timestamp END) as time_out')
                )
                    ->whereBetween('timestamp', [$data['start'], $data['end']])
                    ->groupBy('employee_id', 'date');

            }
        ])->get()
            ->each(function ($employee) use (&$totalLates, &$totalAttendance, $totalWorkingDays,&$totalAbsents) {
                foreach ($employee->attendance as $attendance) {

                    if ($attendance->time_in) {

                        if ($this->isLate($attendance->time_in)) {
                            $totalLates++;
                            $employee->lates++;

                        }

                    }
                }
                $employee->attended = sizeof($employee->attendance);
                $employee->total = $totalWorkingDays;
  
                $employee->absents =  $employee->total - $employee->attended;
                $totalAbsents +=  $employee->absents;

                $employee->late_percentage =   $employee->attended == 0 ? $employee->attended  : ($employee->lates  / $employee->attended ) * 100;
  
                $totalAttendance +=   $employee->attended;

                unset($employee->attendance);             

            });

            $total_employee = sizeof($report);
            $employee_workdays= $total_employee *$totalWorkingDays;
            $absentee_rate =( $totalAbsents /$employee_workdays) *100;



         
            $end = microtime(true);
            $executionTime = ($end - $start) * 1000; 

        return [
            'reports' => $report,
            'total_lates' => $totalLates,
            'dates'=>$data,
            'total_attendance'=>$totalAttendance,
            'speed'=>$executionTime,
            'working_days'=>$totalWorkingDays,
            'total_absents'=> $totalAbsents,
            'total_employee'=> sizeof($report),
            'employee_workdays'=>$employee_workdays,
            'absentee_rate'=>$absentee_rate
        ];


    }

}