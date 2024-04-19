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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('adm_no');
            $table->unsignedBigInteger('form')->nullable();
            $table->integer('form_sequence_number')->nullable();
            $table->unsignedBigInteger('stream_id')->nullable();
            $table->integer('term')->nullable();
            $table->timestamps();

            $table->foreign('form')->references('id')->on('class_forms')->onDelete('cascade');
            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
