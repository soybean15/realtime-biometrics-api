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
        $value = $request->input('value');
        $key = $request->input('key');
       
    
        $setting = Setting::find(1); 
    
        
        $data = $setting->data;
    
      
        $data['theme'][$key] = $value;
    
        
        $setting->data = $data;
        $setting->save();

        return  $setting->data;

        
    }

    public function updateSettings(Request $request){
        return $this->updateSetting($request['key'], $request['value']);
    }

    // public function updateTimeFormat(Request $request){
    //     $this->updateSetting('24hrs_format', $request['value']);
    // }
    // }


    public function getCurrentDateTime(){


  
        $currentTime = Carbon::now();
        $formattedTime = $currentTime->format('H:i:s');
        $formattedDate = $currentTime->format('M, j Y, D');

        $amPm = $currentTime->format('A');

        return response()->json([
            'time'=>  $formattedTime,
            'date'=>$formattedDate,
            'time_format'=>$this->getSetting('time_format'),
            'amPm'=>$amPm
        ]);


    }
}
