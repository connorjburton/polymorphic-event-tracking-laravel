<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
	use SoftDeletes;
    
	protected $hidden = ['deleted_at'];

    public function events() {
        return $this->morphMany('App\Models\Event', 'eventable');
    }
}