<?php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\Mark;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MarkPolicy
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

    public function view(User $user, Mark $mark): bool
    {
        if ($user->hasRole(['school-admin', 'principal', 'teacher'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->id === $mark->student->user_id;
        }

        if ($user->hasRole('parent')) {
            return Guardian::where('user_id', $user->id)
                ->first()?->students()
                ->where('id', $mark->student_id)
                ->exists() ?? false;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'teacher']);
    }

    public function update(User $user, Mark $mark): bool
    {
        return $user->hasRole(['school-admin', 'teacher']);
    }

    public function delete(User $user, Mark $mark): bool
    {
        return $user->hasRole(['school-admin']);
    }
}
