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

    public function getReportByCutoff(Request $request){

        $date = $request['date']==null ? Carbon::now() :  $request['date'];

        
      
        return $this->manager->getReport(function () use ($date) {
            $date= $this->manager->calculateCutOff($date);

            $start = $date['startDate'];
            $end= $date ['endDate'];
    
            return [
                'start'=>$start,
                'end'=>$end
            ];
    
        });

    }

    public function getReportByMonth(Request $request){

        $date = $request['date']==null ? Carbon::now() :  Carbon::parse($request['date']);


        return $this->manager->getReport(function () use ($date) {
            
            $start = $date->copy()->firstOfMonth(); 
            $end = $date->copy()->lastOfMonth(); 
    
            return [
                'start'=>$start,
                'end'=>$end
            ];
    
        });


    }

}
