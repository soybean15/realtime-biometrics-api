<?php

namespace App\Http\Services;

use App\Actions\ZkTeco\PingDevice;
use App\Models\ZkTecoDevice;
use App\Traits\HasSettings;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Validator;
use App\Traits\RealtimeUpdateTrait;


class ZkTecoService
{

    use RealtimeUpdateTrait,HasSettings;
    protected PingDevice $pingDevice;

    public function __construct()
    {

        //$this->pingDevice = $pingDevice;

    }

    public function test()
    {
        return "test";
    }

    public function getAttendance()
    {
        try {
            $zk = new ZKTeco('192.168.1.201');
            $zk->connect();
    
            if (!$zk) {
                $this->disableRealtimeUpdate();
                return false;
            }
    
            $this->enableRealtimeUpdate();
            return $zk->getAttendance();
        } catch (\Exception $ex) {
            $this->disableRealtimeUpdate();
            return false;
        }
    }
    

    public function ping($ip = null)

       
    {

        return $this->pingDevice->execute($ip);
    }


    



    public function store($data)
    {


        $validator = Validator::make($data, [
            'name' => 'required|max:50',
            'ip_address' => 'required',
            'port' => 'required',

        ]);

        if ($validator->fails()) {

            return response(['errors' => $validator->errors()], 403);
        }

        $zkDevice = ZkTecoDevice::create([
            'name' => $data['name'],
            'ip_address' => $data['ip_address'],
            'port' => $data['port']
        ]);


        return response()->json([
            'zkDevice' => $zkDevice
        ]);


    }

    public function destroy($id)
    {

        $device = ZkTecoDevice::find($id);

        if ($device) {
            $device->delete();

            return response()->json([
                'message' => "Device Successfully Deleted"
            ]);
        }
        return response()->json([
            'message' => "Something went wrong"
        ], 404);



    }


    public function disableLiveUpdate(){
        $this->disableRealtimeUpdate();
    }

    public function enableLiveUpdate(){
        $this->enableRealtimeUpdate();
    }

    public function getActiveDevice(){
        return $this->activeDevice();
    }

    public function setTimeFormat(){
        
    }


}