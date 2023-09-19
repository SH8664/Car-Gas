<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Requests\CobonRequestController;
use App\Http\Controllers\Requests\HotelRequestController;
use App\Models\Requests\CobonRequest;
use App\Models\Requests\HotelRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PassportAuthController extends Controller
{
    //for the login of the user
  public function register(Request $request)
  {
    $this->validate( $request ,[
        'name' =>'required',
        'email' =>'email',
        'password' =>'required|min:8',
        'performance_num' =>'required|unique:users,performance_num',
        'phone'=>'required',
        'whatsapp_phone'=>'required',
        'administration'=>'required',
        'region'=>'required',
        'title'=>'required',
        'social_status'=>'required',
        'limited_cobons'=>'required',
        'isadmin'
    ]
    );

    $user =User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'performance_num'=> $request->performance_num,
        'phone'=> $request->phone,
        'whatsapp_phone'=> $request->whatsapp_phone,
        'administration'=>$request->administration,
        'region'=>$request->region,
        'title'=>$request->title,
        'social_status'=>$request->social_status,
        'isadmin'=>$request->isadmin,
        'limited_cobons'=>$request->limited_cobons
    
    ]);
    // return response()->json(['token' => 'message'],200);
    //for the security of the data of the user like the salt of the password
    // $token =$user->createToken('123456789')->accessToken;
    return response()->json(['message' => 'user has been created successfully'],200);
  }
  public function login(Request $request)
  {
   $data=[
        'performance_num' => $request->performance_num,
        'password' => $request->password,
   ];
   if(auth()->attempt($data))
   {
    //  $token =auth()->user()->createToken('123456789')->accessToken;
     return response()->json(['message' => 'login succeeded'],200);
   }else{
    return response()->json(['error' => 'unauthorized access'],401);
   }
   
   
  }

  public function update(Request $request, User $user, $id)
  {
    $input = $request->all();
    $validator = Validator::make($input, [
      'name' =>'required',
      //'email' =>'email',
      //'password' =>'required|min:8',
      'performance_num' =>'',
      'phone'=>'required',
      'whatsapp_phone'=>'required',
      'administration'=>'required',
      'region'=>'required',
      'title'=>'required',
      'social_status'=>'required',
      //'limited_cobons'=>'required',
      //'isadmin'
    ]);
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => 'sorry not stored ',
            'error' => $validator->errors()
        ]);
    }
    // the creation of the User$user 
    $user = User::where('performance_num', $id)->get()->first();
    $user->name = $input['name'];
    //$user->email = $input['email'];
    //$user->password =  $input['password'];
    $user->performance_num = $input['performance_num'];
    $user->phone = $input['phone'];
    $user->whatsapp_phone = $input['whatsapp_phone'];
    $user->administration = $input['administration'];
    $user->region = $input['region'];
    $user->title = $input['title'];
    $user->social_status = $input['social_status'];
   // $user->limited_cobons = $input['limited_cobons'];
    //$user->isadmin = $input['isadmin'];
    //you have to make save to update it in the database
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User update successfully ',
        'User' => $user
    ]);

  }
  public function getName($performance_num)
  {
    $user = User::where('performance_num', $performance_num)->get()->first();
    if($user)
    {
      return $user->name;
    }
  }
  public function userinfo()
  {
    $user = auth()->user();
   return response()->json(['user'=>$user,200]);
  }

  public function User($user)  {
    return response()->json(['user'=>User::where('performance_num',$user)->first()],200);
  }


  public function latest_req($performance_num)
  {
    $school_controller=new School_monController();
    $school_req=$school_controller->latest_schoolmoney($performance_num);
    $cobon_controller=new CobonRequestController();
    $cobon_req=$cobon_controller->latest_cobon($performance_num);
    $hotel_controller=new HotelRequestController();
    $hotel_req=$hotel_controller->latest_hotel($performance_num);
    $resort_controller=new ResortRequestController();
    $resort_req=$resort_controller->latest_resort($performance_num);
    return response()->json([
      'CobonRequest'=>$cobon_req,
      'SchoolRequest'=>$school_req,
      'HotelRequest'=>$hotel_req,
      'ResortRequest'=>$resort_req
    ]);

  }
}