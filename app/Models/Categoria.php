<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
     protected $fillable = ['nombre', 'tipo'];

    public function detallesPresupuesto()
    {
        return $this->hasMany(PresupuestoDetalle::class);
    }

    public function ejecuciones()
    {
        return $this->hasMany(EjecucionMensual::class);
    }

    public function provisiones()
    {
        return $this->hasMany(Provision::class);
    }
}
