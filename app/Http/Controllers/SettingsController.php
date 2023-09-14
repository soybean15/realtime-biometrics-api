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
       
    
        $setting = Setting::find(1); // Assuming you've stored a setting with ID 1
    
        // Get the existing data attribute as an array
        $data = $setting->data;
    
        // Update the theme_color -> primary value
        $data['theme']['primary'] = $primary;
    
        // Update the 'data' attribute of the setting
        $setting->data = $data;
        $setting->save();

        return  $setting->data;

        
    }
}
