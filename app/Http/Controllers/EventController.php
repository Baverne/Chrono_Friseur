<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Models\Event;
use App\Services\EventService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    private EventService $eventService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchEventRequest $request): Collection
    {
        $filters = $request->validated();

        return $this->eventService->filterEvents($filters)
            ->with($this->getTagsRelation())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventRequest $request): Event
    {
        $attributes = $request->validated();

        return $this->eventService->createEvent($attributes)
            ->loadMissing($this->getTagsRelation());
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event): Event
    {
        return $event
            ->loadMissing($this->getTagsRelation());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventRequest $request, Event $event): Event
    {
        $attributes = $request->validated();

        return $this->eventService->updateEvent($event, $attributes)
            ->loadMissing($this->getTagsRelation());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): JsonResponse
    {
        $this->eventService->deleteEvent($event);

        return response()->json([
            'message' => "L'événement a bien été supprimé.",
        ]);
    }

    /**
     * Get the tags relation with the attributes to include in responses.
     */
    private function getTagsRelation(): string
    {
        // tags:id,name,color
        return 'tags:'.implode(',', [
            'id',
            'name',
            'color',
        ]);
    }
}
