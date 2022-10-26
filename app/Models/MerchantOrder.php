<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantOrder extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	protected $casts = [
		'is_accepted' => 'boolean',
		'is_ready' => 'boolean',
		'is_free_delivery' => 'boolean',
		'has_delivery_support' => 'boolean',
		'is_payment_settled' => 'boolean'
	];

	public function products()
	{
		return $this->hasMany(ProductOrder::class, 'merchant_order_id', 'id');
	}

	public function serve()
	{
		return $this->hasOne(ServingOrder::class, 'order_id', 'order_id');
	} 

	public function orderCancellations()
	{
		return $this->hasMany(OrderCancellation::class, 'order_id', 'order_id');
	}

	public function customerOrderCancellation()
	{
		return $this->orderCancellations()->where('canceller_type', 'App\Models\Customer');
	}

	public function merchantOrderCancellations()
	{
		return $this->orderCancellations()->where('canceller_type', 'App\Models\Merchant');
	}

	public function adminOrderCancellation()
	{
		return $this->hasOne(OrderCancellation::class, 'order_id', 'id')->where('canceller_type', 'App\Models\Admin');
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
