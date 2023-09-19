<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Cobon;
use App\Models\User;

class CobonRequest extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $cobon=Cobon::find($this->cobon_id);
        $resturant_name=$cobon['resturant_name'];
        $price=$cobon['price'];
        $type_of_price=$cobon['type_of_price'];

        $user_name= User::where('performance_num', $this->user_id)->get()->first()['name'];
        $partner_name=null;
        if($this->has_partner)
        $partner_name= User::where('performance_num', $this->partner_id)->get()->first()['name'];
        
        return [
            'id' => $this->id,
            'user_name'=>$user_name,
            'resturant_name'=>$resturant_name,
            'price'=>$price,
            'type_of_price'=>$type_of_price,
            'cobon_id' => $this->cobon_id,
            'user_id'=>$this->user_id,
            'payment_way' => $this->payment_way,
            'amount'=>$this->amount,
            'has_partner'=>$this->has_partner,
            'partner_id'=>$this->partner_id,
            'partner_name'=>$partner_name,
            'status'=>$this->status,
            'created_at' => $this->created_at->format('d/m/y'),
            'updated_at'=> $this->updated_at->format('d/m/y'),
        ];      
    }
}