<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    //
    public function index(){

        return response()->json([
            'positions'=> Position::all()
        ]);
    }
}
