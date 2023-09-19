<?php

namespace App\Http\Controllers;

use App\Http\Resources\TravelAllowance as ResourcesTravelAllowance;
use App\Models\TravelAllowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Continue_;

class TravelAllowanceController extends Controller
{

    public function index()
    {
        $travelallowance = TravelAllowance::all();
        return BaseController::sendResponse(
            ResourcesTravelAllowance::collection($travelallowance),
            "All travelallowances are sent"
        );
    }


    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'numeric|exists:users,performance_num',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:tommorrow|after:' . $request->start_date,
            'days_count' => 'required|gte:0',
            'from' => 'required',
            'to' => 'required',
            'accommodation_type' => 'required',

            'total' => 'nullable'
        ]);
        // $travelallowances = TravelAllowance::where('user_id', $input['user_id'])->get();
        // foreach ($travelallowances as $travelallowance) {
        //     if ($input['start_date'] > $travelallowance['start_date']) {
        //         if ($input['end_date'] < $travelallowance['end_date']) {
        //             return response()->json([
        //                 'success' => false,
        //                 'message' => 'date interval is invalid ',
        //     ],404);
        //         }
        //     } else {
        //         if ($input['end_date'] > $travelallowance['end_date']) {
        //             return response()->json([
        //                 'success' => false,
        //                 'message' => 'date interval is invalid ',
        //     ],404);
        //         }
        //     }

        // }

        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }


        $input['total'] = ($input['meals_count'] * $input['meals_cost']) +
            ($input['transport_count'] * $input['transport_cost']) +
            $input['travel_cost'];

        $travelallowance = TravelAllowance::create($input);
        return BaseController::sendResponse(new ResourcesTravelAllowance($travelallowance), 'Travel Allowance created successfully');
    }

    public function is_found($user_id, $start_id, $end_date)
    {
        return response()->json(["result" => (TravelAllowance::where('user_id', $user_id)
            ->where('start_date', $start_id)
            ->where('end_date', $end_date)
            ->exists()
        )]);
    }
    public function update(Request $request,  $id)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => 'numeric|exists:users,performance_num',
            'days_count' => 'required|gte:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:tommorrow|after:' . $request->start_date,
            'from' => 'required',
            'to' => 'required',
            'accommodation_type' => 'required',

        ]);
        $travelallowances = TravelAllowance::where('user_id', $input['user_id'])->get();
        foreach ($travelallowances as $travelallowance) {
            if($travelallowance['id']=$id)
            continue;
            if ($input['start_date'] > $travelallowance['start_date']) {
                if ($input['end_date'] < $travelallowance['end_date']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'date interval is invalid ',
            ],404);
                }
            } else {
                if ($input['end_date'] > $travelallowance['end_date']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'date interval is invalid ',
            ],404);
                }
            }

        }
        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }

        $travelAllowance = TravelAllowance::find($id);
        $travelAllowance->user_id = $input['user_id'];
        $travelAllowance->start_date = $input['start_date'];
        $travelAllowance->end_date = $input['end_date'];
        $travelAllowance->days_count = $input['days_count'];
        $travelAllowance->from = $input['from'];
        $travelAllowance->to = $input['to'];
        $travelAllowance->accommodation_type = $input['accommodation_type'];
        $travelAllowance->meals_count = $input['meals_count'];
        $travelAllowance->meals_cost = $input['meals_cost'];
        $travelAllowance->transport_count = $input['transport_count'];
        $travelAllowance->transport_cost = $input['transport_cost'];
        $travelAllowance->travel_cost = $input['travel_cost'];

        $travelAllowance->total = $input['total'] = $input['meals_count'] * $input['meals_cost'] +
            $input['transport_count'] * $input['transport_cost'] +
            $input['travel_cost'];

        $travelAllowance->save();
        return BaseController::sendResponse(new ResourcesTravelAllowance($travelAllowance), 'Travel Allowance updated successfully');
    }


    public function destroy($id)
    {
        $travelAllowance = TravelAllowance::find($id);
        $travelAllowance->delete();
        return BaseController::sendResponse(new ResourcesTravelAllowance($travelAllowance), 'Travel Allowance deleted successfully');
    }
}
