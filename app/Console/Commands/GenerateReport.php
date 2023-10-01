<?php

namespace App\Console\Commands;

use App\Http\Services\EmployeeService;
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

    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        parent::__construct();
        $this->employeeService = $employeeService;

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
        $this->employeeService->processDailyReport();

        $this->info('report Processed');

    }
}
