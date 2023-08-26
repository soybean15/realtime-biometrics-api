<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,SoftDeletes;




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
}
