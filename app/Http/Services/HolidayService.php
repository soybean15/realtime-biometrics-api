<?php

namespace App\Http\Services;

use App\Models\Holiday;
use App\Models\HolidayTemp;
use Illuminate\Support\Facades\Validator;

class HolidayService
{



    public function store($data)
    {


   
        Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'month' => ['required',],
            'day' => ['required'],
            'category' => ['required'],
        ])->validate();

        return Holiday::create([

            'name'=>$data['name'],
            'month'=>$data['month']['value'],
            'day'=>$data['day'],
            'category'=>$data['category']


        ]);

       
    }

    public function moveHoliday($data){


        HolidayTemp::create([   
            'holiday_id'=>$data['id'],
            'date'=>$data['date']

        ]);



    }



}