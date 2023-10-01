<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'employee_id',
        'date',
        'remarks',
        'resolve'
       

    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


}