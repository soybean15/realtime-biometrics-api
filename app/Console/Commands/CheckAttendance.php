<?php

namespace App\Console\Commands;

use App\Http\Services\ZkTecoService;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Console\Command;

class CheckAttendance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
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

        //  Employee::create([
        //     'employee_id' => date('y') . '-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT) . '-' . date('m'),
        //     'firstname' => "test",
        //     'lastname' =>  "test",
        //     'middlename' => "test",
        //     'gender' =>  "test",
        //     'birthdate' => '2013-10-04',

        //     'contact_number' =>'Test',
        //     'email' => 'Test',
        //     'address' =>'Test',
        //     'user_id' => 2,
        //     'biometrics_id'=>-1


        // ]);

        //* * * * * cd   /home/soybean15/capstone/marlon/api && php artisan schedule:run >> /dev/null 2>&1


        $attendance = $this->zk->getAttendance();

        foreach ($attendance as $item) {

            $existingAttendance = Attendance::where('serial_number', $item['uid'])->first();

            // If a record does not exist, insert a new one
            if (!$existingAttendance) {
                Attendance::create([
                    'serial_number' => $item['uid'],
                    'biometrics_id' => $item['id'],
                    'timestamp' => $item['timestamp'],
                    'state' => $item['state'],
                    'type' => $item['type']
                ]);
            }

        }



        $this->info(' Attedance Created');

    }
}