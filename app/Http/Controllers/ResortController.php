<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Resort;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
//added isadmin
class ResortController extends Controller
{
    //retrieve
    public function index()
    {
        $Resort = Resort::all();
        return response()->json([
            'success' => true,
            'message' => 'All Resorts',
            'Resort' => $Resort
        ]);
    }
    public function store(Request $request)
    {
        // $user = auth()->user();
        // if ($user['isadmin'] == true) {
            $input = $request->all();
            $validator = Validator::make($input, [
                'name' => 'required',
                'start_time' => 'required',
                'end_time' => 'required',
                'price1' => 'required',
                'price2' => 'required',
                'is_available'=>''
            ]);
            if (Resort::where('name', $request->name)
            ->where('start_time', $request->start_time)
            ->where('end_time', $request->end_time)
            ->exists()
        )
            return  BaseController::sendError('Please validate error', ['error' => 'Duplicate row']);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'sorry not stored ',
                    'error' => $validator->errors()
                ],404);
            } else {
                // the creation of the Resort 
                $Resort = Resort::create($input);
                return response()->json([
                    'user' => auth()->user(),
                    'success' => true,
                    'message' => 'Resort saved successfully ',
                    'Resort' => $Resort
                ]);
            }
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'access denied (Not admin)',

        //     ]);
        // }
    }


    //search for a specific Resort
    public function show($id)
    {
        $Resort = Resort::find($id);

        if (is_null($Resort)) {
            return response()->json([
                'success' => false,
                'message' => 'sorry not found ',

            ],404);
        }
        // the creation of the Resort 

        return response()->json([
            'success' => true,
            'Resort' => $Resort
        ]);
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'price1' => 'required',
            'price2' => 'required',
            'is_available'=>''
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'sorry not stored ',
                'error' => $validator->errors()
            ],404);
        }
        // the creation of the Resort 
        $Resort=Resort::find($id);
       
        $Resort->name = $input['name'];
        $Resort->start_time = $input['start_time'];
        $Resort->end_time = $input['end_time'];
        $Resort->price1 = $input['price1'];
        $Resort->price2 = $input['price2'];

        $Resort->is_available = $input['is_available'];
        //you have to make save to update it in the database
        $Resort->save();
        

        return response()->json([
            'success' => true,
            'message' => 'Resort update successfully ',
            'Resort' => $Resort
        ]);
    }
    public function destroy($id)
    {
        //$user = auth()->user();
       // if ($user['isadmin'] == true) {
        $Resort=Resort::find($id);

            $Resort->delete();

            return response()->json([
                'success' => true,
                'message' => 'Resort has been deleted successfully ',
                'Resort' => $Resort
            ]);
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'access denied (Not admin)',

        //     ]);
        // }
    }
}
