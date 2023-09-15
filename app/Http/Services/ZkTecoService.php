<?php

namespace App\Http\Services;
use App\Models\ZkTecoDevice;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Validator;


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

    public function ping(String $ip,){

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

    public function store($data){


        $validator = Validator::make($data, [
            'name' => 'required|max:50',
            'ip_address' => 'required',
            'port' => 'required',
       
        ]);

        if ($validator->fails()) {
        
            return response(['errors' => $validator->errors()], 403);
        }

        $zkDevice = ZkTecoDevice::create([
            'name'=>$data['name'],
            'ip_address'=>$data['ip_address'],
            'port'=>$data['port']
        ]);

        
        return response()->json([
            'zkDevice'=>$zkDevice
        ]);


    }


}