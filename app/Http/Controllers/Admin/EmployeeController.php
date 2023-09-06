<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Services\EmployeeService;
use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    //

    protected EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }


    public function index()
    {
        $employees = Employee::with(['departments', 'positions', 'user'])->orderBy('created_at', 'desc')->paginate(20);
        $trashed = Employee::onlyTrashed()->paginate(10);


        return response()->json([
            'employees' => $employees,
            'trashed' => $trashed,
        ]);
    }

    public function get(string $id)
    {

        return $this->employeeService->getEmployee($id);

    }

    public function store(Request $request)
    {


        return $this->employeeService->store($request->all(), $request->file('image'))->id;

    }

    public function update(Request $request)
    {

        return $this->employeeService->update([$request['attribute'] => $request['value']], $request['id']);
    }

    public function delete(Request $request)
    {

        return $this->employeeService->delete($request['id']);

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

        return $this->employeeService->upload($request['id'], $request->file('image'));




    }

    public function restore(Request $request)
    {
        return $this->employeeService->restore($request['id']);

    }


}