<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo')->nullable();
            $table->string('username')->unique()->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('celular')->nullable();
            $table->text('descripcion')->nullable();
            $table->float('calificacion')->default(0);
            $table->integer('propuestas')->default(0);
            $table->string('imagen')->nullable();
            $table->string('zona')->nullable();
            $table->text('experiencia')->nullable();
            $table->string('ci')->nullable();
            $table->string('estado')->default('activo');
            $table->enum('rol', ['trabajador', 'administracion', 'cliente'])->default('cliente');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
