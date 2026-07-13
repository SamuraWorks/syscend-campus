<?php

namespace App\Policies;

use App\Models\FeePayment;
use App\Models\Guardian;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeePaymentPolicy
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

    public function view(User $user, FeePayment $feePayment): bool
    {
        if ($user->hasRole(['school-admin', 'principal', 'accountant'])) {
            return true;
        }

        if ($user->hasRole('student')) {
            return $user->id === $feePayment->student->user_id;
        }

        if ($user->hasRole('parent')) {
            return Guardian::where('user_id', $user->id)
                ->first()?->students()
                ->where('id', $feePayment->student_id)
                ->exists() ?? false;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(['school-admin', 'accountant']);
    }

    public function update(User $user, FeePayment $feePayment): bool
    {
        return $user->hasRole(['school-admin', 'accountant']);
    }

    public function delete(User $user, FeePayment $feePayment): bool
    {
        return $user->hasRole(['school-admin']);
    }
}
