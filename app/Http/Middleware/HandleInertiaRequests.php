<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Services\RoleRegistry;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'id'         => $request->user()->id,
                    'name'       => $request->user()->name,
                    'email'      => $request->user()->email,
                    'phone'      => $request->user()->phone ?? null,
                    'avatar'     => $request->user()->avatar ?? null,
                    'avatar_url' => $request->user()->avatar_url,
                    'role'       => $this->resolvePrimaryRole($request->user()),
                    'roles'      => $request->user()->getRoleNames()->toArray(),
                    'activeRole' => $this->resolveActiveRole($request->user()),
                    'permissions'=> $request->user()->getAllPermissions()->pluck('name')->toArray(),
                    'school_id'  => $request->user()->school_id ?? null,
                    'status'     => $request->user()->status ?? null,
                ] : null,
            ],
            'flash' => [
                'success'         => fn () => session('success'),
                'error'           => fn () => session('error'),
                'temp_password'   => fn () => session('temp_password'),
                'show_credentials'=> fn () => session('show_credentials'),
            ],
            'verified'           => fn () => session('verified'),
            'already_registered' => fn () => session('already_registered'),
            'faviconUrl' => fn () => once(function () {
                $path = PlatformSetting::get('platform_favicon');
                return $path ? asset('storage/' . $path) : null;
            }),
            'schoolBranding' => fn () => once(function () use ($request) {
                // Authenticated user — resolve from their school
                $user = $request->user();
                if ($user && $user->school_id) {
                    $school = School::find($user->school_id);
                    return $school ? $school->branding : null;
                }
                // Guest user on a school-slug route — resolve from URL
                $slug = $request->route('schoolSlug');
                if ($slug) {
                    $school = School::where('slug', $slug)->where('status', 'active')->first();
                    return $school ? $school->branding : null;
                }
                return null;
            }),
            'schoolConfig' => fn () => once(function () use ($request) {
                $user = $request->user();
                if (!$user || !$user->school_id) return null;
                $schoolId = $user->school_id;
                $settings = SchoolSetting::allFor($schoolId);
                return [
                    'primary_color'     => School::find($schoolId)?->primary_color,
                    'secondary_color'   => School::find($schoolId)?->secondary_color,
                    'currency'          => $settings['currency'] ?? School::find($schoolId)?->currency,
                    'currency_symbol'   => School::find($schoolId)?->currency_symbol,
                    'language'          => $settings['language'] ?? 'en',
                    'terms_per_year'    => (int) ($settings['terms_per_year'] ?? 3),
                    'ca_weight'         => (float) ($settings['ca_weight'] ?? 40),
                    'exam_weight'       => (float) ($settings['exam_weight'] ?? 60),
                    'grading_system'    => $settings['grading_system'] ?? 'wassce',
                    'pass_mark'         => (int) ($settings['pass_mark'] ?? 50),
                    'enable_ece'        => ($settings['enable_ece'] ?? '1') === '1',
                    'enable_primary'    => ($settings['enable_primary'] ?? '1') === '1',
                    'enable_jss'        => ($settings['enable_jss'] ?? '1') === '1',
                    'enable_sss'        => ($settings['enable_sss'] ?? '1') === '1',
                    'section_format'    => $settings['section_format'] ?? 'letter',
                    'school_level'      => School::find($schoolId)?->school_level,
                    'school_type'       => School::find($schoolId)?->school_type,
                    // Result display settings
                    'result_show_position'            => $settings['result_show_position'] ?? 'overall',
                    'result_position_type'            => $settings['result_position_type'] ?? 'rank',
                    'result_show_teacher_comment'     => ($settings['result_show_teacher_comment'] ?? '0') === '1',
                    'result_show_principal_comment'   => ($settings['result_show_principal_comment'] ?? '0') === '1',
                    'result_show_form_master_comment' => ($settings['result_show_form_master_comment'] ?? '0') === '1',
                    'result_show_conduct'            => ($settings['result_show_conduct'] ?? '0') === '1',
                    'result_show_behaviour'          => ($settings['result_show_behaviour'] ?? '0') === '1',
                ];
            }),
        ];
    }

    /**
     * Determine the active role — either the session-stored one or fallback to primary.
     */
    private function resolveActiveRole($user): ?string
    {
        $active = session('active_role');
        $roles = $user->getRoleNames();

        if ($active && $roles->contains($active)) {
            return $active;
        }

        return $this->resolvePrimaryRole($user);
    }

    /**
     * Determine the single authoritative primary role for the user.
     *
     * Priority order matches the dashboard routing logic:
     *  super-admin > ministry-admin > district-officer > school-admin > ...
     *
     * This is the role used by the sidebar to determine which nav items to show.
     */
    private function resolvePrimaryRole($user): ?string
    {
        $roles = $user->getRoleNames();

        if ($roles->isEmpty()) {
            return null;
        }

        // Priority-ordered list of all roles
        $priority = [
            RoleRegistry::SUPER_ADMIN,
            RoleRegistry::MINISTRY_ADMIN,
            RoleRegistry::DISTRICT_OFFICER,
            RoleRegistry::SCHOOL_ADMIN,
            RoleRegistry::PRINCIPAL,
            RoleRegistry::TEACHER,
            RoleRegistry::ACCOUNTANT,
            RoleRegistry::LIBRARIAN,
            RoleRegistry::RECEPTIONIST,
            RoleRegistry::DRIVER,
            RoleRegistry::WARDEN,
            RoleRegistry::STORE_MANAGER,
            RoleRegistry::PROPRIETOR,
            RoleRegistry::STUDENT,
            RoleRegistry::PARENT,
        ];

        foreach ($priority as $role) {
            if ($roles->contains($role)) {
                return $role;
            }
        }

        // Fallback: first role alphabetically
        return $roles->sort()->first();
    }
}
