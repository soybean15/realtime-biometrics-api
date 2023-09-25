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


}
