<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResponseResource extends JsonResource
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

            'api_token' => "",

            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_name' => $this->user_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            // 'status' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'addresses' => UserAddressResource::collection($this->addresses),
            'coupons' => $this->coupons,
            'orders' => UserOrderResource::collection($this->orders),
            
        ];
    }
}
