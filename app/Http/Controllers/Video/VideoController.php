<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use Auth;

use Teepluss\Restable\Facades\Restable as Restable;

use App\Http\Controllers\Event\EventTrait;

class VideoController extends Controller
{
	use EventTrait;
	
	public function storeEvent($id)
	{
		$validator = Validator::make(Input::all(), [
			'type' => 'required|string|exists:event_types,name'
		]);
		
		if ($validator->fails()) {
			return Restable::unprocess($validator->errors())->render();
		}
		
		if (!$video = Video::where('id', $id)) {
			return Restable::missing('Assignment not found or cannot be viewed by student')->render();
		}
		
		$options = Input::only(['user_id', 'eventable_id', 'type', 'data']);
	        $options['eventable_type'] = 'Phase';
		
	        $this->storeEvent($video->pluck('id'), $options, function($event) use ($video) {
	            $video->events()->save($event);
	        }, true);
	}
}
