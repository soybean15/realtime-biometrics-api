<?php

namespace App\Console\Commands;

use App\Http\Services\ZkTecoService;
use Illuminate\Console\Command;

class CheckIsLive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:isLive';

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

       $isLive = $this->zk->isLive();
       $device =['device'=>$this->zk->getActiveDevice(),'isLive'=>$isLive];

        broadcast(new \App\Events\IsLive(  $device ))->toOthers();

        $this->info('Live data is ' . ($isLive ? 'On' : 'Off'));

    }
}
