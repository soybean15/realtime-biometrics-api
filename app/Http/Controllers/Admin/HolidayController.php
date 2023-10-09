<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\HolidayService;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    //
    protected HolidayService $service;
    public  function __construct(HolidayService $service){
        $this->service = $service;

    }
    public function index(){
        $holidays = Holiday::select('month', 'day', 'name', 'category')
        ->orderBy('month')
        ->orderBy('day')
        ->get()
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

        return $this->service->store($request->all());
    }


}
