<?php


namespace App\Traits;
use Carbon\Carbon;
use App\Models\Holiday;

trait WorkDayChecker{


    public function isDateActive($date){
        $carbonDate = Carbon::parse($date);
        $holidays = Holiday::where('day', $carbonDate->day)
        ->where('month', $carbonDate->month)
        ->get();


        $isWeekend = in_array($carbonDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

     
        return !($isWeekend || !$holidays->isEmpty());



    }

}
