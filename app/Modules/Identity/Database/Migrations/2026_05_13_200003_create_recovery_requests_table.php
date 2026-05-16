<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recovery_requests', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('identity_id')->constrained('identities')->cascadeOnDelete();
            $table->string('new_device_uuid');
            $table->string('new_device_public_key');
            $table->string('status')->default('pending');
            $table->integer('fragments_needed');
            $table->integer('fragments_received')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_requests');
    }
};
