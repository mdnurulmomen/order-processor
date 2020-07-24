<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderReadyConfirmationResource extends JsonResource
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
            'id' => $this->id,
            'food_ready_confirmation' => $this->food_ready_confirmation,
            'order_id' => $this->order_id,
            'restaurant_id' => $this->restaurant_id,
            'restaurant' => $this->restaurant,
        ];
    }
}
