<?php

namespace App\Traits;
use \Carbon\Carbon;
trait HasSchedule{

    use HasSettings;




    public function isLate($time) {
        $start = $this->getSetting('start_time'); // 08:00
    
        // Create Carbon instances from the time strings
        $startTime = Carbon::parse($start);
        $actualTime = Carbon::parse($time);
    
        // Compare the actual time with the start time
        if ($actualTime->greaterThan($startTime)) {
            // Employee is late
            return true;
        } else {
            // Employee is not late
            return false;
        }
    }
    
}