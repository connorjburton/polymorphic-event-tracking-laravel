<?php

namespace App\Http\Controllers\Event;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Teepluss\Restable\Facades\Restable as Restable;

use App\Models\Event;

class EventController extends Controller
{
    public function __construct() {
        Validator::extend('model', function($attribute, $value, $parameters, $validator) {
            $modelPath = "App\Models\\" . $value;
            return class_exists($modelPath);
        });
    }

    public function index() {
        $validator = Validator::make(Input::all(), [
            'user_id' => 'numeric|exists:users,id',
            'eventable_id' => 'numeric',
            'eventable_type' => 'string|model',
            'type' => 'string|exists:event_types,name'
        ]);
        
        if ($validator->fails()) {
            return Restable::unprocess($validator->errors())->render();
        }

        $validData = Input::only(['user_id', 'eventable_id', 'eventable_type', 'type']);

        $filteredEvents = Event::with('type')->get()->filter(function($event) use ($validData) {
            $passes = true;
            $eventArray = $event->toArray();

            $eventArray['type'] = $eventArray['type']['name'];

            foreach($validData as $key => $data) {
                if($data != $eventArray[$key]) {
                    $passes = false;
                }
            }

            return $passes;
        })->flatten();

        return Restable::listing($filteredEvents)->render();
    }
}