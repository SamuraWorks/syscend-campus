<?php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StudentPolicy
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

    public function view(User $user, Student $student): bool
    {
        if ($user->hasRole(['school-admin', 'principal', 'teacher'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->id === $student->user_id;
        }

        if ($user->hasRole('parent')) {
            return Guardian::where('user_id', $user->id)
                ->first()?->students()
                ->where('id', $student->id)
                ->exists() ?? false;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }

    public function update(User $user, Student $student): bool
    {
        return $this->view($user, $student);
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->hasRole(['school-admin']);
    }
}
