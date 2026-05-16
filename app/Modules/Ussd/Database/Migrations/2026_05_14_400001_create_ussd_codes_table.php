<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ussd_codes', function (Blueprint $table) {
            $table->id();
            $table->string('operator');        // mtn, moov, celtiis
            $table->string('category');         // mobile_money, forfait, facture...
            $table->string('action_type');      // direct, guided, menu
            $table->string('slug')->unique();
            $table->string('label');            // "Envoyer de l'argent"
            $table->string('description')->nullable();
            $table->string('code');             // *880#, *855*1*1*1*{numero}*{montant}*{code}#
            $table->json('params')->nullable(); // [{key: "numero", label: "Numero", type: "tel"}, ...]
            $table->json('steps')->nullable();  // Guide pas-a-pas en FR simplifie
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['operator', 'category']);
            $table->index(['operator', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ussd_codes');
    }
};
