<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('identity_id')->constrained('identities')->cascadeOnDelete();
            $table->string('guardian_alias');
            $table->string('guardian_public_key');
            $table->text('encrypted_fragment');
            $table->integer('fragment_index');
            $table->string('status')->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index(['identity_id', 'status']);
            $table->unique(['identity_id', 'fragment_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
