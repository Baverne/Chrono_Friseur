<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Services\TagService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Spatie\Tags\Tag;

class TagController extends Controller
{
    private TagService $tagService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Display a listing of tags.
     *
     * @return Collection
     */
    public function index()
    {
        return $this->tagService->filterTags([])
            ->orderBy('id')->get();
    }

    /**
     * Store a newly created tag in storage.
     */
    public function store(StoreTagRequest $request): Tag
    {
        $attributes = $request->validated();

        return $this->tagService->createTag($attributes);
    }

    /**
     * Display the specified tag.
     */
    public function show(Tag $tag): Tag
    {
        return $tag;
    }

    /**
     * Update the specified tag in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): Tag
    {
        $attributes = $request->validated();

        return $this->tagService->updateTag($tag, $attributes);
    }

    /**
     * Remove the specified tag from storage.
     */
    public function destroy(Tag $tag): JsonResponse
    {
        $affectedEvents = $this->tagService->deleteTag($tag);

        return response()->json([
            'message' => 'Le tag a bien été supprimé.',
            'affected_events' => $affectedEvents,
        ]);
    }
}
