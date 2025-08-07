<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presupuesto extends Model
{
    use HasFactory;
    
    protected $table = 'presupuestos';
    protected $fillable = ['unidad_id', 'anio'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function detalles()
    {
        return $this->hasMany(PresupuestoDetalle::class);
    }
}
