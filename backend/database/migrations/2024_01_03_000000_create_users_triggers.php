<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // INSERT
        DB::unprepared("
            CREATE TRIGGER users_after_insert
            AFTER INSERT ON users
            FOR EACH ROW
            INSERT INTO audit_log (table_name, row_id, action, data_after, user_id, created_at)
            VALUES (
                'users',
                NEW.id,
                'INSERT',
                JSON_OBJECT(
                    'username', NEW.username,
                    'email', NEW.email
                ),
                NULL,
                NOW()
            );
        ");

        // UPDATE
        DB::unprepared("
            CREATE TRIGGER users_after_update
            AFTER UPDATE ON users
            FOR EACH ROW
            INSERT INTO audit_log (table_name, row_id, action, data_before, data_after, user_id, created_at)
            VALUES (
                'users',
                NEW.id,
                'UPDATE',
                JSON_OBJECT(
                    'username', OLD.username,
                    'email', OLD.email
                ),
                JSON_OBJECT(
                    'username', NEW.username,
                    'email', NEW.email
                ),
                NULL,
                NOW()
            );
        ");

        // DELETE
        DB::unprepared("
            CREATE TRIGGER users_after_delete
            AFTER DELETE ON users
            FOR EACH ROW
            INSERT INTO audit_log (table_name, row_id, action, data_before, user_id, created_at)
            VALUES (
                'users',
                OLD.id,
                'DELETE',
                JSON_OBJECT(
                    'username', OLD.username,
                    'email', OLD.email
                ),
                NULL,
                NOW()
            );
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS users_after_insert;");
        DB::unprepared("DROP TRIGGER IF EXISTS users_after_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS users_after_delete;");
    }
};
