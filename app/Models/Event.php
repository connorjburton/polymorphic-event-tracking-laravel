<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
	use SoftDeletes;

	protected $hidden = ['deleted_at'];

    /**
     * Get all of the owning eventable models.
     */
    public function eventable()
    {
        return $this->morphTo();
    }
}