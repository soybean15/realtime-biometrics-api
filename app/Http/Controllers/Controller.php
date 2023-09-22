<?php

namespace App\Http\Controllers;

use App\Http\Services\ZkTecoService;
use App\Models\Setting;
use App\Traits\HasSettings;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Carbon\Carbon;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests,HasSettings;

    protected ZkTecoService $zk;

    public function __construct(ZkTecoService $zk)
    {
       
        $this->zk = $zk;
       
    }

    public function index(){
        return $this->zk->getAttendance();
    }
   

    
}
