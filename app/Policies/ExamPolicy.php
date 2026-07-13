<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ExamPolicy
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

    public function view(User $user, Exam $exam): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }
}
