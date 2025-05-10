<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Spatie\Tags\Tag;

class TagService
{
    /**
     * Filter tags based on search criteria
     *
     * @param  array  $filters  Optional filtering parameters
     */
    public function filterTags(array $filters): Builder
    {
        return Tag::query()
            ->when(isset($filters['type']), function ($query) use ($filters) {
                return $query->where('type', $filters['type']);
            }, function ($query) { // Defaults to type = null
                return $query->whereNull('type');
            })
            ->when(isset($filters['name']), function ($query) use ($filters) {
                return $query->where('name->fr', 'like', '%'.$filters['name'].'%');
            })
            ->when(isset($filters['color']), function ($query) use ($filters) {
                return $query->where('color', $filters['color']);
            });
    }

    /**
     * Create a new tag
     *
     * @param  array  $attributes  Tag attributes
     * @return Tag The newly created tag
     */
    public function createTag(array $attributes): Tag
    {
        return Tag::create($attributes);
    }

    /**
     * Update an existing tag
     *
     * @param  Tag  $tag  The tag to update
     * @param  array  $attributes  Tag attributes
     * @return Tag The updated tag
     */
    public function updateTag(Tag $tag, array $attributes): Tag
    {
        // Update the tag with attributes
        $tag->update($attributes);

        return $tag->refresh();
    }

    /**
     * Delete a tag and return affected events with their updated tags
     *
     * @param  Tag  $tag  The tag to delete
     * @return Collection Events affected by tag deletion
     */
    public function deleteTag(Tag $tag): Collection
    {
        // Gather events which had this tag
        $affectedEvents = Event::withAnyTags($tag)->get();

        $tag->delete();

        // Load tags after removal to get a fresh list
        $affectedEvents->loadMissing('tags');

        return $affectedEvents;
    }
}
