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
        Schema::create('provisiones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('unidad_id')
                  ->constrained('unidades')
                  ->onDelete('cascade');
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('restrict');
            $table->string('descripcion', 150)->nullable();
            $table->decimal('monto_total', 15, 2)->default(0);
            $table->enum('periodicidad', ['mensual','anual','unica'])->default('anual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provisions');
    }
};
