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
        Schema::create('ofertas_empleo', function (Blueprint $table) {
            $table->id();
            $table->string('estado');
            $table->string('empresa');
            $table->string('telefono');
            $table->string('nombre_puesto');
            $table->text('requisitos')->nullable();
            $table->timestamps();
            $table->addDomainId(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas_empleo');
    }
};
