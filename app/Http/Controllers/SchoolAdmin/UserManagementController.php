<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Models\{Department, Designation, SchoolClass, Section, Staff, Student, User, UserAuditLog};
use App\Services\{PasswordGeneratorService, UserCreationService};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * User listing with search, filters, stats.
     */
    public function index(Request $request): Response
    {
        $schoolId = $this->getSchoolId();

        $users = User::with('roles')
            ->where('school_id', $schoolId)
            ->when($request->search, fn ($q) => $q->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            }))
            ->when($request->role, fn ($q) => $q->whereHas('roles', fn ($q) => $q->where('name', $request->role)))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('SchoolAdmin/Users/Index', [
            'users' => [
                'data'  => $users->items(),
                'meta'  => [
                    'total'        => $users->total(),
                    'current_page' => $users->currentPage(),
                    'last_page'    => $users->lastPage(),
                ],
                'links' => ['prev' => $users->previousPageUrl(), 'next' => $users->nextPageUrl()],
            ],
            'roles'  => Role::orderBy('name')->pluck('name'),
            'filters'=> $request->only('search', 'role', 'status'),
            'stats'  => [
                'total'     => User::where('school_id', $schoolId)->count(),
                'active'    => User::where('school_id', $schoolId)->where('status', 'active')->count(),
                'temp_pwd'  => User::where('school_id', $schoolId)->where('is_temporary_password', true)->count(),
                'inactive'  => User::where('school_id', $schoolId)->where('status', 'inactive')->count(),
            ],
        ]);
    }

    /**
     * Unified user creation form — supports all account types.
     */
    public function create(): Response
    {
        $schoolId = $this->getSchoolId();

        return Inertia::render('SchoolAdmin/Users/Create', [
            'roles'         => Role::orderBy('name')->pluck('name'),
            'classes'       => SchoolClass::orderBy('numeric_name')->get(['id', 'name']),
            'sections'      => Section::orderBy('name')->get(['id', 'class_id', 'name']),
            'departments'   => Department::orderBy('name')->get(['id', 'name']),
            'designations'  => Designation::orderBy('name')->get(['id', 'department_id', 'name']),
            'staffTypes'    => ['subject_teacher', 'form_master', 'both', 'non_teaching'],
        ]);
    }

    /**
     * Store a new user (unified — supports all account types).
     */
    public function store(Request $request): RedirectResponse
    {
        $schoolId = $this->getSchoolId();

        $data = $request->validate([
            'account_type'  => 'required|in:staff,student,parent',
            // Personal info
            'first_name'    => 'required|string|max:100',
            'last_name'     => 'nullable|string|max:100',
            'gender'        => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'blood_group'   => 'nullable|string|max:5',
            'religion'      => 'nullable|string|max:50',
            'nationality'   => 'nullable|string|max:50',
            'phone'         => 'nullable|string|max:20',
            'email'         => 'nullable|email|max:150',
            'address'       => 'nullable|string|max:500',
            // Account info
            'username'      => 'nullable|string|max:100|unique:users,username',
            'password_mode' => 'required|in:generated,manual',
            'custom_password' => 'nullable|string|min:6|max:128',
            // Roles
            'roles'         => 'required|array|min:1',
            'roles.*'       => 'string|exists:roles,name',
            // Staff-specific
            'department_id'  => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'joining_date'   => 'nullable|date',
            'salary_type'    => 'nullable|in:fixed,hourly',
            'salary'         => 'nullable|numeric|min:0',
            'teacher_type'   => 'nullable|in:subject_teacher,form_master,both,non_teaching',
            'form_master_class_id'   => 'nullable|exists:classes,id',
            'form_master_section_id' => 'nullable|exists:sections,id',
            // Student-specific
            'class_id'       => 'nullable|exists:classes,id',
            'section_id'     => 'nullable|exists:sections,id',
            'admission_date' => 'nullable|date',
            'roll_no'        => 'nullable|string|max:50',
            // Guardian (for students)
            'guardian_name'     => 'nullable|string|max:150',
            'guardian_relation' => 'nullable|string|max:50',
            'guardian_phone'    => 'nullable|string|max:20',
            'guardian_email'    => 'nullable|email|max:150',
            // Options
            'send_welcome_email' => 'nullable|boolean',
            'print_credentials'  => 'nullable|boolean',
        ]);

        try {
            $service = new UserCreationService($schoolId, auth()->id());

            $name = trim($data['first_name'] . ' ' . ($data['last_name'] ?? ''));
            $username = $data['username'] ?: UserCreationService::generateUsername($name);
            $password = $data['password_mode'] === 'manual' ? $data['custom_password'] : null;

            $result = match ($data['account_type']) {
                'staff' => $service->createStaff(
                    array_merge($data, ['username' => $username]),
                    $data['roles'],
                    $password
                ),
                'student' => $service->createStudent(
                    array_merge($data, [
                        'username' => $username,
                        'guardian' => [
                            'name'     => $data['guardian_name'] ?? '',
                            'relation' => $data['guardian_relation'] ?? 'parent',
                            'phone'    => $data['guardian_phone'] ?? null,
                            'email'    => $data['guardian_email'] ?? null,
                        ],
                        'create_student_account' => true,
                    ]),
                    $data['roles'],
                    $password
                ),
                'parent' => $service->createUser(
                    ['name' => $name, 'email' => $data['email'], 'username' => $username, 'phone' => $data['phone']],
                    $data['roles'],
                    $password
                ),
            };

            $user = $result['user'] ?? $result['student_user'] ?? null;
            $tempPassword = $result['temp_password'] ?? $password;

            // Send welcome email if requested
            if ($user && ($data['send_welcome_email'] ?? false) && $user->email) {
                $service->sendWelcomeEmail($user, $tempPassword);
            }

            return redirect()->route('school.users.show', $user?->id ?? 0)
                ->with('success', 'Account created successfully.')
                ->with('temp_password', $tempPassword)
                ->with('show_credentials', true);

        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Failed to create account: ' . $e->getMessage());
        }
    }

    /**
     * Show user detail — includes credentials display.
     */
    public function show(User $user): Response
    {
        abort_unless($user->school_id === $this->getSchoolId(), 403);

        $user->load(['roles', 'createdBy']);

        $staff = Staff::where('user_id', $user->id)->first();
        $student = Student::where('user_id', $user->id)->first();

        $auditLogs = UserAuditLog::where('user_id', $user->id)
            ->latest()
            ->take(30)
            ->get();

        return Inertia::render('SchoolAdmin/Users/Show', [
            'user'       => $user,
            'staff'      => $staff,
            'student'    => $student,
            'auditLogs'  => $auditLogs,
            'allRoles'   => Role::orderBy('name')->pluck('name'),
        ]);
    }

    /**
     * Update user roles.
     */
    public function updateRoles(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->school_id === $this->getSchoolId(), 403);

        $data = $request->validate([
            'roles'   => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $service = new UserCreationService($this->getSchoolId(), auth()->id());
        $service->updateRoles($user, $data['roles']);

        return back()->with('success', 'Roles updated.');
    }

    /**
     * Reset user password.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        abort_unless($user->school_id === $this->getSchoolId(), 403);

        $service = new UserCreationService($this->getSchoolId(), auth()->id());
        $newPassword = $service->resetPassword($user);

        return back()
            ->with('success', 'Password reset.')
            ->with('temp_password', $newPassword)
            ->with('show_credentials', true);
    }

    /**
     * Send welcome email.
     */
    public function sendWelcomeEmail(User $user): RedirectResponse
    {
        abort_unless($user->school_id === $this->getSchoolId(), 403);

        if (!$user->email) {
            return back()->with('error', 'No email address on file.');
        }

        $service = new UserCreationService($this->getSchoolId(), auth()->id());
        $tempPassword = $user->is_temporary_password ? 'Your temporary password' : 'Your current password';
        $sent = $service->sendWelcomeEmail($user, $tempPassword);

        return back()->with($sent ? 'success' : 'error',
            $sent ? 'Welcome email sent.' : 'Failed to send email.'
        );
    }

    /**
     * Toggle user status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        abort_unless($user->school_id === $this->getSchoolId(), 403);

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        UserAuditLog::log(
            $this->getSchoolId(),
            $user->id,
            auth()->id(),
            'status_changed',
            "Status changed to {$newStatus}"
        );

        return back()->with('success', "User {$newStatus}.");
    }

    /**
     * Generate a random password (API endpoint for the frontend).
     */
    public function generatePassword(): \Illuminate\Http\JsonResponse
    {
        $password = PasswordGeneratorService::generate(12, true);
        $strength = PasswordGeneratorService::strengthInfo($password);

        return response()->json(['password' => $password, 'strength' => $strength]);
    }

    /**
     * User audit logs.
     */
    public function auditLogs(User $user): Response
    {
        abort_unless($user->school_id === $this->getSchoolId(), 403);

        $logs = UserAuditLog::where('user_id', $user->id)
            ->with('performer:id,name')
            ->latest()
            ->paginate(30);

        return Inertia::render('SchoolAdmin/Users/AuditLogs', [
            'user'  => $user->only('id', 'name', 'email'),
            'logs'  => $logs,
        ]);
    }
}
