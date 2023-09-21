<?php

namespace App\Http\Controllers;

use App\Http\Services\ZkTecoService;
use App\Models\Setting;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected ZkTecoService $zk;

    public function __construct(ZkTecoService $zk)
    {
       
        $this->zk = $zk;
       
    }

    public function index(){
        return $this->zk->getAttendance();
    }
    public function getCurrentTime(){


        $settings = Setting::find(1);
        $currentTime = Carbon::now();
        $formattedDate = $currentTime->format('M, j Y, D');
        // Check the value of $fmt
        // if ( == 0) {
        //     // Format the time as "9:00AM" (12-hour format)
        //     $formattedTime = $currentTime->format('h:iA');
        // } else {
        //     // Format the time as "09:00" (24-hour format)
        //     $formattedTime = $currentTime->format('H:i');
        // }
    
    }

    
}
