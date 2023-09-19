<?php

namespace App\Http\Services;

use App\Actions\ZkTeco\PingDevice;
use App\Models\ZkTecoDevice;
use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\Validator;


class ZkTecoService
{

    protected PingDevice $pingDevice;

    public function __construct(PingDevice $pingDevice)
    {

        $this->pingDevice = $pingDevice;

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
            if ($zk) {
                return $zk->getAttendance();
            }
            return "disconnected";
        } catch (\Exception $ex) {

            return "error";

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


}