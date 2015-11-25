# Polymorphic Event Tracking Laravel (5.1+)
An example of how polymorphic relationships can be used for expressive & efficient event tracking.

Allows you to store events in command pattern style, making no assumption on how the data will be displayed. The parsing of the data should be done on the front end.

This example makes an assumption that you have users, if you do not have users and add support for anonymous users please let me know and I will merge to support that.

**Please note, this has not been tested in a production environment and should only be used as a reference.**

### Features

+ Event Queue
+ Event Trait
+ Unit Tests

### Storing Events

As long as you include the EventTrait:

```
use App\Http\Controllers\Event\EventTrait;

class example {
  use EventTrait;
}
```

The event can be stored from any file by running:

```
$video = Video::find($id);

$options = Input::only(['type', 'data', 'user_id']);
$options['eventable_type'] = 'Video';
$options['eventable_id'] = $id;

$this->storeEvent($options, function($event) use ($video) {
    $video->events()->save($event);
}, true);
```

*A note about user_id, there is no validation on this ID other than it exists in the users table. This is a secruity vulnerability if you just leave it like that. It means anyone can create an event for anyone. However, if you leave user_id blank it will use Auth::user()->id instead.

TL:DR - If you are passing user_id, make sure the user storing the event is allowed to store the event for that user*

### API Example

**Store Event**

```
request
  .post('/api/video/storeEvent')
  .send({
    user_id: 1,
    type: 'view'
  })
  .end(function(err, resp) {});
  
request
  .post('/api/image/storeEvent')
  .send({
    user_id: 1,
    type: 'view'
  })
  .end(function(err, resp) {});
```

**Retrive event (can be any combination of variables)**

```
request
  .get('/api/events')
  .query({
    user_id: 1,
    type: 'view'
  })
  .end(function(err, resp) {});
```

Will return

```
[{eventable_id: 23 ...], [eventable_id: 52 ...]}
```

###Config

**Add event types**

/database/seeds/EventTypesSeeder.php
```
class EventTypesSeeder extends Seeder
{
    // Seed for command types
    private $command_types = array(
        'view',
        'time_seen',
        'completed'
    );

```
