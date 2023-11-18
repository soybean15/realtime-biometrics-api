<?php

namespace App\Http\Managers;
use App\Models\Position;
use Illuminate\Support\Facades\Validator;

class PositionManager{


    public function store($data){
      
        Validator::make($data, [
            'name' =>['required', 'string', 'max:255'],
        ])->validate();
    
   
        $position = Position::create([
            'name' => $data['name'],
        ]);
    
        return response()->json([
            'position' => $position
        ]);
    }


    public function search (String $val =''){

        $positions = Position::where('name', 'LIKE', "$val%")->get();

      
        return response()->json([
            'positions'=>$positions
        ]); 


    }

    function update($data, $id)
    {
        $position = Position::find($id);


        Validator::make($data, [
            'name' =>['required', 'string', 'max:255'],
        ])->validate();
    
    
        try {
     
            if ($position) {



                $position['name'] = $data['name'];

                $position->save();


                return response()->json([
                    'message' => 'Position Updated Succesfully',
                    'status' => 'success'

                ]);

            } else {

                throw new \Exception("No Position Found", 422);
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


        $position = Position::find($id);


            $position->delete();


            return response()->json([

                'message'=>'Position Successfully Deleted',
                'status'=>'success'
            ]);

    }




    
}