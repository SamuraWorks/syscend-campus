<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use App\Models\School;
use App\Models\SchoolSetting;
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
                'user' => $request->user() ? [
                    'id'         => $request->user()->id,
                    'name'       => $request->user()->name,
                    'email'      => $request->user()->email,
                    'avatar'     => $request->user()->avatar ?? null,
                    'avatar_url' => $request->user()->avatar_url,
                    'role'       => $request->user()->roles()->first()?->name ?? null,
                    'school_id'  => $request->user()->school_id ?? null,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => session('success'),
                'error'   => fn () => session('error'),
            ],
            'faviconUrl' => fn () => once(function () {
                $path = PlatformSetting::get('platform_favicon');
                return $path ? asset('storage/' . $path) : null;
            }),
            'schoolBranding' => fn () => once(function () use ($request) {
                $user = $request->user();
                if (!$user || !$user->school_id) return null;
                $school = School::find($user->school_id);
                if (!$school) return null;
                return $school->branding;
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
}
