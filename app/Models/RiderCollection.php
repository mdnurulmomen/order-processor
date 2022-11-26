<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiderCollection extends Model
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

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class, 'rider_id', 'id');
    }
}
