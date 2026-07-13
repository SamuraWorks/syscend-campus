<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentDocument;
use App\Models\User;
use App\Services\NotificationDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $students = Student::with(['schoolClass:id,name', 'section:id,name', 'guardian:id,name,phone'])
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name',  'like', "%{$request->search}%")
                  ->orWhere('admission_no', 'like', "%{$request->search}%");
            }))
            ->when($request->class_id,  fn ($q) => $q->where('class_id',  $request->class_id))
            ->when($request->section_id, fn ($q) => $q->where('section_id', $request->section_id))
            ->when($request->status,    fn ($q) => $q->where('status',    $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('SchoolAdmin/Students/Index', [
            'students' => [
                'data'  => $students->items(),
                'meta'  => [
                    'total'        => $students->total(),
                    'per_page'     => $students->perPage(),
                    'current_page' => $students->currentPage(),
                    'last_page'    => $students->lastPage(),
                    'from'         => $students->firstItem(),
                    'to'           => $students->lastItem(),
                ],
                'links' => [
                    'prev' => $students->previousPageUrl(),
                    'next' => $students->nextPageUrl(),
                ],
            ],
            'filters'  => $request->only('search', 'class_id', 'section_id', 'status'),
            'classes'  => SchoolClass::orderBy('numeric_name')->get(['id', 'name']),
            'sections' => Section::orderBy('name')->get(['id', 'class_id', 'name']),
            'stats'    => [
                'total'       => Student::count(),
                'active'      => Student::where('status', 'active')->count(),
                'alumni'      => Student::where('status', 'alumni')->count(),
                'transferred' => Student::where('status', 'transferred')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('SchoolAdmin/Students/Create', [
            'classes'  => SchoolClass::orderBy('numeric_name')->get(['id', 'name']),
            'sections' => Section::orderBy('name')->get(['id', 'class_id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // Personal
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'gender'          => 'required|in:male,female,other',
            'date_of_birth'   => 'nullable|date',
            'blood_group'     => 'nullable|string|max:5',
            'religion'        => 'nullable|string|max:50',
            'nationality'     => 'nullable|string|max:50',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:150',
            'address'         => 'nullable|string|max:500',
            'category'        => 'required|in:general,disabled,quota',
            'status'          => 'required|in:active,alumni,transferred,inactive',
            'admission_date'  => 'nullable|date',
            'previous_school' => 'nullable|string|max:200',
            'roll_no'         => 'nullable|string|max:50',
            // Class
            'class_id'        => 'required|exists:classes,id',
            'section_id'      => 'nullable|exists:sections,id',
            // Guardian
            'guardian.name'       => 'required|string|max:150',
            'guardian.relation'   => 'required|string|max:50',
            'guardian.phone'      => 'nullable|string|max:20',
            'guardian.email'      => 'nullable|email|max:150',
            'guardian.occupation' => 'nullable|string|max:100',
            'guardian.address'    => 'nullable|string|max:500',
        ]);

        try {
            $schoolId = $this->getSchoolId();
            $guardianUser = null;

            DB::transaction(function () use ($data, $schoolId) {
                $guardian = Guardian::create(array_merge(
                    $data['guardian'],
                    ['school_id' => $schoolId],
                ));

                $student = Student::create(array_merge(
                    collect($data)->except('guardian')->toArray(),
                    ['guardian_id' => $guardian->id, 'school_id' => $schoolId],
                ));

                if (!empty($data['guardian']['email']) && !$guardian->user_id) {
                    $email = $data['guardian']['email'];
                    $user = User::create([
                        'school_id' => $schoolId,
                        'name'      => $data['guardian']['name'],
                        'email'     => $email,
                        'password'  => Hash::make('password'),
                        'status'    => 'active',
                    ]);
                    $user->assignRole('parent');
                    $guardian->update(['user_id' => $user->id]);
                }
            });

            $guardian = Guardian::latest()->where('school_id', $schoolId)->first();
            if ($guardian?->user_id) {
                $guardianUser = User::find($guardian->user_id);
                NotificationDispatchService::notifyUser(
                    $guardianUser,
                    'Student Admitted',
                    "Your child {$data['first_name']} " . ($data['last_name'] ?? '') . " has been admitted to the school.",
                    '/school/parent/students'
                );
            }

            NotificationDispatchService::notifyRole(
                $schoolId, 'school-admin',
                'New Student Admission',
                "A new student {$data['first_name']} " . ($data['last_name'] ?? '') . " has been admitted.",
                '/school/students'
            );

            return redirect()->route('school.students.index')->with('success', 'Student admitted successfully.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to admit student: ' . $e->getMessage());
        }
    }

    public function show(Student $student): Response
    {
        $student->load(['schoolClass', 'section', 'guardian', 'documents']);

        return Inertia::render('SchoolAdmin/Students/Show', [
            'student' => $student,
        ]);
    }

    public function edit(Student $student): Response
    {
        $student->load('guardian');

        return Inertia::render('SchoolAdmin/Students/Edit', [
            'student'  => $student,
            'classes'  => SchoolClass::orderBy('numeric_name')->get(['id', 'name']),
            'sections' => Section::orderBy('name')->get(['id', 'class_id', 'name']),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $data = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'gender'          => 'required|in:male,female,other',
            'date_of_birth'   => 'nullable|date',
            'blood_group'     => 'nullable|string|max:5',
            'religion'        => 'nullable|string|max:50',
            'nationality'     => 'nullable|string|max:50',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:150',
            'address'         => 'nullable|string|max:500',
            'category'        => 'required|in:general,disabled,quota',
            'status'          => 'required|in:active,alumni,transferred,inactive',
            'admission_date'  => 'nullable|date',
            'previous_school' => 'nullable|string|max:200',
            'roll_no'         => 'nullable|string|max:50',
            'class_id'        => 'required|exists:classes,id',
            'section_id'      => 'nullable|exists:sections,id',
            'guardian.name'       => 'required|string|max:150',
            'guardian.relation'   => 'required|string|max:50',
            'guardian.phone'      => 'nullable|string|max:20',
            'guardian.email'      => 'nullable|email|max:150',
            'guardian.occupation' => 'nullable|string|max:100',
            'guardian.address'    => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($data, $student) {
                $student->update(collect($data)->except('guardian')->toArray());

                if ($student->guardian) {
                    $student->guardian->update($data['guardian']);
                } else {
                    $guardian = Guardian::create(array_merge(
                        $data['guardian'],
                        ['school_id' => $student->school_id],
                    ));
                    $student->update(['guardian_id' => $guardian->id]);
                }
            });

            return redirect()->route('school.students.show', $student)->with('success', 'Student updated.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    public function destroy(Student $student): RedirectResponse
    {
        try {
            $student->delete();
            return redirect()->route('school.students.index')->with('success', 'Student removed.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    public function uploadDocument(Request $request, Student $student): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'file'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('file')->store("students/{$student->id}/documents", 'private');

        StudentDocument::create([
            'school_id'  => $student->school_id,
            'student_id' => $student->id,
            'title'      => $request->title,
            'file_path'  => $path,
            'file_type'  => $request->file('file')->getMimeType(),
            'file_size'  => $request->file('file')->getSize(),
        ]);

        return back()->with('success', 'Document uploaded.');
    }

    public function deleteDocument(StudentDocument $document): RedirectResponse
    {
        Storage::disk('private')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document deleted.');
    }

    public function bulkImport(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file'  => 'required|file|mimes:csv,txt|max:5120',
            'class_id'  => 'required|exists:classes,id',
            'section_id'=> 'nullable|exists:sections,id',
        ]);

        try {
            $schoolId = $this->getSchoolId();
            $file = $request->file('csv_file');
            $rows = array_map('str_getcsv', file($file->getRealPath()));
            $headers = array_map('strtolower', array_map('trim', array_shift($rows)));

            $class = SchoolClass::findOrFail($request->class_id);
            $imported = 0;
            $skipped = 0;
            $errors = [];

            DB::transaction(function () use ($rows, $headers, $schoolId, $class, $request, &$imported, &$skipped, &$errors) {
                foreach ($rows as $idx => $row) {
                    if (count($row) < 2 || empty($row[0])) {
                        $skipped++;
                        continue;
                    }

                    $data = array_combine($headers, $row);

                    $firstName = trim($data['first_name'] ?? '');
                    $lastName  = trim($data['last_name'] ?? '');

                    if (empty($firstName)) {
                        $errors[$idx + 2] = 'Missing first_name';
                        $skipped++;
                        continue;
                    }

                    $gender = strtolower(trim($data['gender'] ?? 'male'));
                    if (!in_array($gender, ['male', 'female', 'other'])) $gender = 'male';

                    // Find class by name or use default
                    $className = trim($data['class'] ?? $data['class_name'] ?? '');
                    $targetClass = $class;
                    if (!empty($className)) {
                        $found = SchoolClass::where('school_id', $schoolId)->where('name', 'like', "%{$className}%")->first();
                        if ($found) $targetClass = $found;
                    }

                    // Find section
                    $sectionId = $request->section_id;
                    $sectionName = trim($data['section'] ?? $data['section_name'] ?? '');
                    if (!empty($sectionName)) {
                        $found = Section::where('school_id', $schoolId)->where('name', 'like', "%{$sectionName}%")->first();
                        if ($found) $sectionId = $found->id;
                    }

                    // Create guardian first
                    $guardianName = trim($data['guardian_name'] ?? $data['parent_name'] ?? '');
                    $guardian = null;
                    if (!empty($guardianName)) {
                        $guardian = Guardian::create([
                            'school_id'  => $schoolId,
                            'name'       => $guardianName,
                            'relation'   => trim($data['guardian_relation'] ?? $data['relation'] ?? 'Parent'),
                            'phone'      => trim($data['guardian_phone'] ?? $data['parent_phone'] ?? ''),
                            'email'      => trim($data['guardian_email'] ?? $data['parent_email'] ?? ''),
                            'occupation' => trim($data['guardian_occupation'] ?? ''),
                            'address'    => trim($data['guardian_address'] ?? $data['address'] ?? ''),
                        ]);
                    }

                    // Generate admission number
                    $lastStudent = Student::where('school_id', $schoolId)->latest('id')->first();
                    $nextNum = $lastStudent ? intval(substr($lastStudent->admission_no, -4)) + 1 : 1;
                    $admissionNo = 'ADM-' . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

                    Student::create([
                        'school_id'       => $schoolId,
                        'first_name'      => $firstName,
                        'last_name'       => $lastName,
                        'gender'          => $gender,
                        'date_of_birth'   => !empty($data['date_of_birth'] ?? '') ? $data['date_of_birth'] : null,
                        'class_id'        => $targetClass->id,
                        'section_id'      => $sectionId,
                        'guardian_id'     => $guardian?->id,
                        'admission_no'    => $admissionNo,
                        'admission_date'  => !empty($data['admission_date'] ?? '') ? $data['admission_date'] : now()->toDateString(),
                        'phone'           => trim($data['phone'] ?? ''),
                        'email'           => trim($data['email'] ?? ''),
                        'address'         => trim($data['address'] ?? ''),
                        'roll_no'         => trim($data['roll_no'] ?? ''),
                        'category'        => 'general',
                        'status'          => 'active',
                        'blood_group'     => trim($data['blood_group'] ?? ''),
                        'religion'        => trim($data['religion'] ?? ''),
                        'nationality'     => trim($data['nationality'] ?? 'Sierra Leonean'),
                        'previous_school' => trim($data['previous_school'] ?? ''),
                    ]);

                    $imported++;
                }
            });

            NotificationDispatchService::notifyRole(
                $schoolId, 'school-admin',
                'Bulk Student Import',
                "{$imported} students imported, {$skipped} skipped.",
                '/school/students'
            );

            $message = "{$imported} students imported successfully.";
            if ($skipped > 0) $message .= " {$skipped} rows skipped.";
            if (!empty($errors)) $message .= " Errors: " . json_encode(array_slice($errors, 0, 5));

            return back()->with('success', $message);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to import students: ' . $e->getMessage());
        }
    }
}
