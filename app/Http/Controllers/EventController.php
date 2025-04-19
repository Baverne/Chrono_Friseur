<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchEventRequest $request)
    {
        $filters = $request->validated();
        $eventsQuery = Event::query();

        if (isset($filters['fulltext'])) {
            $eventsQuery = $eventsQuery->whereFullText(
                ['name', 'description'],
                $filters['fulltext']
            );
        }
        if (! empty($filters['with_tags'])) {
            $eventsQuery = $eventsQuery
                ->whereHas('tags', function (Builder $query) use ($filters) {
                    $query->whereIn('id',
                        array_map(fn ($tag) => $tag['id'], $filters['with_tags'])
                    );
                });
        }
        if (! empty($filters['without_tags'])) {
            $eventsQuery = $eventsQuery
                ->whereDoesntHave('tags', function (Builder $query) use ($filters) {
                    $query->whereIn('id',
                        array_map(fn ($tag) => $tag['id'], $filters['without_tags'])
                    );
                });
        }
        if (isset($filters['after'])) {
            $eventsQuery = $eventsQuery
                ->where('date', '>', $filters['after']);
        }
        if (isset($filters['before'])) {
            $eventsQuery = $eventsQuery
                ->where('date', '<', $filters['before']);
        }

        return $eventsQuery->with('tags')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request)
    {
        $attributes = $request->validated();
        $event = Event::create($attributes);
        $event->tags()->sync(array_column($attributes['tags'], 'id'));
        $event->loadMissing('tags:id,name,color');

        return $event;
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return $event->loadMissing('tags:id,name,color');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event)
    {
        $attributes = $request->validated();
        $event->tags()->sync(array_column($attributes['tags'], 'id'));
        $event->update($attributes);
        $event->loadMissing('tags:id,name,color');

        return $event;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return response()->json(['message' => 'Événement supprimé avec succès.']);
    }
}
