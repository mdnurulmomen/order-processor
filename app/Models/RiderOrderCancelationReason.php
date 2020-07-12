<?php

namespace App\Models;

use App\Models\Rider;
use Illuminate\Database\Eloquent\Model;

class RiderOrderCancelationReason extends Model
{
	protected $guarded = [
		'id'
	];

	public $timestamps = false;

	public function rider() 
	{
		return $this->belongsTo(Rider::class, 'rider_id', 'id');
	}
}
