<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'descripcion'];

    public function presupuestos()
    {
        return $this->hasMany(Presupuesto::class);
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
