<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Http\Managers\DepartmentManager;

class DepartmentController extends Controller
{
    //

    protected DepartmentManager $manager;

    public function __construct(DepartmentManager $manager){
        $this->manager=$manager;
    }

    public function index(){
        
        $departments = Department::all();

        return response()->json([
            'departments'=> $departments
        ]);
    }

    public function getDepartments(){
        $departments = Department::paginate(10);

        return response()->json([
            'departments'=> $departments
        ]);
    }


    public function store(Request $request){
        $this->manager->store($request->all());
    }

    public function destroy(Request $request){
        return $this->manager->destroy($request['id']);
    }
 
    public function update(Request $request){
        return $this->manager->update($request->all(), 
            $request['id']
        );
    }


    public function search (Request $request){

        return $this->manager->search($request['val']==null?'':$request['val']);

      
    }

}
