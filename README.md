# Polymorphic Event Tracking Laravel (5.1+)
An example of how polymorphic relationships can be used for expressive & efficient event tracking.

Allows you to store events in command pattern style, making no assumption on how the data will be displayed. The parsing of the data should be done on the front end.

This example makes an assumption that you have users, if you do not have users and add support for anonymous users please let me know and I will merge to support that.

**Please note, this has not been tested in a production environment and should only be used as a reference.**

###Todo

+ Create a demo frontend to showcase functionality
+ Add the ability to filter by dates
+ Handle type checking intuitively

###Usage

**Store Event**

```
request
  .post('/api/video/storeEvent')
  .send({
    user_id: 1,
    eventable_id: 23,
    eventable_type: 'Video',
    type: 'view'
  })
  .end(function(err, resp) {});
  
request
  .post('/api/image/storeEvent')
  .send({
    user_id: 1,
    eventable_id: 52,
    eventable_type: 'Image',
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
