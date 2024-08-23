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
        Schema::create('work_experience', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->unsignedBigInteger('cv_bank_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('insitution')->nullable();
            $table->string('image')->nullable();
            $table->string('especific_experience')->nullable();
            $table->string('general_experience')->nullable();
            $table->string('area')->nullable();
            $table->string('link_lab')->nullable();
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
        Schema::dropIfExists('work_experience');
    }
};
