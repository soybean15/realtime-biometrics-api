<?php

namespace App\Console\Commands;

use App\Actions\Employee\CreateAttendance;
use App\Http\Services\ZkTecoService;

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

        $data = [
            'uid' => '12345',            // Replace with a valid serial_number
            'id' =>1,          // Replace with a valid employee_id
            'timestamp' => '17:01:00', // Replace with a valid timestamp
            'state' => 'present',        // Replace with a valid state
            'type' => 'Time out',        // Replace with a valid type
        ];

        $createAttendance = new CreateAttendance();
          $type=  $createAttendance->execute($data);
     $this->info('attendamce added ' .$type);


        // try {
        //     // Your code here

        //     $attendance = $this->zk->getAttendance();

        //     if ($attendance) {
        //         foreach ($attendance as $item) {

        //             $existingAttendance = Attendance::where('serial_number', $item['uid'])->first();

        //             // If a record does not exist, insert a new one
        //             if (!$existingAttendance) {

        //                 $createAttendance = new CreateAttendance();

        //                 $_attendance = $createAttendance->execute($item);

        //                 $_attendance->load('employee.positions', 'employee.departments');

        //                 if ($this->getSetting('live_update')) {
        //                     broadcast(new \App\Events\GetAttendance($_attendance))->toOthers();
        //                 }
        //             }
        //         }
        //     }

        //     $this->info('Attendance Created' . $this->getSetting('live_update'));

        // } catch (\Exception $e) {
        //     // Handle the exception here
        //     \Log::error('Error in schedule: ' . $e->getMessage());
        //     // You can also send an email, log the error, or take other actions as needed.
        // }

    }
}