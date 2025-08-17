<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchEventRequest;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Http\Requests\ImportCsvRequest;
use App\Models\Event;
use App\Services\EventService;
use App\Services\CsvImportService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    private EventService $eventService;
    private CsvImportService $csvImportService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(EventService $eventService, CsvImportService $csvImportService)
    {
        $this->eventService = $eventService;
        $this->csvImportService = $csvImportService;
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

    /**
     * Validate CSV file before import
     */
    public function validateCsvImport(ImportCsvRequest $request): JsonResponse
    {
        // If we reach here, validation passed
        $file = $request->file('csv_file');
        
        // Get file info
        $fileInfo = [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'lines' => $this->countCsvLines($file),
        ];

        return response()->json([
            'message' => 'Le fichier CSV est valide et prêt à être importé.',
            'file_info' => $fileInfo,
        ]);
    }

    /**
     * Count lines in CSV file
     */
    private function countCsvLines($file): int
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return 0;
        }

        $lineCount = 0;
        while (fgets($handle) !== false) {
            $lineCount++;
        }
        fclose($handle);

        return $lineCount;
    }

    /**
     * Import events from CSV file
     */
    public function importCsv(ImportCsvRequest $request): JsonResponse
    {
        try {
            $file = $request->file('csv_file');
            $stats = $this->csvImportService->importEvents($file);

            // Prepare response message
            $message = "Import terminé avec succès !";
            if ($stats['events_created'] > 0) {
                $message .= " {$stats['events_created']} événement(s) créé(s).";
            }
            if ($stats['tags_created'] > 0) {
                $message .= " {$stats['tags_created']} étiquette(s) créée(s).";
            }
            if ($stats['events_skipped'] > 0) {
                $message .= " {$stats['events_skipped']} événement(s) ignoré(s) (doublons).";
            }

            $response = [
                'message' => $message,
                'stats' => $stats
            ];

            if (!empty($stats['errors'])) {
                $response['warnings'] = $stats['errors'];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'import : ' . $e->getMessage(),
            ], 422);
        }
    }
}
