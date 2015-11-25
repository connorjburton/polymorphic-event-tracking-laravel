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
            'orderby' => 'string|in:created_at,user_id,eventable_id',
            'limit' => 'numeric|min:1|max:100',
            'direction' => 'string|in:DESC,ASC',
            'user_id' => 'numeric|exists:users,id',
            'eventable_id' => 'numeric',
            'eventable_type' => 'string|model',
            'type' => 'string|exists:event_types,name'
        ]);
        
        if ($validator->fails()) {
            return Restable::unprocess($validator->errors())->render();
        }
        
        $type = Input::get('type');
        $orderby = Input::get('orderby', 'created_at');
        $direction = Input::get('direction', 'DESC');
        $limit = Input::get('limit');
        
        $validData = Input::only(['user_id', 'eventable_id', 'eventable_type', 'data']);
        $validData['eventable_type'] = 'App\Models\\' . $validData['eventable_type'];

        $events = Event::with('type');

        if($type) {
            $events->whereHas('type', function($q) use ($type) {
                $q->where('name', '=', $type);
            });
        }

        foreach($validData as $dataKey => $dataValue) {
            if($dataValue !== NULL) $events->where($dataKey, $dataValue);
        };

        if($limit) {
            $events->take($limit);
        }

        $events = $events->get();

        return Restable::listing($events)->render();
    }
}
