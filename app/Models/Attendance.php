<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [

        'serial_number',
        'biometrics_id',
        'timestamp',
        'state',
        'type'

    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
    public function today(Builder $query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }
}
    
