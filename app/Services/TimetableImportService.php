<?php

namespace App\Services;

use App\Models\{AcademicYear, SchoolClass, Section, Staff, Subject, Timetable};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\{Collection, DB, Str};

class TimetableImportService
{
    private const ALLOWED_COLUMNS = [
        'academic_year', 'day', 'start_time', 'end_time',
        'class_name', 'section_name', 'subject_name', 'teacher_name',
        'room', 'lesson_type', 'status',
    ];

    private const VALID_DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    private int $schoolId;

    public function __construct(int $schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function parseFile($job): void
    {
        $filePath = Storage::disk('private')->path($job->file_path);
        $rows = $this->readCsv($filePath);

        $header = array_map(fn ($h) => Str::slug(Str::lower(trim($h)), '_'), $rows[0] ?? []);
        $dataRows = array_slice($rows, 1);

        $validRows = [];
        $errorRows = [];

        foreach ($dataRows as $idx => $row) {
            $record = array_combine($header, $row);
            $errors = $this->validateRow($record, $idx + 2);

            if (empty($errors)) {
                $validRows[] = $record;
            } else {
                $errorRows[] = ['row' => $idx + 2, 'errors' => $errors, 'data' => $record];
            }
        }

        $job->update([
            'total_rows'       => count($dataRows),
            'valid_rows'       => count($validRows),
            'error_rows'       => count($errorRows),
            'validation_errors' => $errorRows,
            'status'           => empty($errorRows) ? 'validated' : 'validation_failed',
            'validated_at'     => now(),
        ]);

        $job->setRelation('parsedData', collect($validRows));
    }

    public function previewRows($job): array
    {
        $errors = $job->validation_errors ?? [];
        return [
            'valid'   => $job->valid_rows,
            'invalid' => $job->error_rows,
            'total'   => $job->total_rows,
            'errors'  => array_slice($errors, 0, 50),
        ];
    }

    public function executeImport($job): array
    {
        $filePath = Storage::disk('private')->path($job->file_path);
        $rows = $this->readCsv($filePath);
        $header = array_map(fn ($h) => Str::slug(Str::lower(trim($h)), '_'), $rows[0] ?? []);
        $dataRows = array_slice($rows, 1);

        $imported = 0;
        $skipped = 0;

        DB::transaction(function () use ($dataRows, $header, &$imported, &$skipped) {
            foreach ($dataRows as $row) {
                $record = array_combine($header, $row);
                if ($this->importRow($record)) {
                    $imported++;
                } else {
                    $skipped++;
                }
            }
        });

        $job->update([
            'imported_rows' => $imported,
            'status'        => 'completed',
            'imported_at'   => now(),
            'import_summary' => ['imported' => $imported, 'skipped' => $skipped],
        ]);

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    private function importRow(array $record): bool
    {
        $class = SchoolClass::where('school_id', $this->schoolId)
            ->whereRaw('LOWER(name) = ?', [Str::lower($record['class_name'] ?? '')])
            ->first();
        if (! $class) return false;

        $subject = Subject::where('school_id', $this->schoolId)
            ->whereRaw('LOWER(name) = ?', [Str::lower($record['subject_name'] ?? '')])
            ->where('class_id', $class->id)
            ->first();
        if (! $subject) return false;

        $section = null;
        if (! empty($record['section_name'])) {
            $section = Section::where('school_id', $this->schoolId)
                ->where('class_id', $class->id)
                ->whereRaw('LOWER(name) = ?', [Str::lower($record['section_name'])])
                ->first();
        }

        $teacher = null;
        if (! empty($record['teacher_name'])) {
            $nameParts = explode(' ', trim($record['teacher_name']), 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            $teacher = Staff::where('school_id', $this->schoolId)
                ->whereRaw('LOWER(first_name) = ?', [Str::lower($firstName)])
                ->whereRaw('LOWER(last_name) = ?', [Str::lower($lastName)])
                ->first();
        }

        $day = Str::lower(trim($record['day'] ?? ''));
        if (! in_array($day, self::VALID_DAYS)) return false;

        $academicYear = null;
        if (! empty($record['academic_year'])) {
            $academicYear = AcademicYear::where('school_id', $this->schoolId)
                ->whereRaw('LOWER(name) = ?', [Str::lower($record['academic_year'])])
                ->first();
        }

        $startTime = $record['start_time'] ?? null;
        $endTime = $record['end_time'] ?? null;
        if (! $startTime || ! $endTime) return false;

        // Teacher conflict check during import
        if ($teacher) {
            $hasConflict = Timetable::where('school_id', $this->schoolId)
                ->where('teacher_id', $teacher->id)
                ->where('day_of_week', $day)
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->exists();

            if ($hasConflict) return false;
        }

        Timetable::updateOrCreate(
            [
                'school_id'  => $this->schoolId,
                'class_id'   => $class->id,
                'section_id' => $section?->id,
                'day_of_week' => $day,
                'start_time' => $startTime,
            ],
            [
                'subject_id' => $subject->id,
                'teacher_id' => $teacher?->id,
                'end_time'   => $endTime,
                'room'       => $record['room'] ?? null,
                'notes'      => $record['lesson_type'] ?? null,
                'status'     => 'draft',
            ]
        );

        return true;
    }

    private function validateRow(array $record, int $rowNum): array
    {
        $errors = [];

        if (empty($record['class_name'])) $errors[] = 'Class name is required';
        if (empty($record['subject_name'])) $errors[] = 'Subject name is required';
        if (empty($record['day'])) $errors[] = 'Day is required';
        if (empty($record['start_time'])) $errors[] = 'Start time is required';
        if (empty($record['end_time'])) $errors[] = 'End time is required';

        $day = Str::lower(trim($record['day'] ?? ''));
        if ($day && ! in_array($day, self::VALID_DAYS)) {
            $errors[] = "Invalid day: {$record['day']}. Use: monday-sunday";
        }

        if (! empty($record['start_time']) && ! empty($record['end_time'])) {
            if ($record['start_time'] >= $record['end_time']) {
                $errors[] = 'End time must be after start time';
            }
        }

        return $errors;
    }

    private function readCsv(string $path): array
    {
        $rows = [];
        if (($handle = fopen($path, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                $rows[] = $row;
            }
            fclose($handle);
        }
        return $rows;
    }
}
