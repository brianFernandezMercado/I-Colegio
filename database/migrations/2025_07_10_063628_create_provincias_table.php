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
        Schema::create('provincias', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('departamento_id');
    $table->string('nombre');
    $table->string('icono')->nullable();
    $table->timestamps();

    $table->foreign('departamento_id')->references('id')->on('departamentos')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provincias');
    }
};
