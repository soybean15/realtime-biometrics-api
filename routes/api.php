<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\HolidayController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ZkTecoController;

use App\Http\Controllers\SettingsController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum', 'isEnable'])->get('/user', function (Request $request) {
    return $request->user();
});

//routes without middleware
Route::group([], function () {

    Route::prefix('settings')->group(function () {

        Route::get('/', [SettingsController::class, 'index']);
        Route::get('date-time', [SettingsController::class, 'getCurrentDateTime']);
        Route::post('/change-color', [SettingsController::class, 'changeColor']);
        Route::post('/change-setting', [SettingsController::class, 'updateSettings']);

    });


    Route::post('/zk/on-off', [ZkTecoController::class, 'disableEnableRealtimeUpdate']);



});

//route outside Admin
Route::group(['middleware' => ['auth:sanctum', 'isEnable']], function () {


    Route::prefix('zk')->group(function () {
        Route::get('/', [ZkTecoController::class, 'index']);
        Route::post('/ping', [ZkTecoController::class, 'ping']);
        Route::post('/store', [ZkTecoController::class, 'store']);
        ROute::post('delete', [ZkTecoController::class, 'delete']);
    });


});

Route::prefix('admin')->middleware(['auth:sanctum', 'isEnable'])->group(function () {


    Route::prefix('report')->group(function () {

        Route::post('/',[ReportController::class,'getReportByDate']);
        Route::post ('cut-off',[ReportController::class,'getReportByCutoff']);
        Route::post('month',[ReportController::class,'getReportByMonth']);

    });

    Route::prefix('holiday')->group(function () {

        Route::get('/', [HolidayController::class, 'index']);

        Route::post('/store', [HolidayController::class, 'store']);
        Route::post('/move', [HolidayController::class, 'move']);



    });

    Route::prefix('user')->middleware('isAdmin')->group(function () {

        Route::get('/', [UserController::class, 'index']);
        Route::post('enable', [UserController::class, 'enable']);
        Route::post('search', [UserController::class, 'search']);

    });

    Route::prefix('employee')->group(function () {

        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/{id}', [EmployeeController::class, 'get']);
        Route::post('filter', [EmployeeController::class, 'filter']);
        Route::post('update-photo', [EmployeeController::class, 'updatePhoto']);
        Route::post('add', [EmployeeController::class, 'store']);
        Route::post('delete', [EmployeeController::class, 'delete']);
        Route::post('update', [EmployeeController::class, 'update']);
        Route::post('restore', [EmployeeController::class, 'restore']);
        Route::post('search', [EmployeeController::class, 'search']);
        Route::get('attendance/{id}', [EmployeeController::class, 'getAttendance']);
        Route::post('attendance/cutoff/{id}', [EmployeeController::class, 'getAttendanceByCutOff']);
        Route::post('attendance/resolve', [EmployeeController::class, 'resolveAttendance']);
        Route::post('attendance/summary/{id}', [EmployeeController::class, 'attendanceSummary']);
      
        Route::get('pdf/{method}/{id}',[EmployeeController::class,'getAttendanceByCutOffPDF']);

    });

    Route::prefix('department')->group(function () {

        Route::get('/', [DepartmentController::class, 'index']);
        Route::post('/add', [DepartmentController::class, 'store']);
        Route::post('/delete', [DepartmentController::class, 'destroy']);
        Route::post('/update', [DepartmentController::class, 'update']);
        Route::get('search/', [DepartmentController::class, 'search']);
        Route::get('/get', [DepartmentController::class, 'getDepartments']);
    

    });

    Route::prefix('position')->group(function () {

        Route::get('/', [PositionController::class, 'index']);
        Route::post('/add', [PositionController::class, 'store']);
        Route::get('search', [PositionController::class, 'search']);
        Route::post('edit', [PositionController::class, 'update']);
        Route::post('delete', [PositionController::class, 'destroy']);
        Route::get('/get', [PositionController::class, 'getPositions']);


    });

    // ... other admin routes ...
});
// Route::get('/test',[Controller::class, 'index']);









Route::post('/test', function (Request $request) {
    $message = $request->input('message');

    broadcast(new \App\Events\GetAttendance($message))->toOthers();
    //event(new \App\Events\Hello($message));

    return response()->json(['message' => $message]);
});

Route::get('test/{id}', function ($id) {
    $attendance = \App\Models\Attendance::find($id);


    return response()->json([
        'attendance' => $attendance,
        'duration' => $attendance->duration()
    ]);
});


Route::get('test-attendance/{id}', function ($id) {
    $attendance = \App\Models\Attendance::find($id);

    $attendance->load('employee.positions', 'employee.departments');

    return $attendance;

});

Route::get('/attendance', [AttendanceController::class, 'index']);