<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRegularMerchant extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	public function products()
	{
		return $this->hasMany(MyRegularProduct::class, 'my_regular_merchant_id', 'id');
	}

	public function myRegular()
	{
		return $this->belongsTo(MyRegular::class, 'my_regular_id', 'id');
	}

	public function merchant()
	{
		return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
	}
}
