<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Restaurant;
use App\Events\UpdateRider;
use App\Events\UpdateAdmin;
use Illuminate\Database\Eloquent\Model;

class RestaurantOrderRecord extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	/**
      * The "booted" method of the model.
      *
      * @return void
      */
	protected static function boot()
  	{
		parent::boot();

     	static::updated(function ($restaurantOrderRecord) {
            
        // Broadcast for restaurant order acceptance 
        if ($restaurantOrderRecord->food_order_acceptance===1) {

          // checking if order is for home-delivery, order from customer and no rider has benn assigned yet
          if ($restaurantOrderRecord->order->order_type==='home-delivery' && $restaurantOrderRecord->order->orderer() instanceof Customer && !$restaurantOrderRecord->order->riderAssignment) {

            // Broadcast for rider
            event(new UpdateRider($restaurantOrderRecord->order));

          }

        }

        // Broadcast for admin (accept/cancel)
        event(new UpdateAdmin($restaurantOrderRecord->order));

        // Broadcast for restaurant
        // event(new UpdateRestaurant($orderedRestaurant));

     	});

  	}

	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

   	public function restaurant()
	{
		return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id');
	}

}
