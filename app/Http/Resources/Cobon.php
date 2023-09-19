<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Cobon extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'resturant_name' => $this->resturant_name,
            'price' => $this->price,
            'type_of_price'=>$this->type_of_price,
            'is_available'=>$this->is_available,
            'created_at' => $this->created_at->format('d/m/y'),
            'updated_at'=> $this->updated_at->format('d/m/y'),
        ];
    }
}