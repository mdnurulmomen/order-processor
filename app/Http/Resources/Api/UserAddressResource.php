<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
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

            'id' => $this->id,
            'house' => $this->house,
            'road' => $this->road,
            'lat' => $this->lat,
            'lang' => $this->lang,
            'address_name' => $this->address_name,
            'favourites' => $this->favourites
            'additional_hint' => $this->additional_hint,

        ];
    }
}
