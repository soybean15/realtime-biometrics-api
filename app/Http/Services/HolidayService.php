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
    public function moveHoliday($data) {
        $holiday = Holiday::find($data['id']);
    
        if (!$holiday) {
            // Handle the case where the Holiday doesn't exist.
            // You can return an error message or take any other action you need.
            return 'Holiday not found';
        }
    
        $holidayTemp = $holiday->holidayTemp;
    
        if (!$holidayTemp) {
            // If the holiday doesn't have a related holidayTemp, create a new one.
            $holidayTemp = HolidayTemp::create([
                'holiday_id' => $data['id'],
                'date' => $data['date']
            ]);
        } else {
            // If the holiday has a related holidayTemp, update the date.
            $holidayTemp->update([
                'date' => $data['date']
            ]);
        }
    
        return response()->json([

            'message'=> "Successfully moved holiday",
            'holiday'=>$holiday
        ]
        );
    }
    


}