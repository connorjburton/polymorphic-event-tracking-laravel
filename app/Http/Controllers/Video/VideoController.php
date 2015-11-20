<?php

namespace App\Http\Controllers\Video;

use App\Http\Controllers\Controller;
use Auth;

use Teepluss\Restable\Facades\Restable as Restable;

use App\Models\Event;
use App\Models\EventType;

class VideoController extends Controller
{
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
		
		$event = new Event;
		$event->user_id = Auth::user()->id;
		$event->type_id = EventType::where('name', Input::get('type'))->firstOrFail()->id;
		$event->eventable_id = $id;
		$event->eventable_type = 'Video';
		
		$data = Input::get('data');
		if($data) {
			$event->data = json_encode(Input::get('data'));
		}
		
		$video->events()->save($event);
	}
}