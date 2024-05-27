<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mois;
use App\Models\Annee;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stockcc_qualis', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mois::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Annee::class)->constrained()->cascadeOnDelete();
            $table->foreignId('objectifcc_quali_id')->constrained('objectifcc_qualis')->cascadeOnDelete();
            $table->string('realisation');
            $table->string('taux');
            $table->boolean('statut')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockcc_qualis');
    }
};
