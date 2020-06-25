<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderPaymentDetail;
use App\Models\OrderServeProgression;
use App\Models\RestaurantOrderRecord;
use App\Models\OrderPickUpProgression;
use App\Models\OrderReadyConfirmation;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderDeliveryProgression;

class Order extends Model
{
   	protected $guarded = [
         'id'
      ];

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

   	public function items()
   	{
   		return $this->hasMany(OrderItem::class, 'order_id', 'id');
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
         return $this->hasOne(OrderDeliveryProgression::class, 'order_id', 'id');
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
}
