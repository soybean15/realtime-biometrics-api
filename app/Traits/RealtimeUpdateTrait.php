<?php

namespace App\Traits;

use App\Models\Setting;
use App\Models\ZkTecoDevice;

trait RealtimeUpdateTrait
{

    use HasSettings;

    public function activeDevice(){
        $settings = $this->getSettings();
    
        $zktecoId = $settings->data['zkteco'] ?? null;
    
        if ($zktecoId === null || $zktecoId === 0) {
            // Default device information
            return [
                'name' => 'default',
                'ip_address' => '192.168.1.201',
                'port' => '4370',
            ];
        }
    
        $device = ZkTecoDevice::find($zktecoId);
    
        if (!$device) {
            // Default device information
            return [
                'name' => 'default',
                'ip_address' => '192.168.1.201',
                'port' => '4370',
            ];
        }
    
        return $device;


    }

  
    public function enableRealtimeUpdate()
    {
        $this->updateSetting('live_update',true);
        // $data= $settings->data;
        // $data['live_update'] = true;
        // $settings->data = $data;
        // $settings->save();

       // return  $data['live_update'] ;
        //return $settings;
        return response()->json([
            'settings'=>$this->getSettings()
        ]);
     
    }

    public function disableRealtimeUpdate()
    {
        $this->updateSetting('live_update',false);
    //     $settings = $this->getSettings();
    //     $data= $settings->data;
    //     $data['live_update'] = false;
    //     $settings->data = $data;
    //     $settings->save(); 

    //  //   return $settings;
    //  return  $data['live_update'] ;
    return response()->json([
        'settings'=>$this->getSettings()
    ]);
 
    }
}
