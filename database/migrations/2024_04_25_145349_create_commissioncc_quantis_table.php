<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Annee;
use App\Models\Mois;
use App\Models\Cc;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('commissioncc_quantis', function (Blueprint $table) {
            $table->id();
            $table->integer('commission')->default(0);
            $table->foreignIdFor(Annee::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Mois::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Cc::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissioncc_quantis');
    }
};
