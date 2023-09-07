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

    public function enableUser(Request $request){

        $user = User::find($request['id']);

        $user->enable = !$user->enable;

        $user->save();


        return response()->json([
            'user'=>$user
        ]);


    }
}
