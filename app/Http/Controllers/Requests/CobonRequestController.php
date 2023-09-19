<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Requests\CobonRequest as CobonRequest;
use App\Http\Resources\CobonRequest as CobonRequestResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;

class CobonRequestController extends Controller
{

    public function index()
    {
        $cobon_req = CobonRequest::all();

        return BaseController::sendResponse(
            CobonRequestResource::collection($cobon_req),
            "All cobon requests are sent"
        );
    }

    public function store(Request $requests)
    {
        // $user = auth()->user();
        // $limit = $user['limited_cobons'];

        $user = User::where('performance_num', $requests['pn'])->get()->first();
        $status=$requests['status'];
        $partner = null;
        if ($has_partner = $requests['has_partner'])
            $partner = User::where('performance_num', $requests['partner_id'])->get()->first();

        $requests = $requests->all()['data'];
        $cobon_reqs = [];
        foreach ($requests as $input) {
            $input['user_id'] = $user['performance_num'];
            $input['status']=$status;
            if ($has_partner)
                $input['partner_id'] = $partner['performance_num'];
            $input['has_partner'] = $has_partner;
            $limit = $user['limited_cobons'];


            $validator = Validator::make($input, [
                'cobon_id' => 'required|numeric|exists:cobons,id',
                'user_id' => 'numeric|exists:users,performance_num',
                'partner_id' => 'nullable|exists:users,performance_num',
                'payment_way' => 'required|in:cash,installments',
                'amount' => 'required|numeric|gt:0|lte:' . $limit,
            ]);

            if ($validator->fails()) {
                return BaseController::sendError('Please validate error', $validator->errors(), 403);
            } else {
                $cobon_req = CobonRequest::create($input);
                $user['limited_cobons'] -= $input['amount'];
                $user->save();
                if ($has_partner) {
                    $partner['limited_cobons'] -= $input['amount'];
                    $partner->save();
                }

                $cobon_reqs[] = $cobon_req;
            }
        }
        return BaseController::sendResponse(CobonRequestResource::collection($cobon_reqs), 'Cobon requests created successfully');
    }

    public function update(Request $request, CobonRequest $cobon_req)
    {
        // $user = auth()->user();
        // $limit = $user['limited_cobons'];
        $user = User::where('performance_num', $request['pn'])->get()->first();
        $partner = null;
        if ($has_partner = $request['has_partner'])
            $partner = User::where('performance_num', $request['partner_id'])->get()->first();



        $limit = $user['limited_cobons'] + $cobon_req['amount'];
        $input = $request->all();
        $input['user_id'] = $request['pn'];
        if ($has_partner)
            $input['partner_id'] = $request['partner_id'];
        $input['has_partner'] = $has_partner;
        $validator = Validator::make($input, [
            'cobon_id' => 'required|numeric|exists:cobons,id',
            'user_id' => 'numeric|exists:users,performance_num',
            'payment_way' => 'required|in:cash,installments',
            'amount' => 'required|numeric|gt:0|lte:' . $limit,
        ]);

        if ($validator->fails()) {
            return BaseController::sendError('Please validate error', $validator->errors(), 403);
        }
        if ($input['status'] == 'rejected') {
            $user['limited_cobons'] += $cobon_req['amount'];
            if ($has_partner)
                $partner['limited_cobons'] += $cobon_req['amount'];
        } else {
            if ($input['amount'] > $cobon_req['amount']) {
                $user['limited_cobons'] -= $input['amount'] - $cobon_req['amount'];
                if ($has_partner)
                    $partner['limited_cobons'] -= $input['amount'] - $cobon_req['amount'];
            } else {
                $user['limited_cobons'] += $cobon_req['amount'] - $input['amount'];
                if ($has_partner)
                    $partner['limited_cobons']  += $cobon_req['amount'] - $input['amount'];
            }
        }

        if ($has_partner)
            $partner->save();

        $user->save();

        $cobon_req->cobon_id = $input['cobon_id'];
        $cobon_req->user_id = $input['user_id'];
        $cobon_req->payment_way = $input['payment_way'];
        $cobon_req->amount = $input['amount'];
        $cobon_req->status = $input['status'];
        $cobon_req->has_partner=$input['has_partner'];
        $cobon_req->updated_at =  now()->format('y-m-d');
        if($has_partner)
            $cobon_req->partner_id=$input['partner_id'];
        else
        $cobon_req->partner_id=null;

        $cobon_req->save();

        return  BaseController::sendResponse(new CobonRequestResource($cobon_req), 'Cobon request updated successfully');
    }


    public function show(CobonRequest $cobon_req)
    {
        $cobon_req = CobonRequest::find($cobon_req);
        if (is_null($cobon_req)) {
            return BaseController::sendError('Cobon not found');
        }
        return BaseController::sendResponse(CobonRequestResource::collection($cobon_req), 'Cobon request found successfully');
    }

    public function confirmed(CobonRequest $cobon_req)
    {
        $user = auth()->user();
        if ($user['isadmin']) {
            $cobon_req->status = 'confirmed';
            $cobon_req->save();
            return BaseController::sendResponse(new CobonRequestResource($cobon_req), 'Cobon request confirmed successfully');
        }
    }

    public function rejected(CobonRequest $cobon_req)
    {
        $user = auth()->user();
        if ($user['isadmin']) {
            $cobon_req->status = 'rejected';
            $cobon_req->save();
            return BaseController::sendResponse(new CobonRequestResource($cobon_req), 'Cobon request rejected successfully');
        }
    }
    public function destroy(CobonRequest $cobon_req)
    {
        $cobon_req->delete();
        return BaseController::sendResponse(new CobonRequestResource($cobon_req), 'Cobon request deleted successfully');
    }

    public function latest_cobon($pn){
        $cobon_req= CobonRequest::where('created_at',CobonRequest::where('user_id',$pn)->where('status','confirmed')->max('created_at'))->first();
        if($cobon_req!=null)
        return BaseController::sendResponse(new CobonRequestResource($cobon_req), 'Cobon request has been sent successfully');

    }
}
