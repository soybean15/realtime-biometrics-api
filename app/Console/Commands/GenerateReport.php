<?php

namespace App\Console\Commands;

use App\Http\Managers\EmployeeManager;

use App\Http\Services\ZkTecoService;
use Illuminate\Console\Command;

class GenerateReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:report';

    protected EmployeeManager $manager;

    public function __construct(EmployeeManager $manager)
    {
        parent::__construct();
        $this->manager = $manager;

    }

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->manager->processDailyReport();

        $this->info('report Processed');

    }
}
