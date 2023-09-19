<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ResortRequest;
use App\Models\Resort;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;

class ResortRequestController extends Controller
{
    //retrieve
    public function index()
    {
        // $user = auth()->user();
        // if ($user['isadmin'] == true) {
        $ResortRequest = ResortRequest::all();
        $formattedResortRequests = $ResortRequest->map(function ($request) {
            $resort_first_desire = Resort::find($request->first_desire_id);
            $resort_second_desire = Resort::find($request->second_desire_id);
            $resort_third_desire = Resort::find($request->third_desire_id);

            if($resort_first_desire==null)
            {
               $first_id=null;
               $first_name=null;
               $first_start_date=null;
               $first_end_date=null;
               $first_is_avail=null;
              
        }else{
            $first_id= $resort_first_desire->id;
            $first_name= $resort_first_desire->name;
            $first_start_date= $resort_first_desire->start_time;
            $first_end_date= $resort_first_desire->end_time;
            $first_is_avail= $resort_first_desire->is_available;
        }

        if($resort_second_desire==null)
            {
               $second_id=null;
               $second_name=null;
               $second_start_date=null;
               $second_end_date=null;
               $second_is_avail=null;
              
        }else{
            $second_id= $resort_second_desire->id;
               $second_name= $resort_second_desire->name;
               $second_start_date= $resort_second_desire->start_time;
               $second_end_date= $resort_second_desire->end_time;
               $second_is_avail= $resort_second_desire->is_available;
        }
        if($resort_third_desire==null)
        {
           $third_id=null;
           $third_name=null;
           $third_start_date=null;
           $third_end_date=null;
           $third_is_avail=null;
          
    }else{
        $third_id=$resort_third_desire->id;
        $third_name=$resort_third_desire->name;
        $third_start_date=$resort_third_desire->start_time;
        $third_end_date=$resort_third_desire->end_time;
        $third_is_avail=$resort_third_desire->is_available;
       
    }
    
        
            // if($resort_second_desire)
            return [
                'id' => $request->id,
                'name' => $request->name,
                'performance_num' => $request->performance_num,
                'first_desire_id' => $first_id,
                'first_desire_name' => $first_name,
                'first_desire_start_time' => $first_start_date,
                'first_desire_end_time' => $first_end_date,
                'first_desire_is_available'=>$first_is_avail,

                'second_desire_id' => $second_id,
                'second_desire_name' => $second_name,
                'second_desire_start_time' => $second_start_date,
                'second_desire_end_time' => $second_end_date,
                'second_desire_is_available'=>$second_is_avail,

                'third_desire_id' =>$third_id,
                'third_desire_name' =>$third_name,
                'third_desire_start_time' => $third_start_date,
                'third_desire_end_time' => $third_end_date,
                'third_desire_is_available'=>$third_is_avail,

                'status' => $request->status,
                'relatives' => $request->relatives,
                'created_at' => $request->created_at,
                'updated_at' => $request->updated_at,
            ];
        });
        return response()->json([
            'success' => true,
            'message' => 'All ResortRequests',
            'ResortRequest' => $formattedResortRequests
        ]);
        // } else return response()->json([
        //     'success' => false,
        //     'message' => 'access denied (Not admin)',

        // ]);
    }
    public function store(Request $request)
    {
        $input = $request->all();
        //the relatives attachments
        $validator = Validator::make($input, [
            'name' => '',
            'performance_num' => 'required',
            'first_desire_id' => 'required',
            'second_desire_id' => 'required',
            'third_desire_id' => 'required',
            'status',
            'relatives' => 'array|nullable', // Remove the extra pipe before 'array'
            'relatives.*.name' => '', // Add the rule for relative names
            'relatives.*.attachments' => '', // Corrected the rule for attachments
            'relatives.*.birth_time' => '', // Corrected the rule for attachments
            'relatives.*.relation_type' => '', // Corrected the rule for attachments
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'sorry not stored ',
                'error' => $validator->errors()
            ],404);
        }
        // if (ResortRequest::where('first_desire_id', $request->first_desire_id)
        //     ->where('second_desire_id', $request->second_desire_id)
        //     ->where('third_desire_id', $request->third_desire_id)
        //     ->where('performance_num', $request->performance_num)
        //     ->exists()
        // )
        //     return  BaseController::sendError('duplication', ['error' => 'Duplicate row']);

            $resort_first_desire = Resort::find($request->first_desire_id);
            $resort_second_desire = Resort::find($request->second_desire_id);
            $resort_third_desire = Resort::find($request->third_desire_id);
           
            // if(()($resort_first_desire->is_available==0)
            // ||($resort_second_desire->is_available==0)
            // ||($resort_third_desire->is_available==0))
            // return response()->json([
            //     'success' => false,
            //     'message' => 'one of the resorts is unavailable ',
               
            // ],404);
            $varx=new PassportAuthController();
        $input['name']=$varx->getName($input['performance_num']);
         

        if ((isset($input['relatives']))) {
            $relatives = $input['relatives'];

            foreach ($relatives as $index => $relative) {
                if (isset($relative['attachments']) && $request->hasFile("relatives.$index.attachments")) {
                    $file = $request->file("relatives.$index.attachments");
                    $name = time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->move('uploads', $name);
                    $relative['attachments'] = $name;
                    $input['relatives'][$index]['attachments'] =  $name;
                }
            }
        }

        // Convert relatives array to JSON before storing
        $input['relatives'] = json_encode($input['relatives']);

        // Create the ResortRequest record
        
        $ResortRequest = ResortRequest::create($input);


        return response()->json([
            'success' => true,
            'message' => 'ResortRequest saved successfully ',
            'ResortRequest' => $ResortRequest
        ]);
    }


    //search for a specific ResortRequest
    public function show($id)
    {
        $ResortRequest = ResortRequest::find($id);

        if (is_null($ResortRequest)) {
            return response()->json([
                'success' => false,
                'message' => 'sorry not found ',

            ]);
        }
        // the creation of the ResortRequest 

        return response()->json([
            'success' => true,
            'ResortRequest' => $ResortRequest
        ]);
    }

    public function update(Request $request, ResortRequest $ResortRequest, $id)
    {
        //  $user = auth()->user();
        // if ($user['isadmin'] == true) {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => '',
            'performance_num' => 'required',
            'first_desire_id' => 'required',
            'second_desire_id' => 'required',
            'third_desire_id' => 'required',
            'status',
            'relatives' => '',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'sorry not updated ',
                'error' => $validator->errors()
            ]);
        }

        // the creation of the ResortRequest 
        $ResortRequest = ResortRequest::find($id);
        $varx=new PassportAuthController();
        $input['name']=$varx->getName($input['performance_num']);
         $ResortRequest->name = $input['name'];
        $ResortRequest->performance_num = $input['performance_num'];
        $ResortRequest->first_desire_id = $input['first_desire_id'];
        $ResortRequest->second_desire_id = $input['second_desire_id'];
        $ResortRequest->third_desire_id = $input['third_desire_id'];
        $ResortRequest->status = $input['status'];

        $resort_first_desire = Resort::find($request->first_desire_id);
        $resort_second_desire = Resort::find($request->second_desire_id);
        $resort_third_desire = Resort::find($request->third_desire_id);
        if(($resort_first_desire->is_available==0)
        ||($resort_second_desire->is_available==0)
        ||($resort_third_desire->is_available==0))
        return response()->json([
            'success' => false,
            'message' => 'one of the resorts is unavailable ',
            'error' => $validator->errors()
        ],404);



        if ((isset($input['relatives']))) 
        {
            $relatives = $input['relatives'];

            foreach ($relatives as $index => $relative) 
            {
                if (isset($relative['attachments']) && $request->hasFile("relatives.$index.attachments")) {
                    $file = $request->file("relatives.$index.attachments");
                    $name = time() . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->move('uploads', $name);
                    $relative['attachments'] = $name;
                    $input['relatives'][$index]['attachments'] = $name;
                }
                else{
                    $ResortRequest->relatives=$input['relatives'];
                }
            }
        }

        // Convert relatives array to JSON before storing
        $input['relatives'] = json_encode($input['relatives']);

        $ResortRequest->relatives = $input['relatives'];

        
        //you have to make save to updated it in the database
        $ResortRequest->save();

        return response()->json([
            'success' => true,
            'message' => 'ResortRequest updated successfully ',
            'ResortRequest' => $ResortRequest
        ]);
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'access denied (Not admin)',

        //     ]);
        // }
    }
    //uptime confirmed
    public function confirmed(ResortRequest $resortRequest, $id)
    {
        // $user=auth()->user();
        // if($user['isadmin']){
        $ResortRequest = ResortRequest::find($id);

        $ResortRequest->status = 'confirmed';
        $ResortRequest->save();
        return response()->json([
            'success' => true,
            'message' => 'Status of the request has been changed',

        ]);
        // }else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Access denied (not admin)',

        //     ]);
        // }
    }
    public function rejected(ResortRequest $resortRequest, $id)
    {
        // $user=auth()->user();
        // if($user['isadmin']){
        $ResortRequest = ResortRequest::find($id);

        $ResortRequest->status = 'rejected';
        $ResortRequest->save();
        return response()->json([
            'success' => true,
            'message' => 'Status of the request has been changed',

        ]);
        // }else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Access denied (not admin)',

        //     ]);
        // }
    }
    public function destroy(ResortRequest $ResortRequest, $id)
    {
        // $user = auth()->user();
        // if ($user['isadmin'] == true) {
        $ResortRequest = ResortRequest::find($id);
        $ResortRequest->delete();

        return response()->json([
            'success' => true,
            'message' => 'ResortRequest has been deleted successfully ',
            'ResortRequest' => $ResortRequest
        ]);
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'access denied (Not admin)',

        //     ]);
        // }
    }

    public function latest_resort($pn){
        return ResortRequest::where('created_at',ResortRequest::where('performance_num',$pn)->where('status','confirmed')->max('created_at'))->first();
    }
}