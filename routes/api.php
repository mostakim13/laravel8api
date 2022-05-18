<?php

use App\Http\Controllers\UserApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//api for fetch user
Route::get('/users/{id?}', [UserApiController::class, 'showUser']);

//api for add user
Route::post('/add-user', [UserApiController::class, 'addUser']);

//api for add multiple user
Route::post('/add-multiple-user', [UserApiController::class, 'addMultipleUser']);

//api for update user details
Route::put('/update-user/{id}', [UserApiController::class, 'updateUser']);

//api for update single record
Route::patch('/update-single-record/{id}', [UserApiController::class, 'updateSingleRecord']);

//api for delete single user
Route::delete('/delete-single-user/{id}', [UserApiController::class, 'deleteSingleUser']);