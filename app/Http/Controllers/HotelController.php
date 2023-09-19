<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Hotel;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Hotel as HotelResource;
use Illuminate\Validation\Rule;


class HotelController extends Controller
{

    public function index()
    {
        $hotel = Hotel::all();
        return BaseController::sendResponse(
            HotelResource::collection($hotel),
            "All hotels are sent"
        );
    }


    public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'start' => 'required|date|after:tommorrow',
            'end' => 'required|date|after:tommorrow|after:' . $request->start,
        ]);
        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }

        if (Hotel::where('name', $request->name)
            ->where('start', $request->start)
            ->where('end', $request->end)
            ->exists()
        )
            return  BaseController::sendError('Please validate error', ['error' => 'this hotel with this period already exists']);

        $hotel = Hotel::create($input);
        return BaseController::sendResponse(new HotelResource($hotel), 'Hotel created successfully');
    }

    public function show(Hotel $hotel)
    {
        $hotel=Hotel::find($hotel);
        if (is_null($hotel)) {
            return BaseController::sendError('Hotel not found');
        }
        return BaseController::sendResponse(HotelResource::collection($hotel), 'Hotel found successfully');

    }


    public function update(Request $request, Hotel $hotel)
    {
        $input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
            'start' => 'required|date|after:tommorrow',
            'end' => 'required|date|after:tommorrow|after:' . $request->start,
        ]);
        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }
        
        if (Hotel::where('id','<>',$hotel->id)
            ->where('name', $request->name)
            ->where('start', $request->start)
            ->where('end', $request->end)
            ->exists()
        )
            return  BaseController::sendError('Please validate error', ['error' => 'this hotel with this period already exists']);

        $hotel->name=$request->name;
        $hotel->start=$request->start;
        $hotel->end=$request->end;
        $hotel->is_available=$request->is_available;
        $hotel->updated_at=  now()->format('y-m-d');

        $hotel->save();
        return BaseController::sendResponse(new HotelResource($hotel), 'Hotel updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hotel $hotel)
    {
        $hotel->delete();
        return BaseController::sendResponse(new HotelResource($hotel) ,'Hotel deleted successfully' );

    }
    public function disable(Hotel $hotel)  {

        $hotel->is_available=false;
        $hotel->save();
        return BaseController::sendResponse(new HotelResource($hotel) ,'Hotel disabled successfully' );
    }

    public function enable(Hotel $hotel)  {

        $hotel->is_available=true;
        $hotel->save();
        return BaseController::sendResponse(new HotelResource($hotel) ,'Hotel enabled successfully' );
    }
}