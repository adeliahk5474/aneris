<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL: drop constraint lama, buat baru dengan nilai lengkap
            DB::statement("
                ALTER TABLE notifications
                DROP CONSTRAINT IF EXISTS notifications_type_check
            ");

            DB::statement("
                ALTER TABLE notifications
                ADD CONSTRAINT notifications_type_check
                CHECK (type IN ('system','order','commission','review','like'))
            ");

        } else {
            // MySQL
            DB::statement("
                ALTER TABLE notifications
                MODIFY COLUMN type
                ENUM('system','order','commission','review','like')
                NOT NULL DEFAULT 'system'
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE notifications
                DROP CONSTRAINT IF EXISTS notifications_type_check
            ");

            DB::statement("
                ALTER TABLE notifications
                ADD CONSTRAINT notifications_type_check
                CHECK (type IN ('system','order','commission'))
            ");

        } else {
            DB::statement("
                ALTER TABLE notifications
                MODIFY COLUMN type
                ENUM('system','order','commission')
                NOT NULL DEFAULT 'system'
            ");
        }
    }
};
