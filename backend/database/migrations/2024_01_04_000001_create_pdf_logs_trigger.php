<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS pdf_logs_ai;
        ");

        DB::unprepared("
            CREATE TRIGGER pdf_logs_ai
            AFTER INSERT ON pdf_logs
            FOR EACH ROW
            BEGIN
                INSERT INTO audit_log (
                    table_name,
                    row_id,
                    action,
                    data_before,
                    data_after,
                    user_id,
                    created_at
                )
                VALUES (
                    'pdf_logs',
                    NEW.id,
                    'INSERT',
                    NULL,
                    JSON_OBJECT(
                        'id', NEW.id,
                        'user_id', NEW.user_id,
                        'input_text', NEW.input_text,
                        'pdf_filename', NEW.pdf_filename,
                        'pdf_url', NEW.pdf_url
                    ),
                    @user_id,
                    NOW()
                );
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS pdf_logs_ai;");
    }
};
