<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderCancellation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    // protected $with = ['canceller'];

    public function canceller()
    {
        return $this->morphTo();
    }

    public function order()
	{
		return $this->belongsTo(Order::class, 'order_id', 'id');
	}

    public function cancellationReason()
    {
        return $this->belongsTo(CancellationReason::class, 'cancellation_reason_id', 'id');
    }
}
