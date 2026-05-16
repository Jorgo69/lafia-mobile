<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_snapshots', function (Blueprint $table) {
            $table->string('resource')->primary();
            $table->string('version');
            $table->json('data');
            $table->unsignedInteger('item_count');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_snapshots');
    }
};
