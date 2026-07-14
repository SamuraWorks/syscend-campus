<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add username, temporary password tracking, and audit fields to users
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(100) NULL');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS is_temporary_password BOOLEAN DEFAULT FALSE');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS password_changed_at TIMESTAMP NULL');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS created_by INTEGER NULL');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_secondary VARCHAR(20) NULL');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS must_change_password BOOLEAN DEFAULT TRUE');
        DB::statement('ALTER TABLE users ADD COLUMN IF NOT EXISTS last_password_change_at TIMESTAMP NULL');

        // Add user_id to students (for student login accounts)
        $studentHasUserId = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='students' AND column_name='user_id'");
        if (empty($studentHasUserId)) {
            DB::statement('ALTER TABLE students ADD COLUMN user_id INTEGER NULL');
        }

        // Add user_id to staff if not present (for staff login accounts)
        $staffHasUserId = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='staff' AND column_name='user_id'");
        if (empty($staffHasUserId)) {
            DB::statement('ALTER TABLE staff ADD COLUMN user_id INTEGER NULL');
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS username');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS is_temporary_password');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS password_changed_at');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS created_by');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS phone_secondary');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS must_change_password');
        DB::statement('ALTER TABLE users DROP COLUMN IF EXISTS last_password_change_at');
    }
};
