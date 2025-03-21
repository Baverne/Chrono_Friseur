<?php

use App\Http\Requests\StoreEventRequest;
use Illuminate\Support\Facades\Route;
use App\Models\Event;

Route::get('/', function () {
    return view('index');
});

Route::get('/joli', function () {
    return view('joli');
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
    $validated = $request->validated();
    return $validated;
});

Route::put('/events/{id}', function ($id) {
    $event = Event::find($id);
    $event->update(request()->all());
    return $event;
});

Route::delete('/events/{id}', function ($id) {
    $event = Event::find($id);
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
