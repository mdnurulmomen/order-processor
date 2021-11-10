<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantBookingResource extends JsonResource
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
            'total_seat' => $this->total_seat,
            'engaged_seat' => $this->engaged_seat,
            'seat_left' => $this->seat_left,
        ];
    }
}
