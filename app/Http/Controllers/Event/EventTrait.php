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

	public function storeEvent($options = null, $callback = null, $queue = null) {
      if($queue) {
          $this->queueEvent($options, $callback);
          return false;
      }
    
      $validator = Validator::make($options, [
          'type' => 'required|string|exists:event_types,name',
          'user_id' => 'optional|numeric|exists:users,id',
          'eventable_id' => 'required|numeric',
          'eventable_type' => 'required|string'
      ]);
      
      if ($validator->fails()) {
          return Restable::unprocess($validator->errors())->render();
      }
      
      $event = new Event;
      $event->user_id = (isset($options['user_id'])) ? $options['user_id'] : Auth::user()->id;
      $event->type_id = EventType::where('name', $options['type'])->firstOrFail()->id;
      $event->eventable_id = $options['eventable_id'];
      $event->eventable_type = $options['eventable_type'];
    
      $data = $options['data'];
      if($data) $event->data = json_encode($options['data']);
      
      if(is_string($callback)) {
          $serializer = new Serializer();
          $callback = $serializer->unserialize($callback);
      }
      
      $callback($event);
	}
  
  public function queueEvent($options, $callback) {
      $serializer = new Serializer();
      
      $job = (new StoreEvent($options, $serializer->serialize($callback)))->onQueue('events');
    
      $this->dispatch($job);
  }
}
