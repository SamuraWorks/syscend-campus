<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubjectOffering;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumController extends Controller
{
    public function index(): Response
    {
        $schoolId = $this->getSchoolId();

        $offerings = SubjectOffering::query()
            ->where('school_id', $schoolId)
            ->with(['schoolClass', 'section', 'department'])
            ->withCount('enrollments')
            ->orderBy('class_id')
            ->orderBy('sort_order')
            ->get();

        $classes = \App\Models\SchoolClass::query()
            ->where('school_id', $schoolId)
            ->with('sections')
            ->orderBy('level_order')
            ->orderBy('name')
            ->get()
            ->map(function ($cls) use ($offerings) {
                $clsOfferings = $offerings->where('class_id', $cls->id);
                $cls->offerings = $clsOfferings->values();
                $cls->offerings_count = $clsOfferings->count();
                return $cls;
            });

        $academicYears = \App\Models\AcademicYear::query()
            ->where('school_id', $schoolId)
            ->orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        return Inertia::render('SchoolAdmin/Curriculum/Index', [
            'offerings'    => $offerings->groupBy('class_id'),
            'classes'      => $classes,
            'academicYears' => $academicYears,
        ]);
    }

    public function show(int $classId, Request $request): Response
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'section_id'       => 'nullable|exists:sections,id',
        ]);

        $schoolId = $this->getSchoolId();

        $query = SubjectOffering::query()
            ->where('school_id', $schoolId)
            ->where('class_id', $classId)
            ->where('academic_year_id', $request->input('academic_year_id'))
            ->with(['section', 'department', 'subject'])
            ->withCount('enrollments');

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }

        $subjects = $query
            ->orderBy('selection_group')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('selection_group');

        $schoolClass = \App\Models\SchoolClass::with('sections')->findOrFail($classId);

        return Inertia::render('SchoolAdmin/Curriculum/Show', [
            'subjects'  => $subjects,
            'schoolClass' => $schoolClass,
            'filters' => $request->only(['academic_year_id', 'section_id']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'class_id'         => 'required|exists:classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'subject_name'     => 'required|string|max:255',
            'subject_code'     => 'required|string|max:50',
            'subject_type'     => 'required|in:compulsory,elective,selective',
            'section_id'       => 'nullable|exists:sections,id',
            'selection_group'  => 'nullable|string|max:100',
            'is_required'      => 'boolean',
            'min_selection'    => 'nullable|integer|min:0',
            'max_selection'    => 'nullable|integer|min:0',
            'sort_order'       => 'nullable|integer|min:0',
        ]);

        SubjectOffering::create([
            ...$validated,
            'school_id'   => $this->getSchoolId(),
            'is_active'   => true,
        ]);

        return redirect()->back()->with('success', 'Subject offering created successfully.');
    }

    public function update(Request $request, SubjectOffering $offering): RedirectResponse
    {
        $validated = $request->validate([
            'subject_name'     => 'sometimes|string|max:255',
            'subject_code'     => 'sometimes|string|max:50',
            'subject_type'     => 'sometimes|in:compulsory,elective,selective',
            'section_id'       => 'nullable|exists:sections,id',
            'selection_group'  => 'nullable|string|max:100',
            'is_required'      => 'boolean',
            'min_selection'    => 'nullable|integer|min:0',
            'max_selection'    => 'nullable|integer|min:0',
            'sort_order'       => 'nullable|integer|min:0',
            'is_active'        => 'boolean',
        ]);

        $offering->update($validated);

        return redirect()->back()->with('success', 'Subject offering updated successfully.');
    }

    public function destroy(SubjectOffering $offering): RedirectResponse
    {
        $offering->delete();

        return redirect()->back()->with('success', 'Subject offering deleted successfully.');
    }
}
