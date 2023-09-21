<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\ZkTecoService;
use App\Models\ZkTecoDevice;
use Illuminate\Http\Request;

class ZkTecoController extends Controller
{
    //

    protected ZkTecoService $zk;

    public function __construct(ZkTecoService $zk)
    {

        $this->zk = $zk;

    }

    public function index(){
        $devices = ZkTecoDevice::all();

        return response()->json([
            'devices' => $devices
        ]);
    }

    public function ping(Request $request){

        //return $request['ip_address']??'192.168.1.201';
        return $this->zk->ping($request['ip_address']??'192.168.1.201');
     //   return $this->zk->test();



    }

    public function store(Request $request){

        return $this->zk->store($request->all());

    }
    public function delete(Request $request){


        return $this->zk->destroy($request['id']);
    }

    public function disableEnableRealtimeUpdate(Request $request){


        if($request['isLive']){
            return $this->zk->disableLiveUpdate();
        }else{
            
        return $this->zk->enableLiveUpdate();
        }


        // return response()->json([
        //     'isLive'=>$request['isLive']
        // ]);
     

    }
}
