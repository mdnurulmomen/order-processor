<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
	protected $guarded = [
      'id'
   ];

   protected $casts = [
      'has_cutlery' => 'boolean',
      'is_asap_order' => 'boolean',
      // 'customer_confirmation' => 'boolean',
      // 'in_progress' => 'boolean',
   ];

   public function address()  // delivery order
   {
      return $this->hasOne(DeliveryAddress::class, 'order_id', 'id');
   }

   public function serve()
   {
      return $this->hasOne(ServingOrder::class, 'order_id', 'id');
   }

   public function reservation()
   {
      return $this->hasOne(Reservation::class, 'order_id', 'id');
   }

   public function schedule()
   {
      return $this->hasOne(ScheduleOrder::class, 'order_id', 'id');
   }

   public function payment()
	{
		return $this->hasOne(OrderPayment::class, 'order_id', 'id');
	}

   public function merchants()
   {
      return $this->hasMany(MerchantOrder::class, 'order_id', 'id');
   }

   public function readyMerchants()
   {
      return $this->hasMany(MerchantOrder::class, 'order_id', 'id')->where('order_ready_confirmation', 1);
   }

   public function riders()            // riders who got request for this order
   {
      return $this->hasMany(RiderDelivery::class, 'order_id', 'id');
   }

   public function riderAssigned()   // rider who accepted request for this order
   {
      // There might be many riders got request for a single order untill one accepted
      return $this->hasOne(RiderDelivery::class, 'order_id', 'id')->where('delivery_order_acceptance', 1);
   }

   public function collections()
   {
      return $this->hasMany(RiderCollection::class, 'order_id', 'id');
   }

   /*
   public function riderDeliveryReturn()
   {
      return $this->hasOne(RiderDeliveryReturn::class, 'order_id', 'id');
   }
   */

   public function orderCancelations()
   {
      return $this->hasMany(OrderCancelation::class, 'order_id', 'id');
   }

   public function customerOrderCancelation()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Customer');
   }

   public function merchantOrderCancelations()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Merchant');
   }

   public function riderOrderCancelations()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Rider');
   }

   public function adminOrderCancelation()
   {
      return $this->orderCancelations()->where('canceller_type', 'App\Models\Admin');
   }

   public function orderer()
   {
     return $this->morphTo(__FUNCTION__, 'orderer_type', 'orderer_id');
   }

}
