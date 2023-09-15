<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    //
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
}
