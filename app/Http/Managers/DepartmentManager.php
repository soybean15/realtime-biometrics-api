<?php

namespace App\Http\Managers;

use App\Models\Department;
use Illuminate\Support\Facades\Validator;


class DepartmentManager{

    public function store($data){
      
        Validator::make($data, [
            'name' =>['required', 'string', 'max:255'],
        ])->validate();
    
   
        $department = Department::create([
            'name' => $data['name'],
        ]);
    
        return response()->json([
            'department' => $department
        ]);
    }


    function update($data, $id)
    {
        $department = Department::find($id);



        $attribute = $data['attribute'];
        $newValue = $data['value'];

        try {

            $validator = Validator::make($data, [
                'value' => $this->validate($attribute),
            ]);

            if ($validator->fails()) {

                throw new \Exception($validator->errors(), 422);
              
            }

            if ($department) {



                $department[$attribute] = $newValue;

                $department->save();


                return response()->json([
                    'message' => 'Department Updated Succesfully',
                    'status' => 'success'

                ]);

            } else {

                throw new \Exception("No Department Found", 422);
            }

        } catch (\Exception $e) {
            if ($e->getCode() == 422) {

                $error = json_decode($e->getMessage(), true);
                $errorMessage = $error['value'][0];


                return response()->json([
                    'message' => $errorMessage,
                    'status' => 'failed'

                ], $e->getCode());
            }


            return response()->json([
                'message' => $e->getMessage(),
                'status' => 'failed'
            ], 404);

        }

       }


   

    public function destroy($id){


        $department = Department::find($id);


            $department->delete();


            return response()->json([

                'message'=>'Department Successfully Deleted',
                'status'=>'success'
            ]);

    }

    public function search (String $val =''){

        $departments = Department::where('name', 'LIKE', "$val%");

      
        return response()->json([
            'departments'=>$departments
        ]); 


    }

}