<?php

namespace App\Models;

use App\Models\Rider;
use Illuminate\Database\Eloquent\Model;

class OrderDeliveryProgression extends Model
{
	public function rider()
	{
		return $this->belongsTo(Rider::class, 'rider_id', 'id');
	}
}
