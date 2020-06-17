<?php

namespace App\Models;

use App\Models\OrderItem;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderPaymentDetail;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
   	protected $guarded = [
         'id'
      ];

      public function payment()
   	{
   		return $this->hasOne(OrderPaymentDetail::class, 'order_id', 'id');
   	}

   	public function orderItems()
   	{
   		return $this->hasMany(OrderItem::class, 'order_id', 'id');
   	}

   	public function deliveryAddress()
   	{
   		return $this->hasOne(OrderDeliveryInfo::class, 'order_id', 'id');
   	}
}
