<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MyRegular extends Model
{
    protected $guarded = [
 		   'id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'days' => 'json',
    ];

    public $timestamps = false;

    public function restaurants()
	{
		return $this->hasMany(MyRegularRestaurant::class, 'my_regular_id', 'id');
	}

	public function customer()
	{
		return $this->belongsTo(Customer::class, 'customer_id', 'id');
	}

    public function deliveryAddress()
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id', 'id');
    }
}
