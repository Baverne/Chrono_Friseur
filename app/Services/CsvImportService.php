<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;
use Carbon\Carbon;

class CsvImportService
{
    /**
     * Maximum number of rows allowed in CSV
     */
    private const MAX_ROWS = 1000;

    /**
     * Import events from CSV file
     */
    public function importEvents(UploadedFile $file): array
    {
        // Parse CSV
        $csvData = $this->parseCsv($file);

        // Validate CSV structure
        $this->validateCsvStructure($csvData);

        // Import data in a transaction
        return DB::transaction(function () use ($csvData) {
            return $this->processImport($csvData);
        });
    }

    /**
     * Parse CSV file content
     */
    private function parseCsv(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            throw new \Exception('Impossible de lire le fichier CSV.');
        }

        $data = [];
        $lineNumber = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $lineNumber++;
            
            // Skip empty lines
            if (empty(array_filter($row))) {
                continue;
            }

            $data[] = $row;

            // Safety check for max rows
            if ($lineNumber > self::MAX_ROWS) {
                fclose($handle);
                throw new \Exception('Le fichier CSV contient trop de lignes (maximum: ' . self::MAX_ROWS . ').');
            }
        }

        fclose($handle);

        if (empty($data)) {
            throw new \Exception('Le fichier CSV est vide ou ne contient pas de données valides.');
        }

        return $data;
    }

    /**
     * Validate CSV structure and required columns
     */
    private function validateCsvStructure(array $csvData): void
    {
        if (empty($csvData)) {
            throw new \Exception('Le fichier CSV est vide ou ne contient pas de données.');
        }

        $firstRow = $csvData[0];
        if (count($firstRow) !== 4) {
            throw new \Exception('Le fichier CSV doit contenir exactement 4 colonnes.');
        }
    }

    /**
     * Process the import of CSV data
     */
    private function processImport(array $csvData): array
    {
        $stats = [
            'events_created' => 0,
            'events_skipped' => 0,
            'tags_created' => 0,
            'errors' => []
        ];

        // Skip header row
        $dataRows = array_slice($csvData, 1);

        foreach ($dataRows as $index => $row) {
            $lineNumber = $index + 2; // +2 because we start from line 1 and skip header

            try {
                $result = $this->processRow($row, $lineNumber);
                $stats['events_created'] += $result['event_created'] ? 1 : 0;
                $stats['events_skipped'] += $result['event_skipped'] ? 1 : 0;
                $stats['tags_created'] += $result['tags_created'];
            } catch (\Exception $e) {
                $stats['errors'][] = "Ligne {$lineNumber}: " . $e->getMessage();
            }
        }

        return $stats;
    }

    /**
     * Process a single CSV row
     */
    private function processRow(array $row, int $lineNumber): array
    {
        // Clean and validate data
        $name = trim($row[0]);
        $description = trim($row[1]);
        $dateString = trim($row[2]);
        $tagsString = trim($row[3]);

        if (empty($name)) {
            throw new \Exception('Le nom de l\'événement est requis.');
        }

        // Parse and validate date
        try {
            $date = Carbon::createFromFormat('Y-m-d', $dateString);
            if (!$date) {
                throw new \Exception('Format de date invalide.');
            }
        } catch (\Exception $e) {
            throw new \Exception('Format de date invalide. Utilisez le format YYYY-MM-DD.');
        }

        // Check for duplicate event (same name and date)
        $existingEvent = Event::where('name', $name)
            ->where(DB::raw('DATE(date)'), $date->format('Y-m-d'))
            ->first();

        if ($existingEvent) {
            return [
                'event_created' => false,
                'event_skipped' => true,
                'tags_created' => 0
            ];
        }

        // Process tags
        $tagIds = [];
        $tagsCreated = 0;

        if (!empty($tagsString)) {
            $tagNames = array_map('trim', explode(',', $tagsString));
            $tagNames = array_filter($tagNames); // Remove empty strings

            foreach ($tagNames as $tagName) {
                if (!empty($tagName)) {
                    $tag = $this->getOrCreateTag($tagName);
                    if ($tag->wasRecentlyCreated) {
                        $tagsCreated++;
                    }
                    $tagIds[] = $tag->id;
                }
            }
        }

        // Create event
        $event = Event::create([
            'name' => $name,
            'description' => $description ?: null,
            'date' => $date,
        ]);

        // Attach tags
        if (!empty($tagIds)) {
            $event->tags()->sync($tagIds);
        }

        return [
            'event_created' => true,
            'event_skipped' => false,
            'tags_created' => $tagsCreated
        ];
    }

    /**
     * Get existing tag or create new one with random color
     */
    private function getOrCreateTag(string $name): Tag
    {
        // Try to find existing tag (case-insensitive)
        $existingTag = Tag::query()
            ->whereRaw('LOWER(name->"$.fr") = LOWER(?)', [$name])
            ->first();

        if ($existingTag) {
            return $existingTag;
        }

        // Create new tag with random color
        $randomColor = $this->generateRandomColor();

        return Tag::create([
            'name' => ['fr' => $name],
            'color' => $randomColor
        ]);
    }

    /**
     * Generate a random color for tags
     */
    private function generateRandomColor(): string
    {
        // Generate a nice, vibrant color
        $colors = [
            '#EF4444', // red-500
            '#F97316', // orange-500
            '#F59E0B', // amber-500
            '#EAB308', // yellow-500
            '#84CC16', // lime-500
            '#22C55E', // green-500
            '#10B981', // emerald-500
            '#14B8A6', // teal-500
            '#06B6D4', // cyan-500
            '#0EA5E9', // sky-500
            '#3B82F6', // blue-500
            '#6366F1', // indigo-500
            '#8B5CF6', // violet-500
            '#A855F7', // purple-500
            '#D946EF', // fuchsia-500
            '#EC4899', // pink-500
            '#F43F5E', // rose-500
        ];

        return $colors[array_rand($colors)];
    }
}