<?php

namespace App\Actions\ZkTeco;

use App\Models\Setting;
use App\Models\ZkTecoDevice;
use Rats\Zkteco\Lib\ZKTeco;
class PingDevice{

    use IpValidation;
    public function execute($ip =null){

       //return $this->validate($ip);
            try {
                $zk = new ZKTeco($this->validate($ip));
                if ($zk->connect()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Device Connected',
                        'zk_version' => $zk->version(),
                        'device_name' => $zk->deviceName()
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to connect to the Device'
                    ], 405);
                }

            } catch (\Exception $ex) {

                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong, Please Contact the Administrator'
                ], 405);

            }

        


    }

    
}