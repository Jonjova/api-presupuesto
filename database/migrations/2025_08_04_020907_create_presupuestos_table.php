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
       Schema::create('presupuestos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('unidad_id')
                  ->constrained('unidades')
                  ->onDelete('cascade');
            $table->year('anio');
            $table->timestamps();

            $table->unique(['unidad_id','anio'], 'ux_presupuesto_unidad_anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
