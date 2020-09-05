<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableBookingDetail extends Model
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

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'max_payment_time',
    ];
}
