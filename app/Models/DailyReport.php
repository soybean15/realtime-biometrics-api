<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'employee_id',
        'date',
        'late',
        'no_time_in',
        'no_time_out',
        'half_day_in',
        'half_day_out',
        'is_resolve'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getRemarksAttribute($value)
    {
        return json_decode($value);
    }

    public function scopeAttendanceDescrepancy(Builder $query){
        $query->where(function ($query) {
            $query->where('is_resolve', false)
                  ->orWhere('late', true);
        });
    }

  


}