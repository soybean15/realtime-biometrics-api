<?php

namespace App\Http\Controllers;

use App\Http\Services\ZkTecoService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected ZkTecoService $zk;

    public function __construct(ZkTecoService $zk)
    {
       
        $this->zk = $zk;
       
    }

    public function index(){
        return $this->zk->getAttendance();
    }
}
