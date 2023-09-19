<?php

namespace App\Http\Controllers\Requests;

use App\Http\Controllers\Controller;
use App\Models\Requests\HotelRequest;
use App\Http\Controllers\BaseController;
use App\Http\Resources\HotelRequest as ResourcesHotelRequest;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\callback;

class HotelRequestController extends Controller
{

    public function index()
    {
        $hotel_req = HotelRequest::all();
        return BaseController::sendResponse(
            ResourcesHotelRequest::collection($hotel_req),
            "All hotel requests are sent"
        );

    }


    public function store(Request $request)
    {
        // $user=auth()->user();
        // $request['user_id']=$user['performance_num'];


        $rooms=$request['room'];
        if($rooms)  
        foreach ($rooms as $index=>$room) {
            $rooms[$index]=json_decode($request['room'][$index],true);
        }
        $request['room']=$rooms;



        $input = $request->all();
        $validator = Validator::make($input, [
            'hotel_id' => 'required|numeric|exists:hotels,id',
            'user_id' => 'required|numeric|exists:users,performance_num',
            'room' => 'required|present|array',
            'room.*.type' => 'required|in:single,double,triple',
            'room.*.count' => 'required|numeric|gt:0',
            'dependents'=> 'nullable|array',
            'dependents.*.relation_type' => 'required',
            'dependents.*.name' => 'required',
            'dependents.*.birth_date' =>'required|date',
            'dependents.*.attachment' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048|dimensions:min_width=100,min_height=100,max_width=3000,max_height=3000'

        ]);
        
        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }



        $dependents = $request->input('dependents');
        if($dependents!=null)
       { foreach ($dependents as $index => $dependent) {
            if($request->hasFile("dependents.$index.attachment")){
                $file = $request->file("dependents.$index.attachment");
                $name = time().'.'.$file->getClientOriginalExtension();
                $filePath = $file->move('uploads', $name);
                $input['dependents'][$index]['attachment']=$name;
            }
        }

        $input['dependents'] = json_encode($input['dependents']);
    }
        $input['room']=json_encode($rooms);
        $hotel_req = HotelRequest::create($input);
        return BaseController::sendResponse(new ResourcesHotelRequest($hotel_req), 'Hotel request created successfully');
    }


    public function update(Request $request,HotelRequest $hotel_req)
    {
        // $user=auth()->user();
        // $request['user_id']=$user['performance_num'];


        $rooms=$request['room'];
        if($rooms)
        foreach ($rooms as $index=>$room) {
            $rooms[$index]=json_decode($request['room'][$index],true);
        }
        $request['room']=$rooms;

        $input = $request->all();
        $validator = Validator::make($input, [
            'hotel_id' => 'required|numeric|exists:hotels,id',
            'user_id' => 'required|numeric|exists:users,performance_num',
            'room' => 'required|present|array',
            'room.*.type' => 'required|in:single,double,triple',
            'room.*.count' => 'required|numeric|gt:0',
            'dependents'=> 'nullable',
            'dependents.*.relation_type' => 'required',
            'dependents.*.name' => 'required',
            'dependents.*.birth_date' =>'required|date',
            'dependents.*.attachment' => 'required'

        ]);
        
        if ($validator->fails()) {
            return  BaseController::sendError('Please validate error', $validator->errors());
        }



        $dependents = $request->input('dependents');
        // var_dump($dependents);
        if($dependents!=null)
       { foreach ($dependents as $index => $dependent) 
            if($request->hasFile("dependents.$index.attachment")){
                $file = $request->file("dependents.$index.attachment");
                $name = time().'.'.$file->getClientOriginalExtension();
                $filePath = $file->move('uploads', $name);
                $input['dependents'][$index]['attachment']='/storage/'.$filePath;
            }
            $input['dependents'] = json_encode($input['dependents']);
            $hotel_req->dependents = $input['dependents'];
        }
        else{
            $hotel_req->dependents =null;
        }

    
        // $input['room'] = json_encode($input['room']);
        // foreach ($rooms as $index=>$room) {
        //     $rooms[$index]=json_encode($input['room'][$index]);
        // }
        $input['room']=json_encode($rooms);


        $hotel_req->hotel_id = $input['hotel_id'];
        $hotel_req->user_id = $input['user_id'];
        $hotel_req->room = $input['room'];
        $hotel_req->status = $input['status'];
        $hotel_req->updated_at =  now()->format('y-m-d');
        


        
        $hotel_req->save();

        return BaseController::sendResponse(new ResourcesHotelRequest($hotel_req), 'Hotel request updated successfully');
    }


    public function show(HotelRequest $hotel_req)
    {
        $hotel_req = HotelRequest::find($hotel_req);
        if (is_null($hotel_req)) {
            return BaseController::sendError('Hotel not found');
        }
        return BaseController::sendResponse(ResourcesHotelRequest::collection($hotel_req), 'Hotel request found successfully');
    }




    public function confirmed(HotelRequest $hotel_req)
    {
        $user=auth()->user();
        if($user['isadmin']){
            $hotel_req->status = 'confirmed';
            $hotel_req->save();
            return BaseController::sendResponse(new ResourcesHotelRequest($hotel_req), 'Hotel request confirmed successfully');
        }
        
    }

    public function rejected(HotelRequest $hotel_req)
    {
        $user=auth()->user();
        if($user['isadmin']){
            $hotel_req->status = 'rejected';
            $hotel_req->save();
            return BaseController::sendResponse(new ResourcesHotelRequest($hotel_req), 'Hotel request rejected successfully');
        }
    }


    public function destroy(HotelRequest $hotel_req)
    {
        $hotel_req->delete();
        return BaseController::sendResponse(new ResourcesHotelRequest($hotel_req), 'Hotel request deleted successfully');
    }
    public function upload(Request $request)
    {
        $hotel_id = $request->input('hotel_id');
    
        // Iterate over dependents
        $dependents = $request->input('dependents');
        foreach ($dependents as $index => $dependent) {
            $relation_type = $dependent['relation_type'];
            $name = $dependent['name'];
            $birth_date = $dependent['birth_date'];
            if($request->hasFile("dependents.$index.attachment")){
                $file = $request->file("dependents.$index.attachment");
                $name = time().'.'.$file->getClientOriginalExtension();
                $filePath = $file->move('uploads', $name);
                // save this $filePath in database
            }
        }
    
        // Iterate over room
        $rooms = $request->input('room');
        foreach ($rooms as $room) {
            $roomData = json_decode($room, true);
            $type = $roomData['type'];
            $count = $roomData['count'];
    
            // Handle room data here...
        }
    
        // Rest of your logic here...
        return response()->json([
            'success' => true,
            'message' => 'Data and files successfully uploaded'
        ], 200);
    }
    

    public function latest_hotel($pn){
        $hotel_req= HotelRequest::where('created_at',HotelRequest::where('user_id',$pn)->where('status','confirmed')->max('created_at'))->first();
        if($hotel_req!=null)
        return BaseController::sendResponse(new ResourcesHotelRequest($hotel_req), 'Hotel request deleted successfully');
    }


} 