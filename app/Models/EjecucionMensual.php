<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EjecucionMensual extends Model
{
    use HasFactory;
     protected $table = 'ejecucion_mensual';
    protected $fillable = ['unidad_id', 'categoria_id', 'anio', 'mes', 'monto'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
