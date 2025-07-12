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
      Schema::create('sub_servicios', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('categoria_servicio_id');
    $table->string('nombre');
    $table->text('descripcion')->nullable();
    $table->string('icono')->nullable();
    $table->boolean('activo')->default(true);
    $table->timestamps();

    $table->foreign('categoria_servicio_id')->references('id')->on('categorias_servicios')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_servicios');
    }
};
