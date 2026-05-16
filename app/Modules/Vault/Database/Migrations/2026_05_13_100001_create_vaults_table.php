<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaults', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('data_type');
            $table->string('label');
            $table->text('encrypted_payload');
            $table->string('public_key_fingerprint');
            $table->timestamps();

            $table->index(['user_id', 'data_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaults');
    }
};
