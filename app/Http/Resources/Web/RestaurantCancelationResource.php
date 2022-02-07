<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantCancelationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
           'reason_id'=>$this->reason_id,  
           'restaurant_id'=>$this->canceller_id, 
           'restaurant_name'=>$this->canceller->name, 
        ];
    }
}
