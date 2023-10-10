<?php

namespace App\Models;

use App\Traits\HasAttendance;
use App\Traits\ImageTrait;
use App\Traits\EmployeeTrait;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Traits\WorkDayChecker;
class Employee extends Model
{
    use HasFactory, SoftDeletes, ImageTrait, EmployeeTrait, HasAttendance,WorkDayChecker;


    protected $fillable = [
        'employee_id',
        'firstname',
        'lastname',
        'middlename',
        'gender',
        'birthdate',
        'contact_number',
        'email',
        'image',
        'address',
        'user_id',
        'biometrics_id'

    ];



    protected $appends = ['full_name'];


    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('images/users/' . $value);
        } else {
            if ($this->gender == 'Male') {
                return asset('images/defaults/users/male.png');
            } else {
                return asset('images/defaults/users/female.png');
            }
        }
    }

    public function getDeletedAtAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->diffForHumans();
        } else
            return null;

    }

    public function getFullNameAttribute()
    {

        $full_name = $this->firstname . ' ' . $this->lastname;

        //!empty($this->middlename) || 
        if ($this->middlename != 'N/A') {
            $full_name .= " " . strtoupper($this->middlename[0]) . '.';
        }

        return $full_name;


    }

    public function departments()
    {
        return $this->belongsToMany(Department::class);
    }
    public function positions()
    {
        return $this->belongsToMany(Position::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }

    public function dailyReport()
    {
        return $this->hasMany(DailyReport::class);
    }


    public function attendanceToday()
    {
        return $this->attendance()
            ->whereDate('timestamp', now()->toDateString())
            ->where('type', '!=', 'Unknown');
    }

    public function attendanceByMonth($year, $month)
    {
        return $this->attendance()
            ->byMonth($year, $month)
            ->get();

    }

    public function attendanceByCutOff()
    {
        //  return  $this->attendance()->byCutOff();
        $cut_off = '';
        $attendance = $this->attendance()->byCutOff()
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('MAX(CASE WHEN type = "Time in" THEN timestamp END) as time_in'),
                DB::raw('MAX(CASE WHEN type = "Break out" THEN timestamp END) as break_out'),
                DB::raw('MAX(CASE WHEN type = "Break in" THEN timestamp END) as break_in'),
                DB::raw('MAX(CASE WHEN type = "Time out" THEN timestamp END) as time_out'),

            )
            ->groupBy('date')
            ->get()
            ->each(function ($record) use (&$cut_off) {
                $day = Carbon::parse($record->date)->day;


                $endOfMonth = Carbon::parse($record->date)->endOfMonth()->day;

                if ($day < 15) {
                    $cut_off = '1-15';
                } else {
                    $cut_off = '16-' . $endOfMonth;
                }

                $record->daily = $this->dailyReport()->whereDate('date', $record['date'])->get();


            });



        //insert date
        $newArray = [];

        foreach ($attendance as $item) {
            $newArray[$item->date] = $item;
        }

        $startDate = new DateTime($attendance[0]->date);
        $endDate = new DateTime($attendance[sizeof($attendance) - 1]->date);



        $newData = [];
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            if (array_key_exists($dateStr, $newArray)) {
                // Date exists in the original data, add it as-is
                $newData[] = $newArray[$dateStr] ;
            } else {
                // Date is missing, insert an object with null value
                $status='';
                if($this->isDateActive($dateStr)){
                    $status="Absent";

                }else{
    
                    $status="No work day";
                }
                $newData[] = [
                    "date" => $dateStr, 
                    "time_in" => null,
                    "break_out" => null,
                    "break_in" => null,
                    "time_out" => null,
                    "status"=>$status
                ];
            }

         
            $currentDate->modify('+1 day');
        }


        return [
            'attendance' => $newData,
            'cut_off' => $cut_off,
            'start' => $startDate,
            'end' => $endDate
        ];

    }





    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function getEmailAttribute($value)
    {
        return $value ?? 'N/A';
    }

    public function getContactNumberAttribute($value)
    {
        return $value ?? 'N/A';
    }

    public function getAddressAttribute($value)
    {
        return $value ?? 'N/A';
    }

    public function getMiddlenameAttribute($value)
    {
        return $value ?? 'N/A';
    }




}