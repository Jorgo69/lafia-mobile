<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ussd_favorites', function (Blueprint $table) {
            $table->id();
            $table->string('device_id');
            $table->foreignId('ussd_code_id')->constrained('ussd_codes')->cascadeOnDelete();
            $table->json('saved_params')->nullable(); // Parametres pre-remplis (ex: numero frequent)
            $table->string('custom_label')->nullable();
            $table->integer('use_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->unique(['device_id', 'ussd_code_id']);
            $table->index(['device_id', 'use_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ussd_favorites');
    }
};
