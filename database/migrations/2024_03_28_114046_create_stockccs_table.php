<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Mois;
use App\Models\Annee;
use App\Models\Cc;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stockccs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Mois::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Annee::class)->constrained()->cascadeOnDelete();
            $table->foreignId('stockra_id')->constrained('stockras')->cascadeOnDelete();
            $table->integer('value')->default(0);
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
        Schema::dropIfExists('stockccs');
    }
};
