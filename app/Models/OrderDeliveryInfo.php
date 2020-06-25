<?php

namespace App\Models;

use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Model;

class OrderDeliveryInfo extends Model
{
   	protected $guarded = [
    	'id'
    ];

    public $timestamps = false;

	public function customerAddress()
	{
		return $this->belongsTo(CustomerAddress::class, 'delivery_address_id', 'id');
	}
}
