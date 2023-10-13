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

    
    private function calculateCutOff($currentDate)
    {
        $day = $currentDate->day;
        $endOfMonth = $currentDate->endOfMonth()->day;
    
        if ($day < 15) {
            return '1-15';
        } else {
            return "16-$endOfMonth";
        }
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