<?php

namespace App\Traits;
use \Carbon\Carbon;
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

 
    private function getWorkingDays($start, $end, $callback)
    {

      
        $total=0;
        while ($start <= $end) {
          
            $dateStr = $start->format('Y-m-d');
            $total=$callback($dateStr);

            if ($start->isSameDay(Carbon::now())) {
                break;
            }
            $start->addDay();
        }

        return $total;

        

    }
    
}