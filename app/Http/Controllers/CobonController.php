<?php

namespace App\Http\Controllers;

use App\Models\Cobon;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Cobon as CobonResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;



class CobonController extends Controller
{

    public function index()
    {
        $cobon = Cobon::all();
        return BaseController::sendResponse(
            CobonResource::collection($cobon),
            "All cobons are sent"
        );
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'resturant_name' => 'required|unique:cobons,resturant_name',
            'type_of_price' => 'required|in:value,percentage',
            'price' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }
        $cobon = Cobon::create($input);
        return BaseController::sendResponse(new CobonResource($cobon), 'Cobon created successfully');
    }

    public function show(Cobon $cobon)
    {
        $cobon = Cobon::find($cobon);
        if (is_null($cobon)) {
            return BaseController::sendError('Cobon not found');
        }
        return BaseController::sendResponse(CobonResource::collection($cobon), 'Cobon found successfully');
    }

    public function update(Request $request, Cobon $cobon)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'resturant_name' => 'required|alpha',
            Rule::unique('cobons', 'resturant_name')->ignore($cobon),
            'type_of_price' => 'required|in:value,percentage',
            'price' => 'required|numeric|gt:0',
        ]);

        if ($validator->fails()) {
         return BaseController::sendError('Please validate error' ,$validator->errors() );
           }
     $cobon->resturant_name = $input['resturant_name'];
     $cobon->type_of_price = $input['type_of_price'];
     $cobon->price = $input['price'];
     $cobon->is_available=$input['is_available'];
     $cobon->updated_at= now()->format('y-m-d');

     $cobon->save();
     return BaseController::sendResponse(new CobonResource($cobon) ,'Cobon updated successfully' );
    }

    public function destroy(Cobon $cobon)
    {
        $cobon->delete();
        return BaseController::sendResponse(new CobonResource($cobon) ,'Cobon deleted successfully' );

    }

}