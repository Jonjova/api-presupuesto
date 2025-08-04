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
        Schema::create('presupuesto_detalles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('presupuesto_id')
                  ->constrained('presupuestos')
                  ->onDelete('cascade');
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');
            $table->decimal('monto_anual', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['presupuesto_id','categoria_id'], 'ux_detalle_presupuesto_categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuesto_detalles');
    }
};
