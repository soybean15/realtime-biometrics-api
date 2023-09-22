<?php

namespace App\Console\Commands;

use App\Http\Services\ZkTecoService;
use Illuminate\Console\Command;

class GetConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get:config';

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
    protected $description = 'Check if Live Update is on';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //

       $isLive = $this->zk->getSetting('live_update');
       $timeFormat =  $this->zk->getSetting('time_format');
       $config =['device'=>$this->zk->getActiveDevice(),'isLive'=>$isLive,'time_format'=>$timeFormat];

        broadcast(new \App\Events\Config(  $config ))->toOthers();

        $this->info('Live data is ' . ($isLive ? 'On' : 'Off'));

    }
}
