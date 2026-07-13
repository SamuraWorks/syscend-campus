<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Services\NotificationDispatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    /**
     * Student attendance — daily mark page.
     */
    public function index(Request $request): Response
    {
        $date      = $request->date ?? today()->toDateString();
        $classId   = $request->class_id;
        $sectionId = $request->section_id;

        $students = collect();
        $existing = collect();

        if ($classId) {
            $students = Student::with('section:id,name')
                ->where('class_id', $classId)
                ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
                ->where('status', 'active')
                ->orderBy('roll_no')
                ->get(['id', 'first_name', 'last_name', 'roll_no', 'section_id', 'gender']);

            $existing = Attendance::where([
                'school_id'       => $this->getSchoolId(),
                'date'            => $date,
                'attendable_type' => Student::class,
            ])->whereIn('attendable_id', $students->pluck('id'))
              ->get(['attendable_id', 'status', 'remarks'])
              ->keyBy('attendable_id');
        }

        return Inertia::render('SchoolAdmin/Attendance/Index', [
            'classes'  => SchoolClass::orderBy('numeric_name')->get(['id', 'name']),
            'sections' => Section::orderBy('name')->get(['id', 'class_id', 'name']),
            'students' => $students,
            'existing' => $existing,
            'filters'  => [
                'date'       => $date,
                'class_id'   => $classId,
                'section_id' => $sectionId,
            ],
        ]);
    }

    /**
     * Bulk upsert student attendance for a class+date.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date'              => 'required|date',
            'class_id'          => 'required|exists:classes,id',
            'records'           => 'required|array|min:1',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status'     => 'required|in:present,absent,late,half_day',
            'records.*.remarks'    => 'nullable|string|max:200',
        ]);

        $schoolId = $this->getSchoolId();

        try {
            $absentStudents = [];

            DB::transaction(function () use ($data, $schoolId, &$absentStudents) {
                foreach ($data['records'] as $record) {
                    $existing = Attendance::where([
                        'school_id'       => $schoolId,
                        'date'            => $data['date'],
                        'attendable_type' => Student::class,
                        'attendable_id'   => $record['student_id'],
                    ])->first();

                    if ($existing && $existing->approved_at) {
                        continue;
                    }

                    Attendance::updateOrCreate(
                        [
                            'school_id'       => $schoolId,
                            'date'            => $data['date'],
                            'attendable_type' => Student::class,
                            'attendable_id'   => $record['student_id'],
                        ],
                        [
                            'status'  => $record['status'],
                            'remarks' => $record['remarks'] ?? null,
                        ]
                    );

                    if (in_array($record['status'], ['absent', 'late'])) {
                        $student = Student::with('guardian.user')->find($record['student_id']);
                        if ($student && $student->guardian?->user_id) {
                            $absentStudents[] = $student;
                        }
                    }
                }
            });

            foreach ($absentStudents as $student) {
                NotificationDispatchService::notifyUser(
                    $student->guardian->user,
                    'Student Absent',
                    "{$student->full_name} was marked " . ($student->pivot->status ?? 'absent') . " on " . Carbon::parse($data['date'])->format('d M Y') . '.',
                    '/school/parent/attendance'
                );
            }

            return back()->with('success', 'Attendance saved for ' . Carbon::parse($data['date'])->format('d M Y') . '.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to save attendance: ' . $e->getMessage());
        }
    }

    /**
     * Monthly calendar view for a single student.
     */
    public function studentCalendar(Request $request, Student $student): Response
    {
        $month = $request->month ?? today()->format('Y-m');
        [$year, $mon] = explode('-', $month);

        $records = Attendance::where([
            'school_id'       => $this->getSchoolId(),
            'attendable_type' => Student::class,
            'attendable_id'   => $student->id,
        ])
        ->whereYear('date', $year)
        ->whereMonth('date', $mon)
        ->get(['date', 'status', 'remarks'])
        ->keyBy(fn ($r) => $r->date->toDateString());

        $student->load(['schoolClass:id,name', 'section:id,name']);

        return Inertia::render('SchoolAdmin/Attendance/StudentCalendar', [
            'student' => $student,
            'records' => $records,
            'month'   => $month,
        ]);
    }

    /**
     * Staff attendance — daily mark page.
     */
    public function staffIndex(Request $request): Response
    {
        $date         = $request->date ?? today()->toDateString();
        $departmentId = $request->department_id;

        $staff = Staff::with('department:id,name', 'designation:id,name')
            ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'emp_id', 'department_id', 'designation_id']);

        $existing = Attendance::where([
            'school_id'       => $this->getSchoolId(),
            'date'            => $date,
            'attendable_type' => Staff::class,
        ])->whereIn('attendable_id', $staff->pluck('id'))
          ->get(['attendable_id', 'status', 'remarks'])
          ->keyBy('attendable_id');

        return Inertia::render('SchoolAdmin/Attendance/StaffIndex', [
            'staffList'   => $staff,
            'existing'    => $existing,
            'departments' => \App\Models\Department::orderBy('name')->get(['id', 'name']),
            'filters'     => [
                'date'          => $date,
                'department_id' => $departmentId,
            ],
        ]);
    }

    /**
     * Bulk upsert staff attendance for a date.
     */
    public function staffStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date'                 => 'required|date',
            'records'              => 'required|array|min:1',
            'records.*.staff_id'   => 'required|exists:staff,id',
            'records.*.status'     => 'required|in:present,absent,late,half_day',
            'records.*.remarks'    => 'nullable|string|max:200',
        ]);

        $schoolId = $this->getSchoolId();

        try {
            DB::transaction(function () use ($data, $schoolId) {
                foreach ($data['records'] as $record) {
                    $existing = Attendance::where([
                        'school_id'       => $schoolId,
                        'date'            => $data['date'],
                        'attendable_type' => Staff::class,
                        'attendable_id'   => $record['staff_id'],
                    ])->first();

                    if ($existing && $existing->approved_at) {
                        continue;
                    }

                    Attendance::updateOrCreate(
                        [
                            'school_id'       => $schoolId,
                            'date'            => $data['date'],
                            'attendable_type' => Staff::class,
                            'attendable_id'   => $record['staff_id'],
                        ],
                        [
                            'status'  => $record['status'],
                            'remarks' => $record['remarks'] ?? null,
                        ]
                    );
                }
            });

            return back()->with('success', 'Staff attendance saved for ' . Carbon::parse($data['date'])->format('d M Y') . '.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to save staff attendance: ' . $e->getMessage());
        }
    }
}
