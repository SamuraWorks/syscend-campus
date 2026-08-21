<?php

namespace App\Services;

use App\Models\{School, User, UserAuditLog};
use App\Mail\SchoolAdminOnboardingMail;
use App\Services\RoleRegistry;
use Illuminate\Support\Facades\{DB, Hash, Mail};
use Illuminate\Support\Str;

class SchoolAdminOnboardingService
{
    /**
     * Create a school and its initial School Admin with temporary credentials.
     *
     * @param array $schoolData   School fields (name, code, email, phone, address, etc.)
     * @param array $adminData    Admin fields (name, email, phone)
     * @param int   $createdBy    The super admin user ID
     * @return array{school: School, admin: User, temp_password: string}
     */
    public function createSchoolWithAdmin(array $schoolData, array $adminData, int $createdBy): array
    {
        return DB::transaction(function () use ($schoolData, $adminData, $createdBy) {
            // 1. Create the school
            $schoolData['slug'] = $schoolData['slug'] ?? Str::slug($schoolData['name']);
            $schoolData['status'] = 'active';
            $schoolData['is_configured'] = false;

            $school = School::create($schoolData);

            // 2. Generate temporary password
            $tempPassword = PasswordGeneratorService::generate(14, true);

            // 3. Create the School Admin user
            $admin = User::create([
                'school_id'             => $school->id,
                'name'                  => $adminData['name'],
                'email'                 => $adminData['email'],
                'phone'                 => $adminData['phone'] ?? null,
                'username'              => UserCreationService::generateUsername($adminData['name']),
                'password'              => Hash::make($tempPassword),
                'is_temporary_password' => true,
                'must_change_password'  => true,
                'force_password_change' => true,
                'status'                => 'active',
                'created_by'            => $createdBy,
            ]);

            // 4. Assign school-admin role
            $admin->assignRole(RoleRegistry::SCHOOL_ADMIN);

            // 5. Audit log
            UserAuditLog::log(
                $school->id,
                $admin->id,
                $createdBy,
                'school_admin_created',
                "School Admin account created for {$admin->name} (School: {$school->name})",
                [
                    'school_id'    => $school->id,
                    'school_name'  => $school->name,
                    'temp_generated' => true,
                ]
            );

            // 6. Send onboarding email
            $this->sendOnboardingEmail($school, $admin, $tempPassword);

            return [
                'school'        => $school,
                'admin'         => $admin,
                'temp_password' => $tempPassword,
            ];
        });
    }

    /**
     * Send the onboarding email to the School Admin.
     */
    protected function sendOnboardingEmail(School $school, User $admin, string $tempPassword): void
    {
        if (!$admin->email) {
            return;
        }

        try {
            $loginUrl = url('/login');

            Mail::to($admin->email)->send(new SchoolAdminOnboardingMail(
                $school,
                $admin,
                $tempPassword,
                $loginUrl
            ));
        } catch (\Throwable $e) {
            // Email failure should not block the creation flow
            \Log::warning('Failed to send school admin onboarding email', [
                'admin_id' => $admin->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }
}
