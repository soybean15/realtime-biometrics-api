<?php


namespace App\Traits;

use App\Models\HolidayTemp;

use App\Models\Holiday;
use Illuminate\Support\Carbon;

trait WorkDayChecker
{


    public function isDateActive($date)
    {

        if ($date instanceof Carbon) {
            $carbonDate = $date;
        } else {
            $carbonDate = Carbon::parse($date);
        }



    


        $holidays = Holiday::where('day', $carbonDate->day)
            ->where('month', $carbonDate->month)
            ->whereIn('category', ['Regular Holidays', 'Special (Non-Working) Holidays'])
            ->doesntHave('holidayTemp')
            ->get();

            $temp = HolidayTemp::where('date', $carbonDate)->get();


        $isWeekend = in_array($carbonDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);
   //  return !( !$holidays->isEmpty() || !$temp->isEmpty());

         return !($isWeekend || !$holidays->isEmpty() || !$temp->isEmpty());

        // return response()->json([
        //     'date' => $carbonDate,
        //     'raw_date' => $date,
        //     'holiday' => $holidays,
        //     'day'=>$carbonDate->day,
        //     'month'
        // ]);



    }

}
