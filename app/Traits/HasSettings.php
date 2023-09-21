<?php

namespace App\Traits;
use App\Models\Setting;
use App\Models\ZkTecoDevice;

trait HasSettings
{
    public function getSetting($key, $default = null)
    {
        // Assuming you have a Setting model
        $settings = Setting::find(1); // Assuming you want to retrieve settings from the first row

        if($key=='zkteco'){
           return $this->getIpAddress($settings->data['zkteco']);
        }

        if($key=='primary' || $key=='secondary'){
            return $settings->data['theme'][$key];
        }

    
        if ($settings && isset($settings->data[$key])) {
            // Check if the key exists in the settings data
            return $settings->data[$key];
        }
        
    
        // If the key is not found in the settings data, return the default value
        return $default;
    }

    protected function getIpAddress($id)
    {

      // return $id;
        if ($id === null || $id === 0) {
           return '192.168.1.201';
        }
    
         $device = ZkTecoDevice::find($id);
        // return $device['ip_address'];
    
        return $device ? $device['ip_address'] : '192.168.1.201';
    }
    
    
}
