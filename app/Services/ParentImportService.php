<?php

namespace App\Services;

use App\Models\{Guardian, ImportJob, Student};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\{DB, Str};
use PhpOffice\PhpSpreadsheet\IOFactory;

class ParentImportService
{
    private const VALID_RELATIONS = ['mother', 'father', 'guardian', 'other'];

    private const ALLOWED_COLUMNS = [
        'parent_name',
        'relation',
        'student_id_no',
        'phone',
        'email',
        'occupation',
        'address',
        'student_first_name',
        'student_last_name',
    ];

    private int $schoolId;

    public function __construct(int $schoolId)
    {
        $this->schoolId = $schoolId;
    }

    public function parseFile(ImportJob $job): array
    {
        $filePath = Storage::disk('private')->path($job->file_path);

        if (!file_exists($filePath)) {
            throw new \RuntimeException("Import file not found: {$job->file_name}");
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (count($rows) < 2) {
            throw new \RuntimeException('Import file contains no data rows.');
        }

        $headers = array_map(fn($h) => Str::slug(trim($h), '_'), array_values($rows[1]));

        $dataRows = [];
        foreach ($rows as $rowIndex => $row) {
            if ($rowIndex <= 1) continue;

            $rowKeyed = array_combine($headers, array_values($row));
            $rowKeyed['__row_number'] = $rowIndex;
            $dataRows[] = $rowKeyed;
        }

        $job->update([
            'total_rows'  => count($dataRows),
            'file_name'   => $job->file_name,
        ]);

        return $dataRows;
    }

    public function validateRows(ImportJob $job): array
    {
        $rows = $this->parseFile($job);
        $valid = [];
        $errors = [];

        $existingStudentIds = Student::where('school_id', $this->schoolId)
            ->pluck('student_id', 'student_id')
            ->toArray();

        $existingGuardiansByName = Guardian::where('school_id', $this->schoolId)
            ->get()
            ->keyBy(fn($g) => strtolower(trim($g->name)));

        $existingLinks = Guardian::where('school_id', $this->schoolId)
            ->has('students')
            ->with('students:id,guardian_id,student_id')
            ->get()
            ->pluck('students')
            ->flatten()
            ->pluck('student_id', 'guardian_id')
            ->toArray();

        $seenLinks = [];

        foreach ($rows as $row) {
            $rowNum = $row['__row_number'];
            $rowErrors = [];

            $parentName = trim($row['parent_name'] ?? '');
            if ($parentName === '') {
                $rowErrors[] = 'parent_name is required.';
            }

            $relation = strtolower(trim($row['relation'] ?? ''));
            if ($relation === '' || !in_array($relation, self::VALID_RELATIONS, true)) {
                $rowErrors[] = 'relation must be one of: mother, father, guardian, other.';
            }

            $studentIdNo = trim($row['student_id_no'] ?? '');
            $studentFirstName = trim($row['student_first_name'] ?? '');
            $studentLastName = trim($row['student_last_name'] ?? '');
            $hasStudentIdentifier = $studentIdNo !== '' || ($studentFirstName !== '' && $studentLastName !== '');

            if ($studentIdNo !== '') {
                if (!isset($existingStudentIds[$studentIdNo])) {
                    $rowErrors[] = "Student ID '{$studentIdNo}' not found in this school.";
                }
            }

            if (!empty($rowErrors)) {
                $errors[$rowNum] = $rowErrors;
                continue;
            }

            $email = trim($row['email'] ?? '');
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[$rowNum] = ['email is not a valid email address.'];
                continue;
            }

            if ($studentIdNo !== '' && isset($existingStudentIds[$studentIdNo])) {
                $linkKey = strtolower($parentName) . '|' . $studentIdNo;
                if (in_array($linkKey, $seenLinks, true)) {
                    $errors[$rowNum] = ["Duplicate parent+student link skipped (parent: {$parentName}, student: {$studentIdNo})."];
                    continue;
                }
                $seenLinks[] = $linkKey;
            }

            $existingGuardian = $existingGuardiansByName[strtolower($parentName)] ?? null;

            $valid[] = array_merge(
                collect($row)->only(self::ALLOWED_COLUMNS)->toArray(),
                [
                    '__row_number'       => $rowNum,
                    '__relation'         => $relation,
                    '__has_student'      => $hasStudentIdentifier,
                    '__existing_guardian' => $existingGuardian,
                ]
            );
        }

        $job->update([
            'valid_rows'       => count($valid),
            'error_rows'       => count($errors),
            'validation_errors'=> $errors,
            'validated_at'     => now(),
            'status'           => 'validated',
        ]);

        return [
            'valid'  => $valid,
            'errors' => $errors,
            'total'  => $job->total_rows,
        ];
    }

    public function previewRows(ImportJob $job): array
    {
        $validation = $this->validateRows($job);

        return [
            'preview'       => array_slice($validation['valid'], 0, 50),
            'total_rows'    => $validation['total'],
            'valid_rows'    => count($validation['valid']),
            'error_rows'    => count($validation['errors']),
            'errors'        => $validation['errors'],
        ];
    }

    public function executeImport(ImportJob $job): array
    {
        $validation = $this->validateRows($job);

        $validRows = $validation['valid'];
        $batchSize = 50;
        $batches = array_chunk($validRows, $batchSize);

        $summary = [
            'parents_created'    => 0,
            'parents_reused'     => 0,
            'students_linked'    => 0,
            'students_not_found' => 0,
            'skipped_duplicates' => 0,
            'errors'             => [],
        ];

        $job->update(['status' => 'importing']);

        $guardianCache = [];

        foreach ($batches as $batch) {
            DB::transaction(function () use ($batch, &$summary, &$guardianCache) {
                foreach ($batch as $row) {
                    try {
                        $this->processRow($row, $summary, $guardianCache);
                    } catch (\Throwable $e) {
                        $summary['errors'][$row['__row_number']] = $e->getMessage();
                    }
                }
            });
        }

        $job->update([
            'status'          => 'completed',
            'imported_rows'   => $summary['parents_created'] + $summary['parents_reused'],
            'import_summary'  => $summary,
            'imported_at'     => now(),
        ]);

        return $summary;
    }

    private function processRow(array $row, array &$summary, array &$guardianCache): void
    {
        $parentName = trim($row['parent_name']);
        $relation = $row['__relation'];
        $phone = trim($row['phone'] ?? '') ?: null;
        $email = trim($row['email'] ?? '') ?: null;
        $occupation = trim($row['occupation'] ?? '') ?: null;
        $address = trim($row['address'] ?? '') ?: null;

        $guardianKey = strtolower($parentName);

        $existingGuardian = $row['__existing_guardian'] ?? null;

        if ($existingGuardian) {
            $guardian = $existingGuardian;
            $summary['parents_reused']++;
        } elseif (isset($guardianCache[$guardianKey])) {
            $guardian = $guardianCache[$guardianKey];
            $summary['parents_reused']++;
        } else {
            $guardian = Guardian::create([
                'school_id'  => $this->schoolId,
                'user_id'    => null,
                'name'       => $parentName,
                'relation'   => $relation,
                'phone'      => $phone,
                'email'      => $email,
                'occupation' => $occupation,
                'address'    => $address,
            ]);

            $guardianCache[$guardianKey] = $guardian;
            $summary['parents_created']++;
        }

        $studentIdNo = trim($row['student_id_no'] ?? '');
        $studentFirstName = trim($row['student_first_name'] ?? '');
        $studentLastName = trim($row['student_last_name'] ?? '');

        $student = null;

        if ($studentIdNo !== '') {
            $student = Student::where('school_id', $this->schoolId)
                ->where('student_id', $studentIdNo)
                ->first();
        }

        if (!$student && $studentFirstName !== '' && $studentLastName !== '') {
            $student = Student::where('school_id', $this->schoolId)
                ->whereRaw('LOWER(first_name) = ?', [strtolower($studentFirstName)])
                ->whereRaw('LOWER(last_name) = ?', [strtolower($studentLastName)])
                ->first();
        }

        if ($student) {
            $currentGuardianId = $student->guardian_id;
            if ($currentGuardianId && $currentGuardianId !== $guardian->id) {
                $summary['students_not_found']++;
            } else {
                $student->update(['guardian_id' => $guardian->id]);
                $summary['students_linked']++;
            }
        } else {
            $summary['students_not_found']++;
        }
    }
}
