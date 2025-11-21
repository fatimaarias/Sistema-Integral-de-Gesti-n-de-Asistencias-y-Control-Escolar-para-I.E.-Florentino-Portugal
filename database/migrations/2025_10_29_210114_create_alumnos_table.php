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
       Schema::create('alumnos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre_apellido');
        $table->string('direccion')->nullable();
        $table->enum('genero', ['MASCULINO', 'FEMENINO']);
        $table->foreignId('grado_id')->constrained('grados')->onDelete('cascade');
        $table->foreignId('seccion_id')->constrained('secciones')->onDelete('cascade');
        $table->timestamps();
    });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
