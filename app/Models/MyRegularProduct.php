<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRegularProduct extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	public function variation()
	{
		return $this->hasOne(MyRegularProductVariation::class, 'my_regular_product_id', 'id');
	}

	public function addons()
	{
		return $this->hasMany(MyRegularProductAddon::class, 'my_regular_product_id', 'id');
	}

   	/*
      public function customization()
   	{
   		return $this->hasOne(MyRegularItemCustomization::class, 'order_item_id', 'id');
   	}
      */

   	public function merchantProduct()
   	{
   		return $this->belongsTo(MerchantProduct::class, 'merchant_product_id', 'id');
   	}
}
