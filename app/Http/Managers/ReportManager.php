<?php


namespace App\Http\Managers;

use App\Models\Employee;
use App\Traits\HasSchedule;
use Carbon\Carbon;

class ReportManager
{
    use HasSchedule;



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
                                if($this->isLate($attendance->timestamp)) {
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
            'reports'=>$report,
            'lates'=>$lates,  
            'date'=>Carbon::now()->format('Y-m-d'),
                $summary
        ];

    }

    protected function computeAttendance($presents,$lates){

        $count = Employee::withTrashed()->count();
        $absents = $count - $presents;

        $presentPercentage = ($presents / $count) * 100;
    
        $latePercentage = ($lates / $presents) *100;

        return [
            'total'=>$count,
            'present'=>$presents,
            'absents'=>$absents,
            'late_percentage'=>$latePercentage,
            'present_percentage'=>$presentPercentage
        ];


    }
}