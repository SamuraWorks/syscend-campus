<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use App\Services\UserCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ParentController extends Controller
{
    public function index(Request $request): Response
    {
        $guardians = Guardian::withCount('students')
            ->with('user:id,name,email')
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            }))
            ->when($request->status, fn ($q) => $q->where('registration_status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'       => Guardian::count(),
            'registered'  => Guardian::whereNotNull('user_id')->count(),
            'pending'     => Guardian::whereNull('user_id')->count(),
            'linked'      => Guardian::has('students')->count(),
        ];

        return Inertia::render('SchoolAdmin/Parents/Index', [
            'parents' => $guardians,
            'filters' => $request->only(['search', 'status']),
            'stats'   => $stats,
        ]);
    }

    public function create(): Response
    {
        $students = Student::with('schoolClass:id,name')
            ->whereNull('guardian_id')
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_no', 'school_class_id']);

        return Inertia::render('SchoolAdmin/Parents/Create', [
            'unlinkedStudents' => $students,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'relation'      => 'required|string|in:mother,father,guardian,other',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'occupation'    => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:500',
            'student_ids'   => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'create_account' => 'sometimes|boolean',
        ]);

        try {
            $schoolId = $this->getSchoolId();
            $tempPassword = null;
            $msg = null;

            DB::transaction(function () use ($data, $schoolId, &$tempPassword) {
                $guardian = Guardian::create([
                    'school_id' => $schoolId,
                    'name'      => $data['name'],
                    'relation'  => $data['relation'],
                    'phone'     => $data['phone'] ?? null,
                    'email'     => $data['email'] ?? null,
                    'occupation'=> $data['occupation'] ?? null,
                    'address'   => $data['address'] ?? null,
                ]);

                if (!empty($data['student_ids'])) {
                    Student::whereIn('id', $data['student_ids'])
                        ->update(['guardian_id' => $guardian->id]);
                }

                if (!empty($data['email']) && !empty($data['create_account'])) {
                    $existingUser = User::where('school_id', $schoolId)->where('email', $data['email'])->first();

                    if ($existingUser) {
                        if (!$existingUser->hasRole('parent')) {
                            $existingUser->assignRole('parent');
                        }
                        $guardian->update(['user_id' => $existingUser->id]);
                        $msg = "Parent linked to existing user account ({$existingUser->getRoleNames()->implode(', ')}).";
                    } else {
                        $service = new UserCreationService($schoolId, auth()->id());
                        $result = $service->createUser([
                            'name'  => $data['name'],
                            'email' => $data['email'],
                            'phone' => $data['phone'] ?? null,
                        ], ['parent']);
                        $guardian->update(['user_id' => $result['user']->id]);
                        $tempPassword = $result['temp_password'];
                    }
                }
            });

            $msg = $msg ?? 'Parent created successfully.';
            if ($tempPassword) {
                $msg .= " Login credentials have been created.";
            }

            return redirect()->route('school-admin.parents.index')
                ->with('success', $msg)
                ->with('temp_password', $tempPassword)
                ->with('show_credentials', (bool) $tempPassword);
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to create parent: ' . $e->getMessage());
        }
    }

    public function show(Guardian $parent): Response
    {
        $parent->load([
            'students' => fn ($q) => $q->with('schoolClass:id,name', 'section:id,name'),
            'user:id,name,email,phone,status',
        ]);
        $parent->loadCount('students');

        $roles = [];
        if ($parent->user) {
            $roles = $parent->user->getRoleNames()->toArray();
        }

        return Inertia::render('SchoolAdmin/Parents/Show', [
            'parent' => $parent,
            'roles'  => $roles,
        ]);
    }

    public function edit(Guardian $parent): Response
    {
        $parent->load('students:id,first_name,last_name,admission_no,school_class_id,guardian_id');

        $linkedStudentIds = $parent->students->pluck('id')->toArray();

        $unlinkedStudents = Student::with('schoolClass:id,name')
            ->where(function ($q) use ($linkedStudentIds) {
                $q->whereNull('guardian_id')->orWhereIn('id', $linkedStudentIds);
            })
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'admission_no', 'school_class_id']);

        return Inertia::render('SchoolAdmin/Parents/Edit', [
            'parent'           => $parent,
            'linkedStudentIds' => $linkedStudentIds,
            'unlinkedStudents' => $unlinkedStudents,
        ]);
    }

    public function update(Request $request, Guardian $parent): RedirectResponse
    {
        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'relation'      => 'required|string|in:mother,father,guardian,other',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'occupation'    => 'nullable|string|max:100',
            'address'       => 'nullable|string|max:500',
            'student_ids'   => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'create_account' => 'sometimes|boolean',
        ]);

        try {
            DB::transaction(function () use ($data, $parent) {
                $parent->update([
                    'name'       => $data['name'],
                    'relation'   => $data['relation'],
                    'phone'      => $data['phone'] ?? null,
                    'email'      => $data['email'] ?? null,
                    'occupation' => $data['occupation'] ?? null,
                    'address'    => $data['address'] ?? null,
                ]);

                if ($parent->user_id) {
                    $parent->user->update([
                        'name'  => $data['name'],
                        'email' => $data['email'] ?? null,
                        'phone' => $data['phone'] ?? null,
                    ]);
                }

                $newStudentIds = $data['student_ids'] ?? [];
                $currentStudentIds = $parent->students()->pluck('students.id')->toArray();

                $toDetach = array_diff($currentStudentIds, $newStudentIds);
                $toAttach = array_diff($newStudentIds, $currentStudentIds);

                if (!empty($toDetach)) {
                    Student::whereIn('id', $toDetach)->update(['guardian_id' => null]);
                }
                if (!empty($toAttach)) {
                    Student::whereIn('id', $toAttach)->update(['guardian_id' => $parent->id]);
                }

                if (empty($parent->user_id) && !empty($data['email']) && !empty($data['create_account'])) {
                    $existingUser = User::where('school_id', $parent->school_id)->where('email', $data['email'])->first();

                    if ($existingUser) {
                        if (!$existingUser->hasRole('parent')) {
                            $existingUser->assignRole('parent');
                        }
                        $parent->update(['user_id' => $existingUser->id]);
                    } else {
                        $service = new UserCreationService($parent->school_id, auth()->id());
                        $result = $service->createUser([
                            'name'  => $data['name'],
                            'email' => $data['email'],
                            'phone' => $data['phone'] ?? null,
                        ], ['parent']);
                        $parent->update(['user_id' => $result['user']->id]);
                    }
                }
            });

            return redirect()->route('school-admin.parents.show', $parent)->with('success', 'Parent updated.');
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to update parent: ' . $e->getMessage());
        }
    }

    public function destroy(Guardian $parent): RedirectResponse
    {
        try {
            Student::where('guardian_id', $parent->id)->update(['guardian_id' => null]);
            $parent->delete();

            return redirect()->route('school-admin.parents.index')->with('success', 'Parent removed.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete parent: ' . $e->getMessage());
        }
    }
}
