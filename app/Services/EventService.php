<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;

class EventService
{
    /**
     * Filter events based on search criteria
     */
    public function filterEvents(array $filters): Builder
    {
        return Event::query()
            ->when(isset($filters['fulltext']), function ($query) use ($filters) {
                return $query->whereFullText(['name', 'description'], $filters['fulltext']);
            })
            ->when(! empty($filters['with_tags']), function ($query) use ($filters) {
                return $query->whereHas('tags', function (Builder $subQuery) use ($filters) {
                    $subQuery->whereIn('id', $this->extractTagIds($filters['with_tags']));
                });
            })
            ->when(! empty($filters['without_tags']), function ($query) use ($filters) {
                return $query->whereDoesntHave('tags', function (Builder $subQuery) use ($filters) {
                    $subQuery->whereIn('id', $this->extractTagIds($filters['without_tags']));
                });
            })
            ->when(isset($filters['after']), function ($query) use ($filters) {
                return $query->where('date', '>', $filters['after']);
            })
            ->when(isset($filters['before']), function ($query) use ($filters) {
                return $query->where('date', '<', $filters['before']);
            });
    }

    /**
     * Create a new event with optional tags
     *
     * @param  array  $attributes  Event attributes including optional tags
     * @return Event The newly created event
     */
    public function createEvent(array $attributes): Event
    {
        // Create the event with basic attributes
        $event = Event::create($attributes);

        // Sync tags if present
        if (! empty($attributes['tags'])) {
            $this->syncEventTags($event, $attributes['tags']);
        }

        return $event;
    }

    /**
     * Update an existing event with optional tags
     *
     * @param  Event  $event  The event to update
     * @param  array  $attributes  Event attributes including optional tags
     * @return Event The updated event
     */
    public function updateEvent(Event $event, array $attributes): Event
    {
        // Update the event with basic attributes
        $event->update($attributes);

        // Sync tags if present
        if (! empty($attributes['tags'])) {
            $this->syncEventTags($event, $attributes['tags']);
        }

        return $event;
    }

    /**
     * Delete an event
     *
     * @param  Event  $event  The event to delete
     */
    public function deleteEvent(Event $event): void
    {
        $event->delete();
    }

    /**
     * Sync tags to an event
     *
     * @param  Event  $event  The event to sync tags to
     * @param  array  $tags  Array of tag objects with IDs
     */
    private function syncEventTags(Event $event, array $tags): void
    {
        $event->tags()->sync($this->extractTagIds($tags));
    }

    /**
     * Extract tag IDs from an array of tag objects
     */
    private function extractTagIds(array $tags): array
    {
        return array_map(static fn ($tag) => $tag['id'], $tags);
    }
}
