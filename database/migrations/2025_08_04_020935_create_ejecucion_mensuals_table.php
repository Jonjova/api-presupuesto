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
        Schema::create('ejecucion_mensual', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('unidad_id')
                  ->constrained('unidades')
                  ->onDelete('cascade');
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');
            $table->foreignId('subcategoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');      
            $table->year('anio');
            $table->unsignedTinyInteger('mes');
            $table->decimal('monto', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['unidad_id','categoria_id','anio','mes'], 'ux_ejecucion_unidad_cat_anio_mes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejecucion_mensuals');
    }
};
