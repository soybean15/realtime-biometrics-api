<?php

namespace App\Models;

use App\Traits\ImageTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
class Employee extends Model
{
    use HasFactory,SoftDeletes, ImageTrait;


    protected $fillable = [
        'firstname',
        'lastname',
        'middlename',
        'gender',
        'birthdate',
        'contact_number',
        'email',
        'image',
        'address',
    ];



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

    public function getDeletedAtAttribute($value){
        return Carbon::parse($value)->diffForHumans();
    }
}
