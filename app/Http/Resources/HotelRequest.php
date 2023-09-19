<?php

namespace App\Http\Resources;

use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $hotel=Hotel::find($this->hotel_id);
        $hotel_name=$hotel['name'];
        $start=$hotel['start'];
        $end=$hotel['end'];

        $user_name= User::where('performance_num', $this->user_id)->get()->first()['name'];
        $rooms=json_decode($this->room,true);
        $total_rooms=0;
        if($rooms)  
        foreach ($rooms as $index=>$room) {
            $total_rooms+=$room['count'];
        }
        $dependents=json_decode($this->dependents,true);
        $total_dependent=0;
        if($dependents)
        $total_dependent=sizeof($dependents);
        return [
            'id'=>$this->id,
            'hotel_id'=>$this->hotel_id,
            'user_id' =>$this->user_id,
            'user_name'=>$user_name,
            'hotel_name'=>$hotel_name,
            'start'=>$start,
            'end'=>$end,
            'dependents'=>$this->dependents,
            'room' =>$this->room,
            'status' =>$this->status,
            'created_at' => $this->created_at->format('d/m/y'),
            'updated_at'=> $this->updated_at->format('d/m/y'),
            'total_rooms'=>$total_rooms,
            'total_dependent'=>$total_dependent
        ];
    }
}