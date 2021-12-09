<?php

namespace App\Models;

use App\Models\Order;
use App\Models\Restaurant;
use App\Models\OrderedRestaurant;
use App\Models\OrderServeProgression;
use Illuminate\Database\Eloquent\Model;
use App\Models\RestaurantOrderCancelationReason;

class RestaurantOrderRecord extends Model
{
	protected $guarded = [
		'id'
	];

	// public $timestamps = false;

  public function order()
  {
    return $this->belongsTo(Order::class, 'order_id', 'id');
  }

	public function restaurant()
  {
    return $this->belongsTo(Restaurant::class, 'restaurant_id', 'id')->withTrashed();
  }

  public function orderedRestaurants()
  {
    return $this->hasMany(OrderedRestaurant::class, 'order_id', 'order_id');
  }

  public function orderServeProgression()
  {
    return $this->hasOne(OrderServeProgression::class, 'order_id', 'order_id');
  } 

  public function orderCancellationReasons()
  {
    return $this->hasMany(RestaurantOrderCancelationReason::class, 'order_id', 'order_id');
  }

  public function orderReadyConfirmations()
  {
     return $this->hasMany(OrderReadyConfirmation::class, 'order_id', 'order_id');
  }

}
