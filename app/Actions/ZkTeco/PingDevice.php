<?php

namespace App\Actions\ZkTeco;

use Rats\Zkteco\Lib\ZKTeco;
class PingDevice{

    public function execute($ip =null){

        

        if (!$ip) {
            

        } 
            try {
                $zk = new ZKTeco($ip);
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