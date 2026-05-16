<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pharmacy_guards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('zone');
            $table->timestamps();

            $table->index(['zone', 'start_date', 'end_date']);
            $table->index(['pharmacy_id', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pharmacy_guards');
    }
};
