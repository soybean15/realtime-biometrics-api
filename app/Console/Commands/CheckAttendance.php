<?php

namespace App\Console\Commands;

use App\Http\Services\ZkTecoService;
use App\Models\Attendance;
use App\Models\Employee;
use App\Traits\HasSettings;
use Illuminate\Console\Command;

class CheckAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */

    use HasSettings;
    protected $signature = 'check:attendance';
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //


        //* * * * * cd   /home/soybean15/capstone/marlon/api && php artisan schedule:run >> /dev/null 2>&1


        $attendance = $this->zk->getAttendance();



        if ($attendance) {
            foreach ($attendance as $item) {

                $existingAttendance = Attendance::where('serial_number', $item['uid'])->first();

                // If a record does not exist, insert a new one
                if (!$existingAttendance) {
                    $_attendance = Attendance::create([
                        'serial_number' => $item['uid'],
                        'biometrics_id' => $item['id'],
                        'timestamp' => $item['timestamp'],
                        'state' => $item['state'],
                        'type' => $item['type']
                    ]);

                   
                    $_attendance->load('employee.positions', 'employee.departments');
                    // $_attendance->load('employee.positions.departments');

                 

                    if ($this->getSetting('live_update')) {
                        broadcast(new \App\Events\GetAttendance($_attendance))->toOthers();
                    }


                }

            }

        }


        //  $this->zk->disable();



        $this->info(' Attedance Created' . $this->getSetting('live_update'));

    }
}