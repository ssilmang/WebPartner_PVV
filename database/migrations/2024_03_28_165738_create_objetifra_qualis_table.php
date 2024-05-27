<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Ra;
use App\Models\IndicateurQuali;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('objetifra_qualis', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IndicateurQuali::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Ra::class)->constrained()->cascadeOnDelete();
            $table->integer('realisation')->default(0);
            $table->integer('taux')->default(0);
            $table->boolean('statut')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objetifra_qualis');
    }
};
