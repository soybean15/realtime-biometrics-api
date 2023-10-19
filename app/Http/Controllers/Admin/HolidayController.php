<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Managers\HolidayManager;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HolidayController extends Controller
{
    //
    protected HolidayManager $manager;
    public  function __construct(HolidayManager $manager){
        $this->manager = $manager;

    }
    public function index(){
        $holidays = Holiday::select('id','month', 'day', 'name', 'category')
        ->orderBy('month')
        ->orderBy('day')
        ->get()
        ->each(function($record){
            $currentYear = date('Y');
            $temp = $record->holidayTemp()//hasOne
            ->whereYear('date',$currentYear)
            ->get();

            if(!$temp->isEmpty()){
                $date = Carbon::parse($temp[0]->date);

                //date is 2023-11-02
    
                $record->day = $date->day;
                $record->month = $date->month;
              
            }

        })

        ->groupBy(function ($holiday) {
            $currentYear = date('Y');
            $month = str_pad($holiday->month, 2, '0', STR_PAD_LEFT);
            $day = str_pad($holiday->day, 2, '0', STR_PAD_LEFT);
            return $currentYear . '/' . $month . '/' . $day;
        });
     
        return response()->json([
            'holidays'=>$holidays
        ]);



    }

    public function store(Request $request){

        return $this->manager->store($request->all());
    }

    public function move(Request $request){


        return $this->manager->moveHoliday($request->all());
    }

}
