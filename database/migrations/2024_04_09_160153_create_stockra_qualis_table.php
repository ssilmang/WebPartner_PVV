<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stockra_qualis', function (Blueprint $table) {
            $table->id();
            $table->integer('realisation')->default(0);
            $table->integer('taux')->default(0);
            $table->integer('commission')->default(0);
            $table->foreignId("objectira_quali_id")->constrained("objectifra_qualis")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockra_qualis');
    }
};
