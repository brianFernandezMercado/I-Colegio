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
       Schema::create('departamentos', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('pais_id');
    $table->string('nombre');
    $table->string('icono')->nullable();
    $table->boolean('activo')->default(1);
    $table->timestamps();

    $table->foreign('pais_id')->references('id')->on('paises')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
