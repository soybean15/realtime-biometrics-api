<?php

namespace App\Traits;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

trait EmployeeTrait
{
    use HasSettings;

    public function generateBiometricsId()
    {
        $existingIds = Employee::pluck('biometrics_id')->toArray();



        $this->biometrics_id = min(array_diff(range(1, count($existingIds) + 1), $existingIds));

        $this->save();


    }
    public function validate($attributes)
    {


        foreach ($attributes as $attribute => $value) {
            switch ($attribute) {
                case 'email':
                    $validationRules[$attribute] = 'required|email';
                    break;

                case 'firstname':
                    $validationRules[$attribute] = 'required|string';
                    break;

                case 'lastname':
                    $validationRules[$attribute] = 'required|string';
                    break;

                case 'contact_number':
                    $validationRules[$attribute] = 'required|regex:/^[0-9]{10}$/';
                    break;

                case 'birthdate':
                    $validationRules[$attribute] = 'required|date|before:today|after:1900-01-01';
                    break;



                // Add more cases for other attributes as needed

                default:
                    return true;

            }
        }

        $validator = Validator::make($attributes, $validationRules);



        if ($validator->fails()) {
            throw new \Exception($validator->errors(), 412);
        } else {
            return true;
        }

    }


    public function unprocessedData()
    {

        // Get the unprocessed data by comparing timestamps with daily_report.date


        $unprocessedData = $this->attendance()->whereNotIn(DB::raw('DATE(timestamp)'), function ($query) {
            $query->select('date')
                ->from('daily_reports');
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

        foreach ($attendance as $key => $value) {

            $remark = [];
            $isResolved = false;

            foreach ($value as $item) {
                switch ($item->type) {
                    case 'Time in': {


                            // Create Carbon instances for the start time and timestamp
                            $startCarbon = Carbon::createFromFormat('H:i', $start);
                            $timestampCarbon = Carbon::parse($item['timestamp']);
                            $lateThreshold = 3;


                            $diff = $timestampCarbon->diffForHumans($startCarbon);
                            $minutesDiff = \Carbon\CarbonInterval::minutes($diff)->totalMinutes;

                            if ($timestampCarbon->format('H:i') > $startCarbon->format('H:i')) {
                                // Check if timestamp is later than start time
                                $differenceInHours = $timestampCarbon->diffInHours($startCarbon);

                                if ($differenceInHours > $lateThreshold) {
                                    // More than 3 hours late
                                    $remark = "Half day";
                                } else {
                                  
                                    $formattedDiff = "{$minutesDiff} minutes late";
                                    $remark = [
                                        'title'=>'Late',
                                        'details'=>$formattedDiff
                                    ];

                                }
                            } else {
                                $formattedDiff = "{$minutesDiff} minutes early";
                                 $remark = [
                                        'title'=>'On time',
                                        'details'=>$formattedDiff
                                    ];
                            }
                            return response()->json([
                                'raw_timestamp' => $item['timestamp'],
                                'raw_start' => $start,
                                'start' => $startCarbon->format('H:i'),
                                'timestamp' => $timestampCarbon->format('H:i'),
                                'remark' => $remark,
                            ]);



                        }
                    case 'Break in': {

                        }
                    case 'Break out': {

                        }
                    case 'Time out': {

                        }
                }

            }

        }


    }

}