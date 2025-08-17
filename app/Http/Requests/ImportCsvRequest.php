<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ImportCsvRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'csv_file' => [
                'required',
                'file',
                'max:10240', // 10MB in kilobytes
                'mimes:csv,txt'
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'csv_file.required' => 'Un fichier CSV est requis.',
            'csv_file.file' => 'Le fichier téléchargé n\'est pas valide.',
            'csv_file.mimes' => 'Le fichier doit être au format CSV.',
            'csv_file.max' => 'Le fichier ne peut pas dépasser 10 MB.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->hasFile('csv_file')) {
                $file = $this->file('csv_file');
                
                // Read and validate the header line
                $handle = fopen($file->getRealPath(), 'r');
                if (!$handle) {
                    $validator->errors()->add('csv_file', 'Impossible de lire le fichier CSV.');
                    return;
                }
                
                $firstLine = fgets($handle);
                if (!$firstLine) {
                    $validator->errors()->add('csv_file', 'Le fichier CSV est vide.');
                    fclose($handle);
                    return;
                }
                
                // Check column count first
                $columns = explode(';', trim($firstLine));
                if (count($columns) !== 4) {
                    $validator->errors()->add('csv_file', 'Le fichier CSV doit contenir exactement 4 colonnes séparées par des points-virgules. Trouvé: ' . count($columns) . ' colonnes.');
                    fclose($handle);
                    return;
                }
                
                // Skip header validation for now - just check column count
                // TODO: Add back header name validation later
                
                // Count total lines for row limit validation
                $lineCount = 1; // Already read the first line
                while (fgets($handle) !== false) {
                    $lineCount++;
                }
                fclose($handle);
                
                // Check row count (excluding header)
                $dataRows = $lineCount - 1;
                if ($dataRows > 1000) {
                    $validator->errors()->add('csv_file', "Le fichier CSV ne peut pas contenir plus de 1000 lignes de données. Trouvé: {$dataRows} lignes de données.");
                    return;
                }
                
                // Check minimum rows
                if ($dataRows < 1) {
                    $validator->errors()->add('csv_file', 'Le fichier CSV doit contenir au moins une ligne de données en plus de l\'en-tête.');
                    return;
                }
            }
        });
    }

    /**
     * Validate CSV file structure and content
     */
    private function validateCsvStructure($validator, $file)
    {
        try {
            $content = file_get_contents($file->getRealPath());
            if ($content === false) {
                $validator->errors()->add('csv_file', 'Impossible de lire le fichier CSV.');
                return;
            }
            
            $lines = explode("\n", $content);
            
            // Check if file has at least header + 1 data row
            if (count($lines) < 2) {
                $validator->errors()->add('csv_file', 'Le fichier CSV doit contenir au moins une ligne de données en plus de l\'en-tête.');
                return;
            }

            // Check row count (excluding header)
            $dataRowCount = count($lines) - 1;
            if ($dataRowCount > 1000) {
                $validator->errors()->add('csv_file', 'Le fichier CSV ne peut pas contenir plus de 1000 lignes de données.');
                return;
            }

            // Simple header check
            $headerLine = trim($lines[0]);
            $headerParts = explode(';', $headerLine);
            
            if (count($headerParts) !== 4) {
                $validator->errors()->add('csv_file', 'Le fichier CSV doit contenir exactement 4 colonnes.');
                return;
            }

            // Basic header validation (case insensitive, accent insensitive)
            $normalizedHeaders = array_map(function($header) {
                $header = strtolower(trim($header));
                return str_replace(['é', 'è', 'ê', 'ë'], 'e', $header);
            }, $headerParts);

            $requiredHeaders = ['nom', 'description', 'date', 'etiquettes'];
            $missing = array_diff($requiredHeaders, $normalizedHeaders);
            
            if (!empty($missing)) {
                $validator->errors()->add('csv_file', 'En-têtes manquantes ou incorrectes. Attendu: Nom;Description;Date;Étiquettes');
                return;
            }

        } catch (\Exception $e) {
            $validator->errors()->add('csv_file', 'Erreur lors de la lecture du fichier CSV.');
        }
    }

    /**
     * Validate date format
     */
    private function isValidDate($date): bool
    {
        $format = 'Y-m-d';
        $dateTime = \DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) === $date;
    }
}
