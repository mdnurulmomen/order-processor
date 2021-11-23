<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'user_name' => $this->user_name,
            'mobile' => $this->mobile,
            'email' => $this->email,
            'app_notification' => $this->app_notification,
            'email_notification' => $this->email_notification,
            // 'status' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'addresses' => UserAddressResource::collection($this->addresses),
            'coupons' => $this->coupons,
            'orders' => /*OrderResource::collection($this->orders)*/ route('api.v1.user-orders.index', ['user' => $this->id, 'perPage' => 10, 'page=1']),
            'reservations' => /*OrderResource::collection($this->orders)*/ route('api.v1.user-reservations.index', ['user' => $this->id, 'perPage' => 10, 'page=1'])
        ];
    }
}
