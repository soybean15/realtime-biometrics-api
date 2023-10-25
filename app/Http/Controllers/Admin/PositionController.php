<?php

namespace App\Http\Controllers\Admin; 

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Managers\PositionManager;

class PositionController extends Controller
{
    //

    protected PositionManager $manager;

    public function __construct(PositionManager $manager){
        $this->manager=$manager;
    }


    public function index(){

        return response()->json([
            'positions'=> Position::paginate(5)
        ]);
    }

    public function store(Request $request){
        $this->manager->store($request->all());
    }


    public function search (Request $request){

        return $this->manager->search($request['val']==null?'':$request['val']);   
    }


    public function update(Request $request){
        return $this->manager->update($request->all(), 
            $request['id']
        );
    }

    public function destroy(Request $request){
        return $this->manager->destroy($request['id']);
    }


}
