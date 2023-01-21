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

   public function address()  // Delivery Order
   {
      return $this->hasOne(DeliveryAddress::class, 'order_id', 'id');
   }

   public function serve() // Serving Order
   {
      return $this->hasOne(ServingOrder::class, 'order_id', 'id');
   }

   public function take() // Take-Away Order
   {
      return $this->hasMany(TakingOrder::class, 'order_id', 'id');      // as can order multiple merchants
   }

   public function reservation() // Reservation Order
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
      return $this->hasMany(MerchantOrder::class, 'order_id', 'id')->where('is_ready', 1);
   }

   public function riders()            // all riders who got request for this order
   {
      return $this->hasMany(RiderDelivery::class, 'order_id', 'id');
   }

   public function riderAssigned()   // rider who accepted request for this order
   {
      return $this->hasOne(RiderDelivery::class, 'order_id', 'id')->where('is_accepted', 1);
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

   public function orderCancellations()
   {
      return $this->hasMany(OrderCancellation::class, 'order_id', 'id');
   }

   public function customerOrderCancellation()
   {
      return $this->orderCancellations()->where('canceller_type', 'App\Models\Customer');
   }

   public function merchantOrderCancellations()
   {
      return $this->orderCancellations()->where('canceller_type', 'App\Models\Merchant');
   }

   public function riderOrderCancellations()
   {
      return $this->orderCancellations()->where('canceller_type', 'App\Models\Rider');
   }

   public function adminOrderCancellation()
   {
      return $this->hasOne(OrderCancellation::class, 'order_id', 'id')->where('canceller_type', 'App\Models\Admin');
   }

   public function orderer()
   {
     return $this->morphTo(__FUNCTION__, 'orderer_type', 'orderer_id');
   }

}
