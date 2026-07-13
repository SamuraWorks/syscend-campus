<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttendancePolicy
{
    use AuthorizesRequests;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if ($user->hasRole(['school-admin', 'principal', 'teacher', 'accountant'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            if ($attendance->attendable_type === Student::class
                && $attendance->attendable_id === $user->id) {
                return true;
            }

            $student = Student::where('user_id', $user->id)->first();
            if ($student && $attendance->attendable_type === Student::class
                && $attendance->attendable_id === $student->id) {
                return true;
            }

            return false;
        }

        if ($user->hasRole('parent')) {
            $guardian = Guardian::where('user_id', $user->id)->first();
            if (! $guardian) {
                return false;
            }

            if ($attendance->attendable_type === Student::class) {
                return $guardian->students()
                    ->where('id', $attendance->attendable_id)
                    ->exists();
            }

            return false;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'teacher']);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if (! $user->hasRole(['school-admin'])) {
            return false;
        }

        return is_null($attendance->approved_at);
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        if (! $user->hasRole(['school-admin'])) {
            return false;
        }

        return ! $attendance->approved_at;
    }
}
