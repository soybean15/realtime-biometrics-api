<?php

namespace App\Console\Commands;

use App\Http\Services\ZkTecoService;
use Illuminate\Console\Command;

class PingDevice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ping:device';


    protected ZkTecoService $zk;

    public function __construct(ZkTecoService $zk)
    {
        parent::__construct();
        $this->zk = $zk;

    }
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to check if device is online';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->zk->ping();
    }
}
