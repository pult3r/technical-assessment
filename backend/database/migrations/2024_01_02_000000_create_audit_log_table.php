<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();

            $table->string('table_name');
            $table->unsignedBigInteger('row_id');
            $table->string('action'); // INSERT / UPDATE / DELETE

            $table->json('data_before')->nullable();
            $table->json('data_after')->nullable();

            $table->unsignedBigInteger('user_id')->nullable(); // will fill later from JWT
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
