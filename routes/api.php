<?php

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PassportAuthController;
use App\Http\Controllers\ResortController;
use App\Http\Controllers\ResortRequestController;
use App\Http\Controllers\School_monController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\CobonController;
use App\Http\Controllers\Requests\HotelRequestController;
use App\Http\Controllers\Requests\CobonRequestController;
use App\Http\Controllers\TravelAllowanceController;
use App\Http\Controllers\VariablesController;

Route::post('register', [PassportAuthController::class, 'register']);
Route::post('login', [PassportAuthController::class, 'login']);
//Route::middleware(['auth:api'])->group(function(){
Route::get('userinfo', [PassportAuthController::class, 'userinfo']);
Route::get('user/{user}', [PassportAuthController::class, 'User']);
Route::put('update/{id}', [PassportAuthController::class, 'update']);
Route::get('user/requests/{performance_num}', [PassportAuthController::class, 'latest_req']);


Route::get('user/{performance_num}', [PassportAuthController::class, 'getName']);
Route::get('/cobon_req/{pn}',[CobonRequestController::class,'latest_cobon']);
Route::get('/hotel_req/{pn}',[HotelRequestController::class,'latest_hotel']);
Route::get('/resort_req/{pn}',[ResortRequestController::class,'latest_resort']);
Route::get('/School_req/{pn}',[School_monController::class,'latest_schoolmoney']);


Route::post('resorts', [ResortController::class, 'store']);
Route::get('resorts', [ResortController::class, 'index']);
Route::put('resorts/{id}', [ResortController::class, 'update']);
Route::delete('resorts/{id}', [ResortController::class, 'destroy']);

Route::post('resortrequest', [ResortRequestController::class, 'store']);
Route::get('resortrequest', [ResortRequestController::class, 'index']);
Route::delete('resortrequest/{id}', [ResortRequestController::class, 'destroy']);
Route::post('resortrequest/{id}', [ResortRequestController::class, 'update']);
Route::put('/resortrequest/{id}/confirm', [ResortRequestController::class, 'confirmed']);
Route::put('/resortrequest/{id}/reject', [ResortRequestController::class, 'rejected']);



Route::post('schoolmon', [School_monController::class, 'store']);
Route::get('schoolmon/showall', [School_monController::class, 'index']);
Route::post('schoolmon/upload', [School_monController::class, 'upload']);
Route::post('schoolmon/{id}', [School_monController::class, 'update']);
Route::delete('schoolmon/{id}', [School_monController::class, 'destroy']);
// Route::get('schoolmon/sendfile/{id}', [School_monController::class, 'sendfile']);



Route::put('/schoolmon/{performance}/confirm', [School_monController::class, 'confirmed']);
Route::put('/schoolmon/{performance}/reject', [School_monController::class, 'rejected']);


Route::post('/cobons/store',[CobonController::class,'store']);
 Route::get('/cobons/{cobon}',[CobonController::class,'show']);
 Route::put('/cobons/{cobon}',[CobonController::class,'update']);
 Route::delete('/cobons/{cobon}',[CobonController::class,'destroy']);
  
 Route::post('/travelAllowance/store',[TravelAllowanceController::class,'store']);
 Route::get('/travelAllowance',[TravelAllowanceController::class,'index']);
 Route::put('travelAllowance/{id}',[TravelAllowanceController::class,'update']);
 Route::delete('/travelAllowance/{id}',[TravelAllowanceController::class,'destroy']);
 Route::get('travelAllowance/is_found/{user_id}/{start_date}/{end_date}', [TravelAllowanceController::class, 'is_found']);

 Route::get('/cobon_req/{cobon_req}',[CobonRequestController::class,'show']);
 Route::get('/cobon_req/{cobon_req}/confirm',[CobonRequestController::class,'confirmed']);
 Route::get('/cobon_req/{cobon_req}/reject',[CobonRequestController::class,'rejected']);
 Route::delete('/cobon_req/{cobon_req}',[CobonRequestController::class,'destroy']);
 Route::put('/cobon_req/{cobon_req}',[CobonRequestController::class,'update']);
 


 
 Route::post('/hotels/store',[HotelController::class,'store']);
 Route::get('/hotels/{hotel}',[HotelController::class,'show']);
 Route::put('/hotels/{hotel}',[HotelController::class,'update']);
 Route::delete('/hotels/{hotel}',[HotelController::class,'destroy']);


 
 Route::get('/cobons',[CobonController::class,'index']);
 Route::get('/hotels',[HotelController::class,'index']);
 Route::get('/cobon_req',[CobonRequestController::class,'index']);
 Route::get('/hotel_req',[HotelRequestController::class,'index']);
 Route::get('/dependents',[DependentController::class,'index']);
 Route::post('/cobon_req/store',[CobonRequestController::class,'store']);
 Route::post('/hotel_req/store',[HotelRequestController::class,'store']);
 Route::get('/hotel_req/get_file',[HotelRequestController::class,'get_file']);
 Route::get('/sendfile/{filename}',[BaseController::class,'sendfile']);
 
 Route::get('/cobons/{cobon}/disable',[CobonController::class,'disable']);
 Route::get('/cobons/{cobon}/enable',[CobonController::class,'enable']);

 Route::get('/hotel_req/{hotel_req}',[HotelRequestController::class,'show']);
 Route::get('/hotel_req/{hotel_req}/confirm',[HotelRequestController::class,'confirmed']);
 Route::get('/hotel_req/{hotel_req}/reject',[HotelRequestController::class,'rejected']);
 Route::post('/hotel_req/{hotel_req}',[HotelRequestController::class,'update']);
 Route::delete('/hotel_req/{hotel_req}',[HotelRequestController::class,'destroy']);

 Route::post('/cobons/set_var',[CobonController::class,'set_var']);
 Route::get('/variable/get/{key}',[VariablesController::class,'get']);

 Route::post('/variable/store',[VariablesController::class,'store']);

 //});
