<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresupuestoDetalle extends Model
{
    use HasFactory;
    protected $fillable = ['presupuesto_id', 'categoria_id', 'monto_anual'];

    public function presupuesto()
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
}
