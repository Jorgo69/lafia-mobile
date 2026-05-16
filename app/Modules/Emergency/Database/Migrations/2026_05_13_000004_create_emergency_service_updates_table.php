<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_service_updates', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('emergency_contact_id')->constrained('emergency_contacts')->cascadeOnDelete();
            $table->string('status')->default('pending');
            $table->string('reported_issue');
            $table->string('suggested_phone_number')->nullable();
            $table->text('details')->nullable();
            $table->decimal('reporter_latitude', 10, 7)->nullable();
            $table->decimal('reporter_longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_service_updates');
    }
};
