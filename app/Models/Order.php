<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $guarded = [
      'id'
   ];

   protected $casts = [
      // 'is_asap_order' => 'boolean',
      // 'cutlery_addition' => 'boolean',
      // 'customer_confirmation' => 'boolean',
      // 'in_progress' => 'boolean',
   ];

   public function delivery()
   {
      return $this->hasOne(OrderDeliveryInfo::class, 'order_id', 'id');
   }

   public function serve()
   {
      return $this->hasOne(ServeOrder::class, 'order_id', 'id');
   }

   public function reservation()
   {
      return $this->hasOne(Reservation::class, 'order_id', 'id');
   }

   public function asap()
   {
      return $this->hasOne(AsapOrder::class, 'order_id', 'id');
   }

   public function schedule()
   {
      return $this->hasOne(ScheduleOrder::class, 'order_id', 'id');
   }

   public function cutlery()              // only for food items
   {
      return $this->hasOne(OrderCutlery::class, 'order_id', 'id');
   }

   public function payment()
	{
		return $this->hasOne(OrderPayment::class, 'order_id', 'id');
	}

   public function riderCall()
   {
      return $this->hasOne(RiderDeliveryRecord::class, 'order_id', 'id');
   }

   public function restaurants()
   {
      return $this->hasMany(OrderRestaurant::class, 'order_id', 'id');
   }

   public function restaurantAcceptances()
   {
      return $this->hasMany(RestaurantOrderRecord::class, 'order_id', 'id');
   }

   public function riderAssignment()
   {
      // There might be many riders got request for a single order untill one accepted
      return $this->hasOne(RiderDeliveryRecord::class, 'order_id', 'id')->where('delivery_order_acceptance', 1);
   }

   public function orderReadyConfirmations()
   {
      return $this->hasMany(OrderReadyConfirmation::class, 'order_id', 'id');
   }

   public function riderFoodPickConfirmations()
   {
      return $this->hasMany(OrderPickUpProgression::class, 'order_id', 'id');
   }

   public function riderDeliveryConfirmation()
   {
      return $this->hasOne(OrderDeliveryProgression::class, 'order_id', 'id');
   }

   /*
   public function riderDeliveryReturn()
   {
      return $this->hasOne(RiderDeliveryReturn::class, 'order_id', 'id');
   }
   */

   public function orderServeConfirmation()
   {
      return $this->hasOne(OrderServeProgression::class, 'order_id', 'id');
   }

   public function orderCancelations()
   {
      return $this->hasMany(OrderCancelation::class, 'order_id', 'id');
   }

   public function orderer()
   {
     return $this->morphTo(__FUNCTION__, 'orderer_type', 'orderer_id');
   }

   public function customerOrderCancelation()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Customer');
   }

   public function restaurantOrderCancelations()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Restaurant');
   }

   public function riderOrderCancelations()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Rider');
   }

   public function adminOrderCancelation()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Admin');
   }

}
