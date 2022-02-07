<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Rider;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderRestaurant;
use App\Models\RestaurantOrderRecord;
use App\Models\OrderPickUpProgression;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDeliveryProgression;
use App\Models\RiderOrderCancelationReason;
use App\Models\RestaurantOrderCancelationReason;

class RiderDeliveryRecord extends Model
{
    protected $guarded = [
		'id'
	];

	// public $timestamps = false;

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function rider()
	{
		return $this->belongsTo(Rider::class, 'rider_id', 'id');
	}

	public function restaurants()
	{
		return $this->hasMany(OrderRestaurant::class, 'order_id', 'order_id');
	}

	public function restaurantsAccepted()
	{
		return $this->hasMany(RestaurantOrderRecord::class, 'order_id', 'order_id')->where('food_order_acceptance', 1);
	}

	public function orderCancelations()
	{
		return $this->hasMany(OrderCancelation::class, 'order_id', 'order_id');
	}

	public function restaurantOrderCancelations()
   	{
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Restaurant');
   	}

   	public function riderOrderCancelations()
   	{
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Rider');
   	}

	public function delivery()
	{
	 	return $this->hasOne(OrderDeliveryInfo::class, 'order_id', 'order_id');
	}

	public function riderFoodPickConfirmations()
	{
		return $this->hasMany(OrderPickUpProgression::class, 'order_id', 'order_id');
	}

	public function riderDeliveryConfirmation()
	{
		return $this->hasOne(OrderDeliveryProgression::class, 'order_id', 'order_id');
	}

	/*
	public function riderDeliveryReturn()
	{
		return $this->hasOne(RiderDeliveryReturn::class, 'order_id', 'order_id');
	}
	*/
}
