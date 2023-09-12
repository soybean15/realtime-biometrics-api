<?php

namespace App\Console\Commands;

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
       
         Employee::create([
            'employee_id' => date('y') . '-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT) . '-' . date('m'),
            'firstname' => "test",
            'lastname' =>  "test",
            'middlename' => "test",
            'gender' =>  "test",
            'birthdate' => '2013-10-04',

            'contact_number' =>'Test',
            'email' => 'Test',
            'address' =>'Test',
            'user_id' => 2,
            'biometrics_id'=>-1
       

        ]);

        //* * * * * cd   /home/soybean15/capstone/marlon/api && php artisan schedule:run >> /dev/null 2>&1

        
     

        $this->info('Successfully added users');

    }
}
