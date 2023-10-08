<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    //
    public function index(){
        $holidays = Holiday::select('month', 'day', 'name', 'category')
        ->orderBy('month')
        ->orderBy('day')
        ->get()
        ->groupBy(function ($holiday) {
            return $holiday->month . '-' . $holiday->day;
        });

        return response()->json([
            'holidays'=>$holidays
        ]);



    }
}
