<?php

namespace App\Actions\Employee;

use App\Traits\HasSettings;


trait AttendanceType
{

    use HasSettings;
    public function getType($employee, $time)
    {
        // Get the attendance records for today for the given employee
        $attendance = $employee->attendanceToday()->get();

        // $endTime = $this->carbonParse($this->getSetting('end_time'));

        // return 'time '. $time . ' endTime ' . $endTime;
        //  return  $attendance->count();

        // Check if there are no attendance records for today
        if ($attendance->isEmpty()) {
            return "Time in"; // No attendance records, return "Time in"
        }


        // Assuming you have an 'end_time' property in your settings
        // Parse the provided time


        // Define the break time range
        $breakStartTime = $this->carbonParse('12:00 PM');
        $breakEndTime = $this->carbonParse('1:00 PM');
        $attendanceDuringBreak = $attendance->filter(function ($record) use ($breakStartTime, $breakEndTime) {
            $recordTime = $this->carbonParse($record->timestamp);
            return $recordTime->between($breakStartTime, $breakEndTime);
        });

        if ($time->between($breakStartTime, $breakEndTime)) {
            if ($attendanceDuringBreak->isNotEmpty()) {
                return "Break in"; // Attendance exists during the break time, return "Break in"
            }
            return 'Break Out';

        } elseif ($time > $this->carbonParse($this->getSetting('end_time'))) {
            return "Time out"; // Punch-in time is after the end time, return "Time out"
        }else  return "Break in";


    }

    protected function carbonParse($time)
    {
        return \Carbon\Carbon::parse($time);
    }

}