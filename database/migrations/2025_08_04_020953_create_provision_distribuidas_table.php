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
        Schema::create('provision_distribuidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('provision_id')
                  ->constrained('provisiones')
                  ->onDelete('cascade');
            $table->year('anio');
            $table->unsignedTinyInteger('mes');
            $table->decimal('monto', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['provision_id','anio','mes'], 'ux_prov_dist_prov_anio_mes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provision_distribuidas');
    }
};
