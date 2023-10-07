<?php

namespace App\Http\Services;

use App\Actions\Employee\CreateNewEmployee;
use App\Actions\Employee\DeleteEmployee;
use App\Models\Employee;
use App\Traits\HasAttendance;
use Illuminate\Support\Str;



class EmployeeService
{



    protected CreateNewEmployee $createNewEmployee;
    protected DeleteEmployee $deleteEmployee;

    public function __construct(CreateNewEmployee $createNewEmployee, DeleteEmployee $deleteEmployee)
    {
        $this->createNewEmployee = $createNewEmployee;
        $this->deleteEmployee = $deleteEmployee;
    }


    public function getEmployee($id)
    {


        try {

            $employee = Employee::find($id);
            $employee->load(['departments', 'positions', 'user', 'attendanceToday']);
            return response()->json(['employee' => $employee]);

        } catch (\Exception $e) {
            return response()->json(['error' => "Employee Cannot found"], 418);
        }

    }

    public function filter($attribute, $id)
    {

        $employees = Employee::whereHas($attribute, function ($query) use ($attribute, $id) {
            $query->where(Str::singular($attribute) . '_id', $id);
        })->paginate(20);

        $employees->load(['departments', 'positions', 'user']);

        return response()->json([
            'employees' => $employees,
            'id' => $id,
            'attribute' => Str::singular($attribute) . '_id'
        ]);


    }


    public function store($data, $file)
    {

        try {


            $id = $this->createNewEmployee->execute($data, $file)->id;

            $employee = Employee::find($id);

            $employee->load(['departments', 'positions', 'user']);

            return response()->json([

                'employee' => $employee


            ]);

        } catch (\Exception $e) {
            if ($e->getCode() == 410) {
                $errors = json_decode($e->getMessage(), true);
                return response()->json(['errors' => $errors], $e->getCode());
            }
            return response()->json($e->getMessage(), 501);

        }

    }

    public function delete($employee_id)
    {

        try {
            return $this->deleteEmployee->execute($employee_id);

        } catch (\Exception $e) {

            return response()->json(['errors' => $e->getMessage()], $e->getCode());
        }


    }

    public function update($attributes, $employee_id)
    {
        $employee = Employee::find($employee_id);

        try {
            if ($employee->validate($attributes)) {

                foreach ($attributes as $attribute => $value) {
                    $employee->$attribute = $value;
                }

                $employee->save();


                return response()->json([
                    'employee' => $employee
                ]);
            }

        } catch (\Exception $e) {
            if ($e->getCode() == 412) {
                $errors = json_decode($e->getMessage(), true);
                return response()->json(['errors' => $errors], $e->getCode());
            }

            return response()->json($e->getMessage(), 409);
        }



    }

    public function restore($id)
    {


        $employee = Employee::onlyTrashed()->find($id);

        if ($employee) {
            $employee->restore();
            $employee->generateBiometricsId();

            return response()->json(['message' => 'Employee Restored', 'employee' => $employee]);
        } else {
            throw new \Exception('Employee not found in the trash', 404);
        }
    }

    public function upload($id, $file)
    {

        $employee = Employee::find($id);


        $employee->load(['departments', 'positions', 'user']);


        if ($file) {
            $employee->restoreImage('images/users', $file);
        }



        return response()->json([
            'image' => $employee->image,

        ]);
    }


    public function search($value)
    {

        try {

            $employees = Employee::where(function ($query) use ($value) {
                $query->where('firstname', 'LIKE', "%$value%")
                    ->orWhere('lastname', 'LIKE', "%$value%")
                    ->orWhere('middlename', 'LIKE', "%$value%")
                    ->orWhere('employee_id', $value)
                    ->orWhere('biometrics_id', $value);
            })->paginate(20);

            return response()->json([
                'employees' => $employees
            ]);
        } catch (\Exception $e) {


            return response()->json(['message' => 'No available data'], 411);
        }


    }


    public function getAttendance($id)
    {
        $year = date('Y'); // Get the current year (e.g., 2023)
        $month = date('n'); // Get the current month without leading zeros (e.g., 9)

        // Subtract 3 months from the current month

        $employee = Employee::find($id);
        $attendance = $employee->attendanceByMonth($year, $month);
        $transformedAttendance = [];


        foreach ($attendance as $record) {
            $timestamp = \Carbon\Carbon::parse($record->timestamp);
            $transformedAttendance[] = [
                'date' => $timestamp->format('Y-m-d'), //YYYY-MM-DD HH:MM:SS"
                'time' => $timestamp->format('H:i:s'),
                'year' => $timestamp->year,  
                'month' => $timestamp->month,             // Month (1 to 12)
                'day' => $timestamp->day,                 // Day of the month
                'duration'=>$record->duration(),
                'bgcolor'=> 'blue-7',
                'title'=>$record->type,
                'details'=> 'Teaching Javascript 101',
                
              
            ];
        }
        return response()->json([
            'attendance'=>$transformedAttendance,
            'month'=>$month,
            'year'=>$year
        ]);


        // Now, $currentYear and $currentMonth contain the desired values


    }

    public function getAttendanceByCutOff($id){
        $employee = Employee::find($id);

        return response()->json( $employee->attendanceByCutOff()
        );
    }


    public function processDailyReport(){

        $employees = Employee::all();

        foreach($employees as $employee){
            $employee->summarizeDaily();
        }
        

    }

    public function resolveAttendance($data){


        $employee = Employee::find($data['employee_id']);


        $employee->resolveAttendance($data);

        $employee->summarizeDaily();

        // Attendance::create([
        //     'serial_number' => $data['uid'],
        //     'employee_id' =>$data['employee_id'],
        //     'timestamp' =>\Carbon\Carbon::parse($data['timestamp']) ,
        //     'state' => $data['state'],
        //     'type' =>$type 
        // ]);
    }


}