<?php

namespace App\Traits;
use Illuminate\Support\Carbon;

trait HasSchedule{

    use HasSettings,WorkDayChecker;




    public function isLate($time) {
        $start = $this->getSetting('start_time'); // 08:00
    
        // Create Carbon instances from the time strings
        $startTime = Carbon::parse($start);
        $actualTime = Carbon::parse($time);
    

        return $actualTime->format('His.u') > $startTime->format('His.u');
        // return response()->json([
        //     'result'=> $actualTime->format('His.u') > $startTime->format('His.u'),
        //     'startTime'=>$startTime,
        //     'actualTime'=>$actualTime->toTimeString(),
        //     'time'=>$time,
        //     'start'=>$start

        // ]);
    

    }



    public function calculateCutOff($date)
    {
     //   $currentDate = Carbon::parse($date);
     if (!$date instanceof Carbon) {
        $date = Carbon::parse($date);
    }


     
        $day = $date->day;
        $endOfMonth = $date->endOfMonth()->day;


        if ($day <= 15) {
            $date->setDay(1); // Set the day to 1st day of the month
         
            $endDate = $date->copy();
            $endDate->setDay(15);

            return [
            'start' => 1, 
            'end' => 15, 
            'startDate' =>$date,
             'endDate'=> $endDate,
            ];
            
        } else {
            $date->setDay(16); // Set the day to 16th day of the month
             $endDate = $date->copy();
             $endDate->setDay($endOfMonth);
            return [
                'start' => 16, 
                'end' => $endOfMonth, 
                'startDate' => $date,
                'endDate'=> $endDate,
              
            ];
        }
    }
 
    public function getWorkingDays($start, $end, $callback)
    {

      


        // $_end = $end->copy();   
        $total=0;
        while ($start <= $end) {
          
            $dateStr = $start->format('Y-m-d');
            $total+=$callback($dateStr);

            if ($start->isSameDay(Carbon::now())) {
                break;
            }
            $start->addDay();
        }

        return $total;

        

    }
    
}