<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    protected $guarded = [
    	'id'
    ];

    public $timestamps = false;

	public function customerAddress()
	{
		return $this->belongsTo(CustomerAddress::class, 'customer_address_id', 'id');
	}
}
