<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantOrder extends Model
{
    protected $guarded = [
		'id'
	];

	public $timestamps = false;

	public function products()
	{
		return $this->hasMany(ProductOrder::class, 'merchant_order_id', 'id');
	}

	public function serveProgression()
	{
		return $this->hasOne(ServeOrder::class, 'order_id', 'order_id');
	} 

	public function orderCancellations()
	{
		return $this->hasMany(OrderCancelation::class, 'order_id', 'order_id');
	}

	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function merchant()
	{
		return $this->belongsTo(Merchant::class, 'merchant_id', 'id')->withTrashed();
	}
}
