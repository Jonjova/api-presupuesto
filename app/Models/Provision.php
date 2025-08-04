<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provision extends Model
{
    use HasFactory;
    protected $fillable = ['unidad_id', 'categoria_id', 'descripcion', 'monto_total', 'periodicidad'];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function distribuidas()
    {
        return $this->hasMany(ProvisionDistribuida::class);
    }
}
