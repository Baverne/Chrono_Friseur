<?php

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\Route;
use App\Models\Event;

Route::get('/', function () {
    return view('index');
});

Route::get('/events', function () {
    $events = Event::all();
    return $events;
});

Route::get('/events/{id}', function ($id) {
    $event = Event::find($id);
    return $event;
});

// Route::get('/events/{id}', function (Event $event) {
//     return $event;
// });

Route::post('/events',    function (StoreEventRequest $request) {
    $attributes = $request->validated();
    $event = Event::create($attributes);
    return $event;
});

Route::put('/events/{id}', function (UpdateEventRequest $request, $id) {
    $event = Event::findOrFail($id);
    $attributes = $request->validated();
    $event->update($attributes);
    return $event;
});

Route::delete('/events/{id}', function ($id) {
    $event = Event::findOrFail($id);
    $event->delete();
    return response()->json(['message' => 'Event deleted successfully']);
});

Route::get('/eventForm', function () {
    $id = request('id');
    $name = request('name');
    $description = request('description');
    $date = request('date');
    return view('components.eventForm', compact('id', 'name', 'description','date'));
});
