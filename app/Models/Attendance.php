<?php

namespace App\Models;

use App\Traits\HasSettings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory, HasSettings;
    protected $fillable = [

        'serial_number',
        'employee_id',
        'timestamp',
        'state',
        'type'

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeByMonth(Builder $query, $year, $month)
    {
        return $query->whereYear('created_at', $year);

    }

    public function duration()
    {
        $durationInMinutes = 0;
        if ($this->type == 'Time in') {
            $attendance = Attendance::whereDate('timestamp', '=', \Carbon\Carbon::parse($this->timestamp))->get();

            foreach ($attendance as $record) {
                $timeIn = \Carbon\Carbon::parse($this->timestamp,'UTC');
                $timeOut = \Carbon\Carbon::parse($record->timestamp ,'UTC'); // Assuming 'time_out' is the column name
                if ($record->type == 'Time out') {

                    $durationInMinutes = $timeIn->diffInMinutes($timeOut);
                    break;

                    // Do something with $durationInMinutes
                }
                $endTimeString = $this->getSetting('end_time'); // Assuming it returns "17:00"
                // Concatenate the date part and time part, and then format it as a timestamp
                $timestamp = $timeIn->format('Y-m-d') . ' ' . $endTimeString;
                $timestamp = \Carbon\Carbon::parse($timestamp, 'UTC');

        


                // Combine the date from $timeIn with the time from $endTime     $combinedDateTime = $timeIn->setTime($endTime->hour, $endTime->minute, 0);
                if ($timeIn < $timestamp) {
                    $durationInMinutes = $timeIn->diffInMinutes($timestamp);
                } else {
                    $durationInMinutes = 0; // Set the duration to 0 if $timeIn is later than $timestamp
                }
                
                // Do something with $durationInMinutes


            }
        }

           return $durationInMinutes ==0?10:$durationInMinutes;

        return 'time in:'. $timeIn .'  '. 'timestamp:' . $timestamp;


    }


}