<?php

namespace App\Policies;

use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class GuardianPolicy
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

    public function view(User $user, Guardian $guardian): bool
    {
        if ($user->hasRole(['school-admin', 'principal'])) {
            return true;
        }

        if ($user->hasRole('parent')) {
            return $user->id === $guardian->user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }

    public function update(User $user, Guardian $guardian): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }

    public function delete(User $user, Guardian $guardian): bool
    {
        return $user->hasRole(['school-admin', 'principal']);
    }
}
