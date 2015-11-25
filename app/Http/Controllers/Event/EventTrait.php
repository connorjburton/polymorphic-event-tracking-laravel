<?php

namespace App\Http\Controllers\Event;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Models\Event;
use App\Models\EventType;

use App\Jobs\StoreEvent;

use SuperClosure\Serializer;
use Teepluss\Restable\Facades\Restable as Restable;
use Auth;

trait EventTrait {
  use DispatchesJobs;

	public function storeEvent($id = null, $options = null, $callback = null, $queue = null) {
      if($queue) {
          $this->queueEvent($id, $options, $callback);
          return false;
      }
    
      $validator = Validator::make($options, [
          'type' => 'required|string|exists:event_types,name'
      ]);
      
      if ($validator->fails()) {
          return Restable::unprocess($validator->errors())->render();
      }
      
      $event = new Event;
      $event->user_id = Auth::user()->id;
      $event->type_id = EventType::where('name', $options['type'])->firstOrFail()->id;
      $event->eventable_id = $id;
      $event->eventable_type = $options['eventable_type'];
    
      $data = $options['data'];
      if($data) $event->data = json_encode($options['data']);
      
      if(is_string($callback)) {
          $serializer = new Serializer();
          $callback = $serializer->unserialize($callback);
      }
      
      $callback($event);
	}
  
  public function queueEvent($id, $options, $callback) {
      $serializer = new Serializer();
      
      $job = (new StoreEvent($id, $options, $serializer->serialize($callback)))->onQueue('events');
    
      $this->dispatch($job);
  }
}
