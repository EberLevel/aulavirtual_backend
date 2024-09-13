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
        Schema::create('proyecto_tarea_archivos', function (Blueprint $table) {
            $table->id(); // Columna ID primaria autoincremental
            $table->longText('contenido'); // Ajusta el tipo de columna según tus necesidades
            $table->unsignedBigInteger('proyecto_tarea_id'); // Llave foránea a 'proyecto_tarea'
            $table->timestamps(); // Agrega columnas 'created_at' y 'updated_at'

            // Definir la llave foránea
            $table->foreign('proyecto_tarea_id')->references('id')->on('proyecto_tarea')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proyecto_tarea_archivos');
    }
};
