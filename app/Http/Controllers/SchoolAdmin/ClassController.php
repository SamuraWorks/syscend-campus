<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Staff;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ClassController extends Controller
{
    private function getEnabledLevels(int $schoolId): array
    {
        $settings = SchoolSetting::allFor($schoolId);
        $levels = [];
        if (($settings['enable_ece'] ?? '1') === '1')     $levels[] = 'early_childhood';
        if (($settings['enable_primary'] ?? '1') === '1')  $levels[] = 'primary';
        if (($settings['enable_jss'] ?? '1') === '1')      $levels[] = 'junior_secondary';
        if (($settings['enable_sss'] ?? '1') === '1')      $levels[] = 'senior_secondary';
        return $levels;
    }

    public function index(Request $request): Response
    {
        $schoolId = auth()->user()->school_id;

        $query = SchoolClass::withCount(['sections', 'subjects', 'students'])
            ->with(['sections' => function ($q) {
                $q->withCount('students')->orderBy('name');
            }]);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('short_name', 'ilike', "%{$search}%");
            });
        }

        if ($level = $request->input('level')) {
            $query->where('school_level', $level);
        }

        if ($request->input('status') === 'active') {
            $query->where('is_active', true);
        } elseif ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        }

        $classes = $query->orderBy('school_level')
            ->orderBy('level_order')
            ->orderBy('numeric_name')
            ->orderBy('name')
            ->paginate($request->input('per_page', 25))
            ->withQueryString();

        $staff = Staff::where('status', 'active')
            ->select('id', 'first_name', 'last_name')
            ->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->full_name]);

        return Inertia::render('SchoolAdmin/Classes/Index', [
            'classes'       => $classes,
            'staff'         => $staff,
            'enabledLevels' => $this->getEnabledLevels($schoolId),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $schoolId = auth()->user()->school_id;
        $enabledLevels = $this->getEnabledLevels($schoolId);

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'short_name'      => 'nullable|string|max:20',
            'numeric_name'    => 'nullable|integer|min:1',
            'capacity'        => 'nullable|integer|min:0',
            'school_level'    => ['nullable', 'string', Rule::in($enabledLevels ?: ['early_childhood','primary','junior_secondary','senior_secondary'])],
            'level_order'     => 'nullable|integer|min:0',
            'department_id'   => 'nullable|exists:departments,id',
            'class_teacher_id'=> 'nullable|exists:staff,id',
            'description'     => 'nullable|string|max:500',
            'is_active'       => 'boolean',
        ]);

        $exists = SchoolClass::where('school_id', $schoolId)
            ->where('name', $data['name'])
            ->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'A class with this name already exists.'])->withInput();
        }

        $data['school_id'] = $schoolId;
        SchoolClass::create($data);

        return back()->with('success', 'Class created.');
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        $schoolId = auth()->user()->school_id;
        $enabledLevels = $this->getEnabledLevels($schoolId);

        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'short_name'      => 'nullable|string|max:20',
            'numeric_name'    => 'nullable|integer|min:1',
            'capacity'        => 'nullable|integer|min:0',
            'school_level'    => ['nullable', 'string', Rule::in($enabledLevels ?: ['early_childhood','primary','junior_secondary','senior_secondary'])],
            'level_order'     => 'nullable|integer|min:0',
            'department_id'   => 'nullable|exists:departments,id',
            'class_teacher_id'=> 'nullable|exists:staff,id',
            'description'     => 'nullable|string|max:500',
            'is_active'       => 'boolean',
        ]);

        $exists = SchoolClass::where('school_id', $schoolId)
            ->where('name', $data['name'])
            ->where('id', '!=', $class->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'A class with this name already exists.'])->withInput();
        }

        $class->update($data);

        return back()->with('success', 'Class updated.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        $class->delete();

        return back()->with('success', 'Class deleted.');
    }
}
