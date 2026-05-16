<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_fragments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('recovery_request_id')->constrained('recovery_requests')->cascadeOnDelete();
            $table->foreignUlid('guardian_id')->constrained('guardians')->cascadeOnDelete();
            $table->text('re_encrypted_fragment');
            $table->timestamps();

            $table->unique(['recovery_request_id', 'guardian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_fragments');
    }
};
