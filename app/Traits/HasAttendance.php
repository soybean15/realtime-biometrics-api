<?php

namespace App\Traits;

use App\Models\Attendance;
use App\Models\DailyReport;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

trait HasAttendance
{

    use WorkDayChecker;



    public function getAttendanceSummary($start, $end, $month, $year)
    {



        $totalAttendance = 0;
        $attended = 0;
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
            ->each(function ($record) use (&$totalAttendance, &$late, &$toResolve, &$attended) {
                $attended++;
                if (!$record->is_resolve) {
                    $toResolve++;
                }

                foreach ($record->remarks as $item) {

                    switch ($item->key) {
                        case 'late': {
                                $late++;
                            }
                    }

                }

            });

       $test = $this->getWorkingDays(Carbon::parse($startDate),Carbon::parse( $endDate), function ($dateStr) use (&$totalAttendance) {

            if($this->isDateActive($dateStr)){
                $totalAttendance++;
            }



        });



        return response()->json([
            'late' => $late,
            'attended'=>$attended,
            'total_attendance' => $totalAttendance,
            'to_resolve' => $toResolve,
            'endDate'=>$endDate,
            'startDate'=>$startDate,
            'test'=>$test
        ]);

    }


    public function attendanceByCutOff()
    {


        $cutOff = $this->calculateCutOff(Carbon::now());
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
            ->each(function ($record) {
                $record->daily = $this->dailyReport()->whereDate('date', $record['date'])->get();

            });



        if ($attendance->isEmpty()) {
            return [
                'attendance' => $attendance,
                'cut_off' => $cutOff['start'] . '-' . $cutOff['end'],
            ];
        }
        $newArray = [];
        // Index the attendance data by date
        foreach ($attendance as $item) {
            $newArray[$item->date] = $item;
        }

        $startDate = Carbon::parse($cutOff['startDate']);
        $endDate = Carbon::parse($attendance[count($attendance) - 1]->date);

        // Loop through the date range
        $start = $startDate->copy();



        $newData = [];


        $this->getWorkingDays($start, $endDate, function ($dateStr) use (&$newArray, &$newData) {



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

        });


        return [
            'attendance' => $newData,
            'cut_off' => $cutOff['start'] . '-' . $cutOff['end'],
            'end_date'=>$attendance[count($attendance) - 1]->date


        ];
    }

    private function getWorkingDays($start, $end, $callback)
    {

        $test = 0;
        while ($start <= $end) {
            $test++;
            $dateStr = $start->format('Y-m-d');
            $callback($dateStr);

            $start->addDay();
        }

        return $test;

    }


    private function calculateCutOff($currentDate)
    {
        $day = $currentDate->day;
        $endOfMonth = $currentDate->endOfMonth()->day;


        if ($day <= 15) {
            $currentDate->setDay(1); // Set the day to 1st day of the month
            return ['start' => 1, 'end' => 15, 'startDate' => $currentDate->format('Y-m-d')];
        } else {
            $currentDate->setDay(16); // Set the day to 16th day of the month
            return ['start' => 16, 'end' => $endOfMonth, 'startDate' => $currentDate->format('Y-m-d')];
        }
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