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
        Schema::create('aula_availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aula_id')->constrained();
            $table->foreignId('domain_id')->constrained();
            $table->date('fecha');
            $table->integer('fecha_day');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aula_availability');
    }
};