<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Managers\EmployeeManager;
use App\Http\Services\DomPDFService;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    //

    protected EmployeeManager $manager;

    public function __construct(EmployeeManager $manager)
    {
        $this->manager = $manager;
    }


    public function index()
    {
        $employees = Employee::with(['departments', 'positions', 'user','attendanceToday'])->orderBy('created_at', 'desc')->get();
        $trashed = Employee::onlyTrashed()->get();


        return response()->json([
            'employees' => $employees,
            'trashed' => $trashed,
        ]);
    }

    public function get(string $id)
    {

        return $this->manager->getEmployee($id);

    }

    public function filter(Request $request){
        return $this->manager->filter($request['attribute'], $request['id']);
    }

    public function store(Request $request)
    {


        return $this->manager->store($request->all(), $request->file('image'));

    }

    public function update(Request $request)
    {

        return $this->manager->update([$request['attribute'] => $request['value']], $request['id']);
    }

    public function delete(Request $request)
    {

        return $this->manager->delete($request['id']);

    }

    public function updatePhoto(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'image' => 'image|mimes:jpeg,jpg,png,gif|max:2048', // Example rules
        ]);

        if ($validator->fails()) {

            return response()->json([
                'errors' => $validator->errors(),

            ],412);
        }

        return $this->manager->upload($request['id'], $request->file('image'));




    }
    public function restore(Request $request)
    {
        return $this->manager->restore($request['id']);

    }
    public function search(Request $request){

        return $this->manager->search($request['value']);

    }

    public function getAttendance(String $id){
        return $this->manager->getAttendance($id,);
    }

    public function getAttendanceByCutOff(Request $request,String $id){
        return $this->manager->getAttendanceByCutOff($id,$request['date']);
    }

    public function resolveAttendance(Request $request){

        return $this->manager->resolveAttendance($request->all());
        

        // return response()->json([
        //     $request->all()
        // ]);

    }

    public function attendanceSummary(Request $request, String $id){


        return $this->manager->attendanceSummary($request->all(),$id);

        // return response()->json([
        //     'id'=>$id,
        //     'request'=>$request->all()
        // ]);
    }


    public function getAttendanceByCutOffPDF(String $date, String $id){

       // return $date;

        return $this->manager->getAttendanceByCutOff($id,$date=='null'?null:$date,function($data) use (&$method){
            $pdf= DomPDFService::generate('reports.attendance_card',$data);

           // return  $pdf->download();
            
            return $pdf->stream();
            // if ($method =='stream'){
            //     return $pdf->stream();
            // }else{
            //    return  $pdf->download();
            // }


        });
    }

    public function generatePDF(){


        $this->manager->generateAttendancePDF();

    }


}