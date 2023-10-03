<?php

namespace App\Traits;
use App\Models\DailyReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
trait HasAttendance
{

    

    public function unprocessedData()
    {

        // Get the unprocessed data by comparing timestamps with daily_report.date


        $unprocessedData = $this->attendance()->whereNotIn(DB::raw('DATE(timestamp)'), function ($query) {
            $query->select('date')
                ->from('daily_reports')
                ->where('employee_id', $this->id);;
        })

            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->timestamp)->format('Y-m-d'); // Assuming you have Carbon imported
            });


        return $unprocessedData;

    }

    public function summarizeDaily()
    {
    
        $attendance = $this->unprocessedData();
        $start = $this->getSetting('start_time'); //returns 08:00
        $end = $this->getSetting('end_time'); //returns 08:00
        foreach ($attendance as $key => $value) {

            $remarks = [];
           
            $hasTimeIn = false;
            $hasTimeOut = false;
            foreach ($value as $item) {

                $startCarbon = Carbon::createFromFormat('H:i', $start);
                $endCarbon = Carbon::createFromFormat('H:i', $end);
                $timestampCarbon = Carbon::parse($item['timestamp']);
              //  $timestampCarbon = Carbon::parse('17:01');
                switch ($item['type']) {
                    case "Time in": {
                            $hasTimeIn = true;
                            $remarks[] = $this->processTimein($start, $item['timestamp'], $timestampCarbon, $startCarbon);
                            break;

                            //return $hasTimeIn;
                            //return
                        }
                    case 'Break in': {
                            break;
                        }
                    case 'Break out': {
                            break;
                        }
                    case "Time out": {
                        $hasTimeOut = true;
                            if (!$hasTimeIn) {

                                $remarks[] = [
                                    'key' => 'no_time_in',
                                    'title' => 'No Time In',
                                    'details' => 'No Time in'
                                ];


                            } else {

                                $remarks[] = $this->processTimeOut($timestampCarbon, $endCarbon);


                            }


                        }
                }

            }
            $isResolved = true;
            //check if user doest have time out
            if($hasTimeIn && !$hasTimeOut){
                $isResolved = false;
                $remarks[]= [
                    'key' => 'no_time_out',
                    'title' => 'No Time out',
                    'details' => 'No Time out'
                ];
            }

            if(!$hasTimeIn && $hasTimeOut){
                $isResolved = false;
                $remarks[]= [
                    'key' => 'no_time_in',
                    'title' => 'No Time in',
                    'details' => 'No Time in'
                ];
            }


            //to do, once I added calendar for active work hours
            if (!$hasTimeIn || !$hasTimeOut) {
                $isResolved = false;
            }
            return DailyReport::create([
                'employee_id'=>$this->id,
                'date'=>$key,
                'remarks'=>json_encode($remarks),
                'is_resolve'=>$isResolved
            ]);

         
        }


    }

    protected function processTimeOut($timestampCarbon, $endCarbon)
    {

        if ($timestampCarbon->format('H:i') < $endCarbon->format('H:i')) {

            $diff = $timestampCarbon->diffForHumans($endCarbon);
            $minutesDiff = \Carbon\CarbonInterval::minutes($diff)->totalMinutes;
            $formattedDiff = "{$minutesDiff} minutes early";

            return [
                'key' => 'Early Out',
                'title' => 'Early Out',
                'details' => $formattedDiff
            ];



        } else {

            return [
                'key' => 'time_out',
                'title' => 'Time out',
                'details' => 'Time Out'
            ];

        }
    }


    protected function processTimein($start, $timestamp, $timestampCarbon, $startCarbon)
    {

        $lateThreshold = 3;


        $diff = $timestampCarbon->diffForHumans($startCarbon);
        $minutesDiff = \Carbon\CarbonInterval::minutes($diff)->totalMinutes;

        if ($timestampCarbon->format('H:i') > $startCarbon->format('H:i')) {
            // Check if timestamp is later than start time
            $differenceInHours = $timestampCarbon->diffInHours($startCarbon);

            if ($differenceInHours > $lateThreshold) {
                // More than 3 hours late
                return [
                    'key' => 'half_day',
                    'title' => 'Half Day',
                    'details' => 'Half Day'
                ];
            } else {

                $formattedDiff = "{$minutesDiff} minutes late";
                return [
                    'key' => 'late',
                    'title' => 'Late',
                    'details' => $formattedDiff
                ];

            }
        } else {
            $formattedDiff = "{$minutesDiff} minutes early";
            return [
                'key' => 'on_time',
                'title' => 'On time',
                'details' => $formattedDiff
            ];
        }
        // return response()->json([
        //     'raw_timestamp' => $timestamp,
        //     'raw_start' => $start,
        //     'start' => $startCarbon->format('H:i'),
        //     'timestamp' => $timestampCarbon->format('H:i'),
        //     'remark' => $remark,
        // ]);

    }

}