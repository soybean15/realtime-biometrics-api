<?php

namespace App\Actions\Employee;

use App\Traits\HasSettings;


trait AttendanceType
{

    use HasSettings;
    public function getType($employee, $time)
    {

        $attendance = $employee->attendanceToday()->get();


        $start  = $this->carbonParse($this->getSetting('start_time'));

       
        $end  = $this->carbonParse($this->getSetting('end_time'));


      // return $time;
        if ($attendance->isEmpty()) {
            $afterBreak = $start->addHours(5);
            

            if($time > $afterBreak) return 'Time out';
            
            return "Time in"; 
        }

        $breakStartTime = $start->addHours(3);
        $breakEndTime = $breakStartTime->copy();
        $breakEndTime->addHours(2);
        $attendanceDuringBreak = $attendance->filter(function ($record) use ($breakStartTime, $breakEndTime) {
            $recordTime = $this->carbonParse($record->timestamp);
            return $recordTime->between($breakStartTime, $breakEndTime);
        });

        if ($time->between($breakStartTime, $breakEndTime)) {
            if ($attendanceDuringBreak->isNotEmpty()) {
                return "Break in"; // Attendance exists during the break time, return "Break in"
            }
            return 'Break out';

        } elseif ($time >= $end || $time->diffInHours($start) < 5) {
            return "Time out"; // Punch-in time is after the end time, return "Time out"
        
        }else{
            return 'Invalid';
        }


    }

    protected function carbonParse($time)
    {
        return \Carbon\Carbon::parse($time);
    }

}