<?php

namespace App\Models;

use App\Traits\HasSettings;
use Carbon\Carbon;
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

    public function scopeByCutOff(Builder $query)
{
      // Set the timezone to your application's timezone
  //    $timezone = config('app.timezone');
     // $currentDate = Carbon::parse('2023-09-01');
     $currentDate = Carbon::now();
      // Create separate Carbon instances for start and end dates
      $startDate = $currentDate->copy();
      $endDate = $currentDate->copy();
  
      if ($currentDate->day > 15) {
          // If it's after the 15th, get data from the current month
          $startDate->startOfMonth()->day(16);
          $endDate->endOfMonth();
      } else {
          // If it's on or before the 15th, get data from the previous month
          $startDate->subMonth();
          $endDate->subMonth()->endOfMonth()->day(15);
      }
  

    // return response()->json([
    //     'current' => $currentDate->toDateTimeString(),
    //     'start' => $startDate->toDateTimeString(),
    //     'end' => $endDate->toDateTimeString(),
    // ]);
     return $query->whereBetween('created_at', [$startDate, $endDate]);
}


    public function duration()
    {
        $durationInMinutes = 0;
        if ($this->type == 'Time in' || $this->type == 'Break in' ) {
            $attendance = Attendance::whereDate('timestamp', '=', Carbon::parse($this->timestamp))->get();

            foreach ($attendance as $record) {
                $timeIn = Carbon::parse($this->timestamp, 'UTC');
                $timeOut = Carbon::parse($record->timestamp, 'UTC');
                if ($this->type == 'Time in' &&  $record->type == 'Break out' ) {

                    $durationInMinutes = $timeIn->diffInMinutes($timeOut);
                    break;
                }

                if ($this->type == 'Break in' && $record->type == 'Time out' ) {

                    $durationInMinutes = $timeIn->diffInMinutes($timeOut);
                    break;
                }
                $endTimeString = $this->getSetting('end_time');

                $timestamp = $timeIn->format('Y-m-d') . ' ' . $endTimeString;
                $timestamp =Carbon::parse($timestamp, 'UTC');

                if ($timeIn < $timestamp) {
                    $durationInMinutes = $timeIn->diffInMinutes($timestamp);
                } else {
                    $durationInMinutes = 0;
                }

            }
        }

        return $durationInMinutes == 0 ? 10 : $durationInMinutes;

    }


}