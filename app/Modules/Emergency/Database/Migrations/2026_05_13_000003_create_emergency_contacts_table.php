<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_contacts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('emergency_center_id')->constrained('emergency_centers')->cascadeOnDelete();
            $table->string('operator');
            $table->string('phone_number');
            $table->integer('priority_score')->default(0);
            $table->string('provider_routing')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['emergency_center_id', 'operator']);
            $table->index('operator');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_contacts');
    }
};
