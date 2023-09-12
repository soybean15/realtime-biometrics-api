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


}