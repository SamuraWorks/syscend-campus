<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\Controller;
use App\Models\{Guardian, School, User};
use App\Services\RegistryVerificationService;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\{Auth, Hash, RateLimiter};
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class ParentRegistrationController extends Controller
{
    private string $throttleKey = 'registration-verify-parent';

    public function show(Request $request, string $schoolSlug): Response
    {
        $school = School::where('slug', $schoolSlug)
            ->where('status', 'active')
            ->firstOrFail();

        return Inertia::render('Registration/Parent', [
            'school' => $school->only('id', 'name', 'slug', 'code'),
        ]);
    }

    public function verify(Request $request, string $schoolSlug)
    {
        if (RateLimiter::tooManyAttempts($this->throttleKey . ':' . $request->ip(), 5)) {
            $seconds = RateLimiter::availableIn($this->throttleKey . ':' . $request->ip());
            return back()->withErrors([
                'guardian_id' => 'Too many verification attempts. Please try again in ' . $seconds . ' seconds.',
            ]);
        }

        $school = School::where('slug', $schoolSlug)->firstOrFail();

        $data = $request->validate([
            'guardian_id' => 'required|string|max:50',
            'full_name'   => 'required|string|max:255',
            'email'       => 'nullable|email|max:255',
        ]);

        RateLimiter::hit($this->throttleKey . ':' . $request->ip(), 60);

        $service = new RegistryVerificationService();
        $result = $service->verifyParent($school->id, $data['guardian_id'], $data['full_name'], $data['email'] ?? null);

        if (!$result['success']) {
            RateLimiter::hit($this->throttleKey . ':' . $request->ip(), 60);
            $errors = ['guardian_id' => $result['message']];
            if (isset($result['requires_email'])) {
                $errors['email'] = $result['message'];
                unset($errors['guardian_id']);
            }
            return back()->withErrors($errors)->onlyInput('guardian_id', 'full_name');
        }

        $verificationToken = bin2hex(random_bytes(32));
        session([
            "registration.verify_{$verificationToken}" => [
                'guardian_id' => $result['guardian']->id,
                'school_id'   => $school->id,
                'expires_at'  => now()->addMinutes(15)->timestamp,
            ],
            'registration.verify_token' => $verificationToken,
        ]);

        return back()->with('verified', [
            'guardian_name' => $result['guardian']->name,
            'children'      => $result['children'] ?? [],
            'message'       => $result['message'],
            'verify_token'  => $verificationToken,
        ]);
    }

    public function complete(Request $request, string $schoolSlug)
    {
        $school = School::where('slug', $schoolSlug)->firstOrFail();

        $verifyToken = session('registration.verify_token');
        $sessionData = $verifyToken ? session("registration.verify_{$verifyToken}") : null;

        if (!$sessionData || $sessionData['expires_at'] < now()->timestamp) {
            session()->forget('registration');
            return back()->withErrors(['message' => 'Verification session expired. Please verify again.']);
        }

        $guardian = Guardian::where('school_id', $sessionData['school_id'])
            ->where('id', $sessionData['guardian_id'])
            ->first();

        if (!$guardian || $guardian->claimed_by !== null || $guardian->user_id !== null) {
            session()->forget('registration');
            return back()->withErrors(['message' => 'This record is no longer available for registration.']);
        }

        $data = $request->validate([
            'email'                => ['required', 'email', 'unique:users,email'],
            'password'             => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => 'required',
        ]);

        $user = User::create([
            'school_id'             => $school->id,
            'name'                  => $guardian->name,
            'email'                 => $data['email'],
            'phone'                 => $guardian->phone,
            'password'              => Hash::make($data['password']),
            'is_temporary_password' => false,
            'must_change_password'  => false,
            'status'                => 'active',
            'registration_status'   => 'registered',
        ]);

        $user->assignRole('parent');

        $service = new RegistryVerificationService();
        $claimResult = $service->claimRecordWithLock($guardian, $user->id, Guardian::class);

        if (!$claimResult['success']) {
            $user->delete();
            session()->forget('registration');
            return back()->withErrors(['message' => $claimResult['message']]);
        }

        $guardian->update(['user_id' => $user->id]);

        activity()
            ->causedBy($user)
            ->performedOn($guardian)
            ->withProperties(['school_id' => $school->id, 'registration_type' => 'parent'])
            ->log('Parent self-registration completed');

        session()->forget('registration');

        Auth::login($user);
        $user->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard'));
    }
}
