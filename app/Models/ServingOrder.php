<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServingOrder extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id'
    ];

    public function confirmer()
    {
        return $this->morphTo();
    }
}
