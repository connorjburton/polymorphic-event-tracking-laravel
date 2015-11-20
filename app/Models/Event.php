<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
	use SoftDeletes;

	protected $hidden = ['deleted_at'];

	public function getEventableTypeAttribute($value) {
		//Avoid exposing paths to outside world
		return str_replace('App\Models\\', '', $value);
	}

    /**
     * Get all of the owning eventable models.
     */
    public function eventable()
    {
        return $this->morphTo();
    }

    public function type() {
    	return $this->hasOne('App\Models\EventType', 'id', 'type_id');
    }
}