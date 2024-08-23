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
        Schema::create('academic_formation', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('cv_bank_id');
            $table->unsignedBigInteger('user_id');
            $table->string('education')->nullable();
            $table->string('profession')->nullable();
            $table->string('con')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['En curso', 'Finalizado'])->nullable();
            $table->string('advance')->nullable();
            $table->string('institute')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->text('observation')->nullable();
            $table->addDomainId();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_formation');
    }
};
