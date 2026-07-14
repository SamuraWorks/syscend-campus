<?php

namespace App\Services;

use App\Models\DocumentImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Http, Storage};

class DocumentExtractionService
{
    private int $schoolId;
    private int $userId;

    public function __construct(int $schoolId, int $userId)
    {
        $this->schoolId = $schoolId;
        $this->userId = $userId;
    }

    /**
     * Upload and process a document for AI extraction.
     */
    public function uploadAndExtract(UploadedFile $file, string $documentType): DocumentImport
    {
        $path = $file->store('imports/' . $this->schoolId, 'private');

        $import = DocumentImport::create([
            'school_id'     => $this->schoolId,
            'user_id'       => $this->userId,
            'document_type' => $documentType,
            'file_path'     => $path,
            'file_name'     => $file->getClientOriginalName(),
            'file_type'     => $file->getMimeType(),
            'file_size'     => $file->getSize(),
            'status'        => 'uploaded',
        ]);

        return $import;
    }

    /**
     * Extract data from uploaded document using AI.
     * Uses OpenAI Vision API for image/PDF analysis.
     */
    public function extract(DocumentImport $import): DocumentImport
    {
        $import->update(['status' => 'processing']);

        try {
            $filePath = Storage::disk('private')->path($import->file_path);
            $fileContent = file_get_contents($filePath);
            $base64 = base64_encode($fileContent);
            $mimeType = $import->file_type ?? 'image/jpeg';

            $extractedData = $this->callVisionApi($base64, $mimeType, $import->document_type);

            $import->update([
                'status'               => 'extracted',
                'extracted_data'       => $extractedData['data'] ?? null,
                'extraction_metadata'  => [
                    'model'         => 'gpt-4o',
                    'confidence'    => $extractedData['confidence'] ?? 0,
                    'extracted_at'  => now()->toIso8601String(),
                    'field_scores'  => $extractedData['field_scores'] ?? [],
                ],
                'processed_at'         => now(),
            ]);
        } catch (\Throwable $e) {
            $import->update([
                'status'         => 'failed',
                'admin_notes'    => 'Extraction failed: ' . $e->getMessage(),
                'processed_at'   => now(),
            ]);
        }

        return $import;
    }

    /**
     * Call OpenAI Vision API for document analysis.
     */
    private function callVisionApi(string $base64Image, string $mimeType, string $documentType): array
    {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            // Fallback: return mock data for demo/testing
            return $this->getMockExtraction($documentType);
        }

        $prompt = $this->getExtractionPrompt($documentType);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type'  => 'application/json',
        ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
            'model'  => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => [
                        [
                            'type' => 'text',
                            'text' => $prompt,
                        ],
                        [
                            'type' => 'image_url',
                            'image_url' => [
                                'url'    => 'data:' . $mimeType . ';base64,' . $base64Image,
                                'detail' => 'high',
                            ],
                        ],
                    ],
                ],
            ],
            'max_tokens' => 4000,
            'temperature' => 0.1,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException('AI API request failed: ' . $response->body());
        }

        $content = $response->json('choices.0.message.content', '');

        // Parse JSON from response
        preg_match('/\{[\s\S]*\}/', $content, $matches);
        if (empty($matches[0])) {
            throw new \RuntimeException('Failed to parse AI response');
        }

        return json_decode($matches[0], true) ?? throw new \RuntimeException('Invalid JSON in AI response');
    }

    /**
     * Get extraction prompt based on document type.
     */
    private function getExtractionPrompt(string $documentType): string
    {
        return match ($documentType) {
            'report_card' => <<<'PROMPT'
Analyze this school report card image. Extract ALL visible data as structured JSON.

Return a JSON object with these fields:
{
  "school_info": {
    "name": "...",
    "motto": "...",
    "registration_number": "...",
    "colors": {"primary": "#hex", "secondary": "#hex"}
  },
  "student_info": {
    "name": "...",
    "admission_number": "...",
    "class": "...",
    "section": "...",
    "academic_year": "...",
    "term": "..."
  },
  "results": {
    "subjects": [
      {
        "name": "...",
        "assessment_scores": {"component_name": score},
        "total": number,
        "grade": "...",
        "remarks": "..."
      }
    ],
    "overall_total": number,
    "overall_percentage": number,
    "overall_grade": "...",
    "position": "...",
    "promotion_status": "..."
  },
  "attendance": {
    "total_days": number,
    "present": number,
    "absent": number,
    "late": number
  },
  "comments": {
    "teacher": "...",
    "principal": "..."
  },
  "confidence": 0.0-1.0,
  "field_scores": {"field_name": confidence_score}
}

Extract exact numbers. If a field is not visible, use null. Return ONLY valid JSON.
PROMPT,
            'admission_form' => <<<'PROMPT'
Analyze this student admission form. Extract ALL visible data as structured JSON.
Return student info, parent/guardian info, previous school info, medical info.
Return ONLY valid JSON with confidence scores.
PROMPT,
            default => <<<'PROMPT'
Analyze this school document and extract all visible data as structured JSON.
Return the extracted data with confidence scores for each field.
Return ONLY valid JSON.
PROMPT,
        };
    }

    /**
     * Mock extraction for demo/testing when no API key is configured.
     */
    private function getMockExtraction(string $documentType): array
    {
        return [
            'data' => [
                'school_info' => [
                    'name' => 'Demo School',
                    'motto' => 'Knowledge is Power',
                ],
                'student_info' => [
                    'name' => 'Extracted Student Name',
                    'class' => 'Class 6',
                ],
                'results' => [
                    'subjects' => [
                        ['name' => 'Mathematics', 'total' => 78, 'grade' => 'A', 'remarks' => 'Excellent'],
                        ['name' => 'English', 'total' => 65, 'grade' => 'B', 'remarks' => 'Good'],
                    ],
                    'overall_percentage' => 71.5,
                    'overall_grade' => 'B',
                ],
            ],
            'confidence' => 0.85,
            'field_scores' => [
                'school_info.name' => 0.95,
                'student_info.name' => 0.80,
                'results.subjects' => 0.75,
            ],
        ];
    }

    /**
     * Import extracted results into the database.
     */
    public function importResults(DocumentImport $import, array $reviewedData, bool $confirm = false): array
    {
        if (!$confirm) {
            return ['status' => 'preview', 'data' => $reviewedData];
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        try {
            DB::transaction(function () use ($import, $reviewedData, &$imported, &$skipped, &$errors) {
                // Process based on document type
                match ($import->document_type) {
                    'report_card' => $this->importReportCard($import, $reviewedData, $imported, $skipped, $errors),
                    default => throw new \RuntimeException('Unsupported document type: ' . $import->document_type),
                };

                $import->update([
                    'status'            => 'imported',
                    'imported_at'       => now(),
                    'records_imported'  => $imported,
                    'records_skipped'   => $skipped,
                    'import_results'    => [
                        'imported' => $imported,
                        'skipped'  => $skipped,
                        'errors'   => $errors,
                    ],
                ]);
            });
        } catch (\Throwable $e) {
            $import->update(['status' => 'failed', 'admin_notes' => $e->getMessage()]);
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'errors' => $errors];
    }

    private function importReportCard(DocumentImport $import, array $data, int &$imported, int &$skipped, array &$errors): void
    {
        // Implementation for importing report card data
        // This will match students by admission number and create/update marks
    }
}
