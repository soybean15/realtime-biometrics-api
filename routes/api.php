<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ZkTecoController;
use App\Http\Controllers\Controller;
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

Route::middleware(['auth:sanctum','isEnable'])->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([],function(){

    Route::get('/settings',[SettingsController::class,'index']);
    Route::post('/settings/change-color',[SettingsController::class,'changeColor']);



});

//route outside Admin
Route::group(['middleware'=>['auth:sanctum','isEnable']],function(){


    Route::prefix('zk')->group(function(){
        Route::get('/',[ZkTecoController::class,'index']);
        Route::post('/ping',[ZkTecoController::class,'ping']);
        Route::post('/store',[ZkTecoController::class,'store']);
       
    });
  

});

Route::prefix('admin')->middleware(['auth:sanctum','isEnable'])->group(function () {
   
    Route::prefix('user')->middleware( 'isAdmin')->group(function(){

        Route::get('/',[UserController::class, 'index']);
        Route::post('enable',[UserController::class,'enable']);
        Route::post('search',[UserController::class,'search']);

    });

    Route::prefix('employee')->group(function(){

        Route::get('/',[EmployeeController::class, 'index']);
        Route::get('/{id}',[EmployeeController::class, 'get']);
        Route::post('filter',[EmployeeController::class, 'filter']); 
        Route::post('update-photo',[EmployeeController::class, 'updatePhoto']);        
        Route::post('add',[EmployeeController::class,'store']);
        Route::post('delete',[EmployeeController::class,'delete']);
        Route::post('update',[EmployeeController::class,'update']);
        Route::post('restore',[EmployeeController::class,'restore']);
        Route::post('search',[EmployeeController::class,'search']);

    });

    Route::prefix('department')->group(function(){

        Route::get('/',[DepartmentController::class,'index']);

    });

    Route::prefix('position')->group(function(){

        Route::get('/',[PositionController::class,'index']);

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

Route::get('/attendance',[AttendanceController::class, 'index']);