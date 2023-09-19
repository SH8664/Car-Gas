<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TravelAllowance extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user= User::where('performance_num', $this->user_id)->get()->first();
        $user_name=$user['name'];
        $title=$user['title'];
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'user_name'=>$user_name,
            'title'=>$title,
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'days_count'=>$this->days_count,
            'from'=>$this->from,
            'to'=>$this->to,
            'accommodation_type'=>$this->accommodation_type,
            'meals_count'=>$this->meals_count,
            'meals_cost'=>$this->meals_cost,
            'transport_count'=>$this->transport_count,
            'transport_cost'=>$this->transport_cost,
            'travel_cost'=>$this->travel_cost,
            'total'=>$this->total,
        ];
    }
}
