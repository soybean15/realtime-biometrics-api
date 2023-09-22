<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Traits\HasSettings;
use Illuminate\Http\Request;
use Carbon\Carbon;
class SettingsController extends Controller
{
    use HasSettings;
    public function index(){
        $retrievedSetting = Setting::find(1); // Assuming you've stored a setting with ID 1
        $retrievedData = $retrievedSetting->data;

        return  $retrievedData;
    }
    public function changeColor(Request $request){
        $primary = $request->input('primary');
       
    
        $setting = Setting::find(1); 
    
        
        $data = $setting->data;
    
      
        $data['theme']['primary'] = $primary;
    
        
        $setting->data = $data;
        $setting->save();

        return  $setting->data;

        
    }

    public function updateSettings(Request $request){
        return $this->updateSetting($request['key'], $request['value']);
    }


    public function getCurrentDateTime(){


  
        $currentTime = Carbon::now();
        $formattedDate = $currentTime->format('M, j Y, D');
     
        if ( $this->getSetting('24hrs_format')) {
            // Format the time as "9:00AM" (12-hour format)
            $formattedTime = $currentTime->format('H:i');
            
        } else {
            // Format the time as "09:00" (24-hour format)
            $formattedTime = $currentTime->format('h:iA');
        }
    

        return response()->json([
            'time'=>  $formattedTime,
            'date'=>$formattedDate
        ]);


    }
}
