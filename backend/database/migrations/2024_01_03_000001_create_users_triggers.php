<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // INSERT trigger
        DB::unprepared("
            CREATE TRIGGER users_ai
            AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_log (table_name, row_id, action, data_before, data_after, user_id, created_at)
                VALUES (
                    'users',
                    NEW.id,
                    'INSERT',
                    NULL,
                    JSON_OBJECT(
                        'id', NEW.id,
                        'username', NEW.username,
                        'email', NEW.email
                    ),
                    @user_id,
                    NOW()
                );
            END
        ");

        // UPDATE trigger
        DB::unprepared("
            CREATE TRIGGER users_au
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_log (table_name, row_id, action, data_before, data_after, user_id, created_at)
                VALUES (
                    'users',
                    NEW.id,
                    'UPDATE',
                    JSON_OBJECT(
                        'id', OLD.id,
                        'username', OLD.username,
                        'email', OLD.email
                    ),
                    JSON_OBJECT(
                        'id', NEW.id,
                        'username', NEW.username,
                        'email', NEW.email
                    ),
                    @user_id,
                    NOW()
                );
            END
        ");

        // DELETE trigger
        DB::unprepared("
            CREATE TRIGGER users_ad
            AFTER DELETE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_log (table_name, row_id, action, data_before, data_after, user_id, created_at)
                VALUES (
                    'users',
                    OLD.id,
                    'DELETE',
                    JSON_OBJECT(
                        'id', OLD.id,
                        'username', OLD.username,
                        'email', OLD.email
                    ),
                    NULL,
                    @user_id,
                    NOW()
                );
            END
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS users_ai");
        DB::unprepared("DROP TRIGGER IF EXISTS users_au");
        DB::unprepared("DROP TRIGGER IF EXISTS users_ad");
    }
};
