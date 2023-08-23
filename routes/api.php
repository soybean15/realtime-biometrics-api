<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('admin')->middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    // Define your admin-specific routes here

    // Example admin route
    Route::prefix('users')->group(function(){

        Route::get('/',[UserController::class, 'index']);

    });
   
    // ... other admin routes ...
});
