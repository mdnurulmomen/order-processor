<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderDeliveryReturn extends Model
{
    protected $guarded = [
		'id'
	];

	// public $timestamps = false;

	/**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
    ];

    public $timestamps = false;

	public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

	public function rider()
	{
		return $this->belongsTo(Rider::class, 'rider_id', 'id');
	}
}
