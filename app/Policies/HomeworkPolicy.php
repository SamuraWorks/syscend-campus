<?php

namespace App\Policies;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HomeworkPolicy
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

    public function view(User $user, Homework $homework): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        if ($user->hasRole(['school-admin'])) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return true;
        }

        return false;
    }

    public function update(User $user, Homework $homework): bool
    {
        if ($user->hasRole(['school-admin'])) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $user->id === $homework->teacher->user_id;
        }

        return false;
    }

    public function delete(User $user, Homework $homework): bool
    {
        if ($user->hasRole(['school-admin'])) {
            return true;
        }

        if ($user->hasRole('teacher')) {
            return $user->id === $homework->teacher->user_id;
        }

        return false;
    }
}
