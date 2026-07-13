<?php

namespace App\Policies;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class StaffPolicy
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

    public function view(User $user, Staff $staff): bool
    {
        if ($user->hasRole(['school-admin', 'principal'])) {
            return true;
        }

        if ($user->hasRole('staff')) {
            return $user->id === $staff->user_id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin']);
    }

    public function update(User $user, Staff $staff): bool
    {
        return $user->hasRole(['school-admin']);
    }

    public function delete(User $user, Staff $staff): bool
    {
        return $user->hasRole(['school-admin']);
    }
}
