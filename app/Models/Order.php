<?php

namespace App\Models;

use App\Events\UpdateAdmin;
use App\Events\UpdateRestaurant;
use App\Models\OrderedRestaurant;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderPaymentDetail;
use App\Models\RiderDeliveryRecord;
use App\Models\OrderServeProgression;
use App\Models\RestaurantOrderRecord;
use App\Models\OrderPickUpProgression;
use App\Models\OrderReadyConfirmation;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDeliveryProgression;
use App\Models\RiderOrderCancelationReason;
use App\Models\RestaurantOrderCancelationReason;

class Order extends Model
{
   	protected $guarded = [
         'id'
      ];

      protected $casts = [
         'cutlery_addition' => 'boolean',
      ];

      /**
      * The "booted" method of the model.
      *
      * @return void
      */
      protected static function boot()
      {
         parent::boot();

         static::saved(function ($order) {
            
            // Broadcast for order confirmation
            if ($order->call_confirmation===1) {
               
               foreach ($order->restaurants as $orderedRestaurant) {

                  $order->restaurantAcceptances()->create([
                     'food_order_acceptance' => -1, // ringing
                     'restaurant_id' => $orderedRestaurant->restaurant_id,
                  ]);
                  
                  event(new UpdateRestaurant($orderedRestaurant));

               }

            }

            // Broadcast for admin
            event(new UpdateAdmin($order));

         });

      }

      /**
      * Get the owning orderer model.
      */
      public function orderer()
      {
        return $this->morphTo(__FUNCTION__, 'orderer_type', 'orderer_id');
      }

      public function payment()
   	{
   		return $this->hasOne(OrderPaymentDetail::class, 'order_id', 'id');
   	}

   	public function restaurants()
      {
         return $this->hasMany(OrderedRestaurant::class, 'order_id', 'id');
      }

      public function delivery()
      {
         return $this->hasOne(OrderDeliveryInfo::class, 'order_id', 'id');
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

      public function waiterServeConfirmation()
      {
         return $this->hasOne(OrderServeProgression::class, 'order_id', 'id');
      }

      public function restaurantOrderCancelations()
      {
         return $this->hasMany(RestaurantOrderCancelationReason::class, 'order_id', 'id');
      }

      public function riderOrderCancelation()
      {
         return $this->hasOne(RiderOrderCancelationReason::class, 'order_id', 'id');
      }
}
