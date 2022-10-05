<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantProductAddon extends Model
{
    use SoftDeletes;

   	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

   	public $timestamps = false;

    public function addon()
    {
        return $this->belongsTo(Addon::class, 'addon_id', 'id')->withTrashed();
    }
}