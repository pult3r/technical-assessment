<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pdf_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // powiązany user
            $table->text('input_text');                         // tekst wejściowy
            $table->string('pdf_filename');                    // nazwa wygenerowanego pliku
            $table->string('pdf_url');                         // pełny URL do pliku
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pdf_logs');
    }
};
