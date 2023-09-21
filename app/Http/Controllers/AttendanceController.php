<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    //
    public function index(){

        $attendance = Attendance::with(['employee'])->get();

        $attendance->load(['employee']); 
        return response()->json([
            'attendance'=> $attendance
        ]);

    }
    
}
