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
        Schema::create('proyecto_tarea', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Campo de nombre
            $table->string('prioridad'); // Campo de prioridad
            $table->string('estado'); // Campo de estado
            $table->string('grupo')->nullable(); // Campo de grupo, puede ser nulo
            $table->string('responsable')->nullable(); // Campo de responsable, puede ser nulo
            $table->longText('descripcion')->nullable();
            $table->unsignedBigInteger('proyecto_id')->nullable()->index(); // Clave foránea a proyectos
            $table->timestamps(); // Campos de timestamp para created_at y updated_at

            // Definición de la clave foránea para proyecto_id
            $table->foreign('proyecto_id')->references('id')->on('proyectos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_tarea');
    }
};
