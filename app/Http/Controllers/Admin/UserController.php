<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UserController extends Controller
{
    //
    public function index(){

        $users = User::paginate(20);
        return response()->json([

            'users'=> $users
        ]);
    }

    public function enable(Request $request){

        $user = User::find($request['id']);

        $user->enable = !$user->enable;

        $user->save();


        return response()->json([
            'user'=>$user
        ]);


    }

    public function search(Request $request){
        $users=  User::where(function ($query) use ($request ) {
            $value = $request->input('value');
            $query->where('name', 'LIKE', "%$value%")
                  ->orWhere('email', 'LIKE', "%$value%");
                
        })->paginate(20);
    
        return response()->json([

            'users'=> $users
        ]);
    }

}
