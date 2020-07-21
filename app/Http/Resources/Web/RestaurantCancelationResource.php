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
        // return parent::toArray($request);
        return [
           'reason_id'=>$this->reason_id,  
           'restaurant'=>$this->restaurant, 
           'restaurant_id'=>$this->restaurant_id, 
        ];
    }
}
