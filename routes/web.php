<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| RESTful routes for Event and Tag resources.
|
*/

/**
 * Registers RESTful routes for the Event and Tag resources,
 * mapping them to the methods in their respective controller class.
 */
Route::apiResources([
    /*
 * Available routes for events and the respective function in the controller:
 *   GET /events - index (list all events)
 *   GET /events/{event} - show (view a single event)
 *   POST /events - store (create a new event)
 *   PUT/PATCH /events/{event} - update (update an existing event)
 *   DELETE /events/{event} - destroy (delete an event)
 */
    'events' => EventController::class,

    /*
 * Available routes for tags and the respective function in the controller:
 *   GET /tags - index (list all tags)
 *   GET /tags/{tag} - show (view a single tag)
 *   POST /tags - store (create a new tag)
 *   PUT/PATCH /tags/{tag} - update (update an existing tag)
 *   DELETE /tags/{tag} - destroy (delete a tag)
 */
    'tags' => TagController::class,
]);
