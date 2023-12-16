<?php

namespace App\Traits;

use App\Models\Attendance;
use App\Models\DailyReport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait HasAttendance
{

    use WorkDayChecker, HasSchedule;



    public function getAttendanceSummary($start, $end, $month, $year)
    {



        $totalAttendance = 0;
        $attended = 0;
        $absent = 0;
        $late = 0;
        $toResolve = 0;

        $startDate = "{$year}-{$month}-{$start}";
        // $endDay = $end; // Use the provided end day
        // // Check if the month is December to handle the next year
        // $nextYear = ($month == 12) ? $year + 1 : $year;
        // $nextMonth = ($month == 12) ? 1 : $month + 1;
        $endDate = "{$year}-{$month}-{$end}";



        // return response()->json([
        //     'start'=>$start,
        //     'end'=>$end,
        //     'month'=>$month,
        //     'year'=>$year,
        //     'start_date'=>$startDate,
        //     'endDate'=>$endDate

        // ]);

        $this->dailyReport()
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->each(function ($record) use (&$totalAttendance, &$late, &$toResolve, &$attended, &$absent) {

                if (!$record->is_resolve) {
                    $toResolve++;
                }
                $attended++;


                
                if($record->late){
                    $late++;
                }
            });

        $this->getWorkingDays(Carbon::parse($startDate), Carbon::now(), function ($dateStr) use (&$totalAttendance) {

            if ($this->isDateActive($dateStr)) {
                $totalAttendance++;
            }



        });



        return response()->json([
            'late' => $late,
            'attended' => $attended,
            'total_attendance' => $totalAttendance,
            'to_resolve' => $toResolve,
            'absent' => $absent,
            'startDate' => $startDate,

        ]);

    }


    public function attendanceByCutOff($date)
    {


        $cutOff = $this->calculateCutOff($date);
        $attendance = $this->attendance()->byCutOff($date)
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('MAX(CASE WHEN type = "Time in" THEN timestamp END) as time_in'),
                DB::raw('MAX(CASE WHEN type = "Break out" THEN timestamp END) as break_out'),
                DB::raw('MAX(CASE WHEN type = "Break in" THEN timestamp END) as break_in'),
                DB::raw('MAX(CASE WHEN type = "Time out" THEN timestamp END) as time_out')
            )
            ->groupBy('date')
            ->get()
            ->each(function ($record) {
                $record->daily = $this->dailyReport()->whereDate('date', $record['date'])->get();

            });

        $newArray = []; //map attendance to new array

        if (!$attendance->isEmpty()) {
            // return [
            //     'attendance' => $attendance,
            //     'cut_off' => $cutOff['start'] . '-' . $cutOff['end'],
            // ];


            // Index the attendance data by date
            foreach ($attendance as $item) {
                $newArray[$item->date] = $item;
            }
        }




        $startDate = $cutOff['startDate'];
        //$endDate = Carbon::parse($attendance[count($attendance) - 1]->date);
        $endDate = $cutOff['endDate'];
        // Loop through the date range





        $newData = []; //storage of final output


        $this->getWorkingDays($cutOff['startDate'], $cutOff['endDate'], function ($dateStr) use (&$newArray, &$newData) {


            if ($dateStr == Carbon::now()) {
                return;
            }
            if (sizeof($newArray) > 0 && array_key_exists($dateStr, $newArray)) {
                // Date exists in the original data, add it as-is
                $newData[] = $newArray[$dateStr];
             
            } else {
                // Date is missing, insert an object with null value
                $status = $this->isDateActive($dateStr) ? 'No Attendance' : 'No Work Day';
                $newData[] = [
                    'date' => $dateStr,
                    'time_in' => null,
                    'break_out' => null,
                    'break_in' => null,
                    'time_out' => null,
                    'status' => $status,
                  
                ];
            }

        });


        return [
            'attendance' => $newData,
            'cut_off' => $cutOff['start'] . '-' . $cutOff['end'],
            'month' => $cutOff['startDate']->format('F'),

            'date' => $date,
            'cut_off_array' => $cutOff,
            'start' => $startDate,
            'end' => $endDate,
            'new'=>$attendance


        ];
    }






    public function unprocessedData()
    {

        // Get the unprocessed data by comparing timestamps with daily_report.date

        $unprocessedData = $this->attendance()
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('daily_reports')
                ->where('employee_id', $this->id)
                ->whereDate('date', '=', DB::raw('DATE(attendances.timestamp)'));
        })
        ->orderBy('timestamp')
        ->get()
        ->groupBy(function ($date) {
            return Carbon::parse($date->timestamp)->format('Y-m-d');
        });


        return $unprocessedData;

        // return [
           
        //     'attendance'=>$unprocessedData,
        //     'report'=>$this->dailyReport
        // ];

    }

    public function summarizeDaily()
    {

       $this->removeDailyReportByDate(Carbon::now()->toDateTimeString());
        $attendance = $this->unprocessedData();

        $_start = $this->getSetting('start_time'); //returns 08:00
        $_end = $this->getSetting('end_time'); //returns 08:00
       // return $attendance;
        $start = Carbon::parse(  $_start);
        $end =Carbon::parse( $_end);

      //  return $attendance;
  
    

        foreach ($attendance as $key => $value) {
            $late= false;
            $hasTimeIn= false;
            $hasTimeOut= false;
            $no_time_in = false;
            $no_time_out = false;
            $half_day_in =false;
            $half_day_out=false;
            $half_day_out = false;
            $is_resolve = true;
           
            $key = Carbon::parse($key)->format('Y-m-d');
            foreach ($value as $item) {

                
                $timeStamp = Carbon::parse($item->timestamp);
             
                switch ($item->type) {
    
                    case 'Time in':{
                        $hasTimeIn = true;
                        $halfdayThreshold = 3;
                        if ($timeStamp->diffInHours($start) >= $halfdayThreshold) {
                            $half_day_in = true;
                            break;
                        }
            
                        if($timeStamp >$start){
                         
                            $late = true;
                        }
                      
                        break;
                    }
                    case 'Break out':{
                        break;
                    }
                    case 'Break in':{
                        break;
                    }
                    case 'Time out':{

                        $hasTimeOut = true;
                        $halfdayThreshold = 3;
                        if ($timeStamp->diffInHours($end) >= $halfdayThreshold) {
                            $half_day_out = true;
                            break;
                        }
                       
                      
                        break;
                    }
                    case 'Invalid':{
                        break;
                    }
    
                }
    

            }

            if( $hasTimeIn && !$hasTimeOut ){
                $no_time_out = true;
            }
            if( !$hasTimeIn && $hasTimeOut ){
                $no_time_in = true;
            }
            if(
                $no_time_in ||
                $no_time_out ||
                $half_day_in ||
                $half_day_out 
            ){
                $is_resolve = false;
            }

            DailyReport::create([
                'employee_id'=>$this->id,
                'date'=>$key,
                'late'=>$late,
                'no_time_in'=>$no_time_in,
                'no_time_out'=>$no_time_out,
                'half_day_in'=>$half_day_in,
                'half_day_out'=>$half_day_out,
                'is_resolve'=>$is_resolve

            ]);

           
        }  

    //    return [
    //         'employee_id'=>$this->id,
    //         'date'=>$key,
    //         'late'=>$late,
    //         'no_time_in'=>$no_time_in,
    //         'no_time_out'=>$no_time_out,
    //         'half_day_in'=>$half_day_in,
    //         'half_day_out'=>$half_day_out,
    //         'is_resolve'=>$is_resolve

    //    ];



    }


    protected function removeDuplicateRemarks(&$remarks, $keys)
    {
        foreach ($remarks as $_key => $remark) {
            foreach ($remarks as $_key => $remark) {
                if (in_array($remark['key'], $keys)) {
                    unset($remarks[$_key]);
                }
            }
        }
    }



    public function resolveAttendance($data)
    {

       // $this->removeDailyReportByDate($data['timestamp']);


        $dailyReports = DailyReport::find($data['id']);
        $dailyReports->delete();
        $types = [
            'no_time_in' => 'Time in',
            'half_day_in' => 'Time in',
            'half_day_out' => 'Time out',
            'no_time_out' => 'Time out',

        ];

        $typeValue = $types[$data['type']] ?? 'Invalid';

        if ($typeValue == 'Time in') {
            $attendance = Attendance::where('employee_id', $this->id)
                ->whereDate('timestamp', Carbon::parse($data['timestamp']))
                ->where('type', $typeValue)
                ->first(); // Retrieve the first matching record

            if ($attendance) {
                $attendance->delete(); // Delete the record if found
            }
        }



        Attendance::create([
            'serial_number' => 1,
            'employee_id' => $this->id,
            'timestamp' => Carbon::parse($data['timestamp']),
            'state' => 1,
            'type' => $typeValue
        ]);



        DailyReport::create([
            'employee_id'=>$data['employee_id'],
            'date'=>$data['timestamp'],
            'late'=>false,
            'no_time_in'=>false,
            'no_time_out'=>false,
            'half_day_in'=>false,
            'half_day_out'=>false,
            'is_resolve'=>true

        ]);

        


    }

    protected function removeDailyReportByDate($date)
    {
        $dailyReports = DailyReport::where('employee_id', $this->id)
        ->whereDate('date', Carbon::parse($date)->format('Y-m-d'))
        ->get();

    // Loop through each daily report and delete
    foreach ($dailyReports as $dailyReport) {
        $dailyReport->delete();
    }

    }




    public function getAttendanceByCutOff($date = null,$callback=null){
        //$employee = Employee::find($id);

        if ($date === null) {
            $date = Carbon::now();
        }else{
            $date = Carbon::parse($date);
        }


        $data = $this->attendanceByCutOff($date);



        if($callback){
       
            return $callback(['data'=>$data, 'employee'=> $this]);
        }

        return response()->json( $data);
    }





}