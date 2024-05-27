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
        Schema::table('objectifcc_qualis', function (Blueprint $table) {
            $table->foreignId('objectifra_quali_id')->constrained('objectifra_qualis')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('objectifcc_qualis', function (Blueprint $table) {
            //
        });
    }
};
