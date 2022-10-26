<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOrder extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	protected $casts = [
		'is_addon_added' => 'boolean',
	];

	public function variation()
	{
		return $this->hasOne(ProductVariationOrder::class, 'product_order_id', 'id');
	}

	public function addons()
	{
		return $this->hasMany(ProductAddonOrder::class, 'product_order_id', 'id');
	}

	public function customization()
	{
		return $this->hasOne(ProductOrderCustomization::class, 'product_order_id', 'id');
	}

	public function merchantProduct()
	{
		return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id')->withTrashed();
	}
}
