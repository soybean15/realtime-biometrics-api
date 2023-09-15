<?php

namespace App\Http\Services;
use Rats\Zkteco\Lib\ZKTeco;



class ZkTecoService
{

    public function test(){
        return "test";
    }

    public function getAttendance(){


        try{
            $zk = new ZKTeco('192.168.1.201');
            $zk->connect();   
            if($zk){
             return    $zk->getAttendance(); 
            }
            return "disconnected";
        }catch(\Exception $ex){

            return "error";

        }

    }

    public function ping(String $ip){

        try{
            $zk = new ZKTeco($ip);
            if($zk->connect()){
                return response()->json([
                    'status'=>true,
                    'message'=>'Device Connected'
                ]);
            }else{
                return response()->json([
                    'status'=>false,
                    'message'=>'Failed to connect to the Device'
                ],405);
            }
     
        }catch(\Exception $ex){

            return response()->json([
                'status'=>false,
                'message'=>'Something went wrong, Please Contact the Administrator'
            ],405);

        }
       // return $ip;
    }


}