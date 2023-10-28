<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Managers\DashboardManager;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    protected DashboardManager $manager;
    public function __construct(DashboardManager $manager){
        $this->manager = $manager;
    }

    public function index() {
        return $this->manager->index();
    }
}
