<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pending_reports', function (Blueprint $table) {
            $table->id();
            $table->string('target_type');       // pharmacy, emergency_center, emergency_contact
            $table->string('target_id');
            $table->string('target_label');       // "Pharmacie Camp Ghezo" — pour affichage local
            $table->string('report_type');        // closed, open, not_responding, wrong_number
            $table->text('details')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamp('observed_at');
            $table->string('status')->default('queued'); // queued, sending, sent, failed
            $table->unsignedInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pending_reports');
    }
};
