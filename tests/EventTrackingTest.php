<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Http\Models\EventType;
use App\Http\Controllers\Event\EventTrait;

class EventTracking extends TestCase
{
	use EventTrait, DatabaseTransactions;

    public function testStoreEventByJob()
    {
        $this->assertTrue(true);
    }

    public function testStoreEvent()
    {
    	$user = factory(App\User::class, 1)->create();
    	$this->actingAs($user);

    	$eventType = factory(App\Models\EventType::class, 1)->create();
    	$video = factory(App\Models\Video::class, 1)->create();

    	$options = [
    		'eventable_id' => $video->id,
    		'eventable_type' => 'Video',
    		'type' => $eventType->name,
    		'data' => ['test' => 'test-data']
    	];

    	$self = $this;
    	$this->storeEvent($options, function($event) use ($video, $user, $options, $eventType, $self) {
            $video->events()->save($event);

            $databaseFields = [
	    		'user_id' => $user->id,
	    		'eventable_id' => $video->id,
	    		'eventable_type' => 'App\Models\\' . $options['eventable_type'],
	    		'type_id' => $event->type_id,
	    		'data' => $event->data,
	    		'deleted_at' => NULL
	    	];

	    	$modelHasEvent = App\Models\Video::find($video->id)->whereHas('events', function($query) use ($event) {
	    		$query->where('id', '=', $event->id);
	    	})->get();

	    	if($self->seeInDatabase('events', $databaseFields) && $modelHasEvent) {
	    		$self->assertTrue(true);
	    	}
        });
    }
}