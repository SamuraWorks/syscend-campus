<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DemoResetSeeder extends Seeder
{
    protected array $protectedEmails = ['syscend@gmail.com'];

    public function run(): void
    {
        $this->command->info('Resetting platform — removing all demo/operational data...');

        DB::statement('SET session_replication_role = replica;');

        $tables = $this->getCleanupTables();
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
                $this->command->line("  ✓ Cleared: {$table}");
            }
        }

        DB::statement('SET session_replication_role = DEFAULT;');

        User::withoutGlobalScopes()
            ->whereNotIn('email', $this->protectedEmails)
            ->forceDelete();

        $this->command->info('Platform reset complete.');
    }

    private function getCleanupTables(): array
    {
        return [
            // Activity & audit
            'activity_log', 'user_audit_logs',

            // Reports & results
            'report_cards', 'result_approval_logs', 'result_change_requests',
            'assessment_components', 'school_assessment_configs',
            'exam_assessment_links', 'assessment_types',

            // Examination
            'marks', 'exams', 'national_examinations',

            // Attendance
            'attendance_corrections', 'attendances',

            // Academic
            'timetables', 'lessons', 'homework_submissions', 'homework',
            'syllabi', 'online_classes',

            // Fees & finance
            'fee_payments', 'fee_structures', 'fee_categories',
            'payrolls', 'salary_structures',

            // Students & staff
            'student_documents', 'staff_documents',
            'students', 'staff', 'guardians',

            // Users (non-super-admin)
            'users',

            // Academic structure
            'subjects', 'sections', 'classes', 'academic_terms', 'academic_years',

            // Departments & designations
            'designations', 'departments',

            // Communication
            'announcements', 'messages', 'notifications',
            'email_templates', 'notification_templates',

            // Library
            'book_issues', 'library_books',

            // Transport
            'student_routes', 'transport_routes', 'vehicles',

            // Hostel
            'hostel_allocations', 'hostel_rooms', 'hostels',

            // Inventory
            'maintenance_logs', 'assets', 'inventory_purchases', 'inventory_items', 'inventory_categories',

            // Visitors & inquiries
            'visitor_logs', 'admission_inquiries',

            // Documents
            'document_imports',

            // Schedule
            'schedule_periods', 'schedule_event_types', 'school_time_settings',
            'holidays', 'shifts',

            // School settings
            'school_settings',

            // Subscriptions & packages
            'school_subscriptions', 'coupons', 'package_modules', 'packages',

            // Demo requests
            'demo_requests',

            // Schools (will recreate one)
            'schools',

            // Reset sequences
            'platform_settings',
        ];
    }
}
