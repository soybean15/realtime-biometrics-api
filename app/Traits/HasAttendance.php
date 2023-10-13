<?php

namespace App\Traits;

use App\Models\Attendance;
use App\Models\DailyReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait HasAttendance
{




 
    public function attendanceByCutOff()
    {
        $attendance = $this->attendance()->byCutOff()
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
            ->each(function($record){
                $record->daily = $this->dailyReport()->whereDate('date', $record['date'])->get();

            });
    
        if ($attendance->isEmpty()) {
            return [
                'attendance' => $attendance,
                'cut_off' => '',
            ];
        }
    
        $newData = [];
        $newArray = [];
    
        // Index the attendance data by date
        foreach ($attendance as $item) {
            $newArray[$item->date] = $item;
        }
    
        $startDate = Carbon::parse($attendance[0]->date);
        $endDate = Carbon::parse($attendance[count($attendance) - 1]->date);
    
        // Loop through the date range
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
    
            if (array_key_exists($dateStr, $newArray)) {
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
    
            $currentDate->addDay();
        }
    
        return [
            'attendance' => $newData,
            'cut_off' => $this->calculateCutOff($currentDate),
        ];
    }
    public function unprocessedData()
    {

        // Get the unprocessed data by comparing timestamps with daily_report.date


        $unprocessedData = $this->attendance()->whereNotIn(DB::raw('DATE(timestamp)'), function ($query) {
            $query->select('date')
                ->from('daily_reports')
                ->where('employee_id', $this->id);
            ;
        })
            ->orderBy('timestamp') // Add this line to order by timestamp
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
            $isResolved = true;
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

                            $this->removeDuplicateRemarks($remarks, ['undertime', 'no_time_in']);
                            if (!$hasTimeIn) {
                                $isResolved = false;
                                $remarks[] = [
                                    'key' => 'no_time_in',
                                    'title' => 'No Time In',
                                    'details' => 'No Time in'
                                ];


                            } else {
                                $isResolved = true;

                                $remarks[] = $this->processTimeOut($timestampCarbon, $endCarbon);
                            }

                            //remove undertime if it exist



                        }

                }

            }

            //check if user doest have time out
            // Check if user has time in but no time out
            if ($hasTimeIn && !$hasTimeOut) {

                if ($timestampCarbon->isPast()) {
                    // If $timestampCarbon is in the past, add the 'No Time Out' remark
                    $remarks[] = [
                        'key' => 'no_time_out',
                        'title' => 'No Time Out',
                        'details' => 'No Time Out',
                    ];
                } else {
                    // If $timestampCarbon is in the future, you can add a different remark or handle it as needed
                    // For example, you could add a remark indicating that the time out is pending
                    $remarks[] = [
                        'key' => 'pending_time_out',
                        'title' => 'Pending Time Out',
                        'details' => 'Pending Time Out (Timestamp is in the future)',
                    ];
                }
            }


            // if(!$hasTimeIn && $hasTimeOut){
            //     $isResolved = false;
            //     $remarks[]= [
            //         'key' => 'no_time_in',
            //         'title' => 'No Time in',
            //         'details' => 'No Time in'
            //     ];
            // }


            //to do, once I added calendar for active work hours
            if (!$hasTimeIn || !$hasTimeOut) {
                $isResolved = false;
            }
            return DailyReport::create([
                'employee_id' => $this->id,
                'date' => $key,
                'remarks' => json_encode($remarks),
                'is_resolve' => $isResolved
            ]);


        }


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

    protected function processTimeOut($timestampCarbon, $endCarbon)
    {

        if ($timestampCarbon->format('H:i') < $endCarbon->format('H:i')) {

            $diff = $timestampCarbon->diffForHumans($endCarbon);
            $minutesDiff = \Carbon\CarbonInterval::minutes($diff)->totalMinutes;
            $formattedDiff = "{$minutesDiff} minutes early";

            return [
                'key' => 'undertime',
                'title' => 'Undertime',
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







    public function resolveAttendance($data)
    {

        $this->removeDailyReportByDate($data['timestamp']);



        $types = [
            'no_time_in' => 'Time in',
            'no_time_out' => 'Time out'
        ];

        $typeValue = $types[$data['type']] ?? 'Unknown';




        Attendance::create([
            'serial_number' => 1,
            'employee_id' => $this->id,
            'timestamp' => Carbon::parse($data['timestamp']),
            'state' => 1,
            'type' => $typeValue
        ]);

    }

    protected function removeDailyReportByDate($date)
    {

        DailyReport::where('employee_id', $this->id)
            ->whereDate('date', Carbon::parse($date)->format('Y-m-d'))
            ->delete();

    }

}