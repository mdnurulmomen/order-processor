<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TakingOrder extends Model
{
    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    protected $casts = [
        'is_taken' => 'boolean',
    ];

    /**
     * Get the owning imageable model.
     */
    public function confirmer()
    {
        return $this->morphTo();
    }
}
