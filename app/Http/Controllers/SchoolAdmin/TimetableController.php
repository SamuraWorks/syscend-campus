<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\{AcademicYear, SchedulePeriod, SchoolClass, Section, Staff, Subject, Timetable};
use Illuminate\Http\{JsonResponse, RedirectResponse, Request};
use Inertia\Inertia;
use Inertia\Response;

class TimetableController extends Controller
{
    public function index(Request $request): Response
    {
        $schoolId = $this->getSchoolId();
        $classId   = $request->class_id;
        $sectionId = $request->section_id;

        // Get configured periods from DB (or fallback to hardcoded)
        $currentYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first();
        $periods = SchedulePeriod::where('school_id', $schoolId)
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->active()
            ->ordered()
            ->get();

        // If no periods configured, generate defaults
        if ($periods->isEmpty()) {
            $periods = collect([
                (object)['id' => 0, 'name' => 'Period 1', 'start_time' => '07:30', 'end_time' => '08:15', 'duration_minutes' => 45, 'is_break' => false, 'event_type' => null],
                (object)['id' => 0, 'name' => 'Period 2', 'start_time' => '08:15', 'end_time' => '09:00', 'duration_minutes' => 45, 'is_break' => false, 'event_type' => null],
                (object)['id' => 0, 'name' => 'Period 3', 'start_time' => '09:00', 'end_time' => '09:45', 'duration_minutes' => 45, 'is_break' => false, 'event_type' => null],
                (object)['id' => 0, 'name' => 'Break',     'start_time' => '09:45', 'end_time' => '10:00', 'duration_minutes' => 15, 'is_break' => true,  'event_type' => null],
                (object)['id' => 0, 'name' => 'Period 4', 'start_time' => '10:00', 'end_time' => '10:45', 'duration_minutes' => 45, 'is_break' => false, 'event_type' => null],
                (object)['id' => 0, 'name' => 'Period 5', 'start_time' => '10:45', 'end_time' => '11:30', 'duration_minutes' => 45, 'is_break' => false, 'event_type' => null],
                (object)['id' => 0, 'name' => 'Period 6', 'start_time' => '11:30', 'end_time' => '12:15', 'duration_minutes' => 45, 'is_break' => false, 'event_type' => null],
                (object)['id' => 0, 'name' => 'Lunch',     'start_time' => '12:15', 'end_time' => '13:00', 'duration_minutes' => 45, 'is_break' => true,  'event_type' => null],
            ]);
        }

        $timetableEntries = collect();
        if ($classId) {
            $timetableEntries = Timetable::with(['subject:id,name,code', 'teacher:id,first_name,last_name'])
                ->where('class_id', $classId)
                ->when($sectionId, fn ($q) => $q->where('section_id', $sectionId))
                ->get();
        }

        // Index by day+start_time for easy grid lookup
        $grid = [];
        foreach ($timetableEntries as $p) {
            $grid[$p->day_of_week][$p->start_time] = $p;
        }

        return Inertia::render('SchoolAdmin/Timetable/Index', [
            'classes'      => SchoolClass::orderBy('numeric_name')->get(['id', 'name']),
            'sections'     => Section::orderBy('name')->get(['id', 'class_id', 'name']),
            'subjects'     => $classId ? Subject::where('class_id', $classId)->orderBy('name')->get(['id', 'name', 'code']) : collect(),
            'teachers'     => Staff::where('status', 'active')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
            'periods'      => $timetableEntries,
            'schedulePeriods' => $periods,
            'grid'         => $grid,
            'days'         => ['monday','tuesday','wednesday','thursday','friday','saturday'],
            'filters'      => ['class_id' => $classId, 'section_id' => $sectionId],
            'hasConfiguredPeriods' => $periods->isNotEmpty() && $periods->first()->id > 0,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'class_id'    => 'required|exists:classes,id',
            'section_id'  => 'nullable|exists:sections,id',
            'subject_id'  => 'required|exists:subjects,id',
            'teacher_id'  => 'nullable|exists:staff,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'room'        => 'nullable|string|max:50',
            'notes'       => 'nullable|string|max:200',
        ]);

        // Teacher conflict check
        if (!empty($data['teacher_id'])) {
            $conflict = Timetable::where('school_id', $this->getSchoolId())
                ->where('teacher_id', $data['teacher_id'])
                ->where('day_of_week', $data['day_of_week'])
                ->where('start_time', $data['start_time'])
                ->exists();

            if ($conflict) {
                return back()->withErrors(['teacher_id' => 'This teacher already has a class at this time.']);
            }
        }

        Timetable::updateOrCreate(
            [
                'school_id'   => $this->getSchoolId(),
                'class_id'    => $data['class_id'],
                'section_id'  => $data['section_id'] ?? null,
                'day_of_week' => $data['day_of_week'],
                'start_time'  => $data['start_time'],
            ],
            array_merge($data, ['school_id' => $this->getSchoolId()])
        );

        return back()->with('success', 'Period saved.');
    }

    public function destroy(Timetable $timetable): RedirectResponse
    {
        $timetable->delete();
        return back()->with('success', 'Period removed.');
    }

    /**
     * Teacher's personal weekly schedule.
     */
    public function teacherSchedule(Request $request): Response
    {
        $schoolId = $this->getSchoolId();
        $teacherId = $request->teacher_id;

        $currentYear = AcademicYear::where('school_id', $schoolId)->where('is_current', true)->first();
        $schedulePeriods = SchedulePeriod::where('school_id', $schoolId)
            ->when($currentYear, fn ($q) => $q->where('academic_year_id', $currentYear->id))
            ->active()
            ->ordered()
            ->get();

        $periods = collect();
        if ($teacherId) {
            $periods = Timetable::with(['schoolClass:id,name', 'section:id,name', 'subject:id,name'])
                ->where('teacher_id', $teacherId)
                ->get();
        }

        $grid = [];
        foreach ($periods as $p) {
            $grid[$p->day_of_week][$p->start_time] = $p;
        }

        return Inertia::render('SchoolAdmin/Timetable/TeacherSchedule', [
            'teachers'        => Staff::where('status', 'active')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'emp_id']),
            'periods'         => $periods,
            'schedulePeriods' => $schedulePeriods,
            'grid'            => $grid,
            'days'            => ['monday','tuesday','wednesday','thursday','friday','saturday'],
            'filters'         => ['teacher_id' => $teacherId],
        ]);
    }
}
