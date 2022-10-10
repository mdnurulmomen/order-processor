<?php

namespace App\Http\Resources\Web;

use Illuminate\Http\Resources\Json\JsonResource;

class ServingOrderResource extends JsonResource
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

            'is_served' => $this->is_served,
            'confirmer' => $this->confirmer,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}
