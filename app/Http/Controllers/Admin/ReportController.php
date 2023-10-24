<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Managers\ReportManager;
use Illuminate\Http\Request;
use \Carbon\Carbon;

class ReportController extends Controller
{
    //

    protected ReportManager $manager;
    public function __construct(ReportManager $manager){

        $this->manager = $manager;

    }


    public function getReportByDate(Request $request){
      
        
        $lastActive = Carbon::parse($request->date);
        //this line gets the previous working day for comparison
        while (true) {        
            $lastActive->subDay();
            if ($this->manager->isDateActive($lastActive)) {
                break;
            }
        }

        return response()->json([
            'active'=> $this->manager->getReportByDate($request['date']),//<-this method have some heavy process
            'previous'=>  $this->manager->getReportByDate($lastActive),

        ]) ;

        // return response()->json([
        //      $this->manager->getReportByDate($request['date']),//<-this method have some heavy process
        // ]) ;
       
       



    }

}
