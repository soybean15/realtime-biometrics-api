<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Managers\ReportManager;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    //

    protected ReportManager $manager;
    public function __construct(ReportManager $manager){

        $this->manager = $manager;

    }

    public function index(){

     $reports = $this->manager->index();


     return response()->json([

        'reports'=>$reports
     ]);
    }

    public function getReportByDate(Request $request){

        $this->manager->getReportByDate($request['date']);



    }

}
