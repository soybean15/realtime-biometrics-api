<?php

namespace App\Actions\ZkTeco;
use App\Models\Setting;
use App\Models\ZkTecoDevice;


trait IpValidation
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    protected function validate($ip=null)
    {

        // $retrievedSetting = Setting::find(1);
        // return response()->json([
        //     $retrievedSetting->data['live_update']
        // ]) ;
        if (!$ip) {

            $retrievedSetting = Setting::find(1);

            $id = $retrievedSetting->data['zkteco'];

          

            if ($id == 0) {
                $ip = '192.168.1.201';
            } else {

                $device = ZkTecoDevice::find($id);

                $ip = $device->ip_address;

            }

        
        
        }
        return $ip;
    }
}