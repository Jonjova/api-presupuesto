<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EjecucionMensual extends Model
{
    use HasFactory;
    protected $table = 'ejecucion_mensual';
    protected $fillable = ['unidad_id', 'categoria_id', 'subcategoria_id', 'anio', 'mes', 'monto'];

    static $messages = [
        'unidad_id.required' => 'El campo unidad es obligatorio.',
        'categoria_id.required' => 'El campo categoría es obligatorio.',
        'subcategoria_id.required' => 'El campo subcategoría es obligatorio.',
        'anio.required' => 'El campo año es obligatorio.',
        'mes.required' => 'El campo mes es obligatorio.',
        'monto.required' => 'El campo monto es obligatorio.',
    ];

    public static $messagesUpdate = [
        'unidad_id.required' => 'El campo unidad es obligatorio.',
        'unidad_id.exists' => 'La unidad seleccionada no es válida.',
        'categoria_id.required' => 'El campo categoría es obligatorio.',
        'categoria_id.exists' => 'La categoría seleccionada no es válida.',
        'subcategoria_id.required' => 'El campo subcategoría es obligatorio.',
        'subcategoria_id.exists' => 'La subcategoría seleccionada no es válida.',
        'anio.required' => 'El campo año es obligatorio.',
        'anio.integer' => 'El año debe ser un número entero.',
        'anio.min' => 'El año debe ser como mínimo :min.',
        'anio.max' => 'El año debe ser como máximo :max.',
        'mes.required' => 'El campo mes es obligatorio.',
        'mes.integer' => 'El mes debe ser un número entero.',
        'mes.min' => 'El mes debe ser como mínimo :min.',
        'mes.max' => 'El mes debe ser como máximo :max.',
        'monto.required' => 'El campo monto es obligatorio.',
        'monto.numeric' => 'El monto debe ser un número.',
        'monto.min' => 'El monto debe ser como mínimo :min.',
    ];

    public function unidad()
    {
        return $this->belongsTo(Unidad::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }
    
    public function subcategoria()
    {
        return $this->belongsTo(Categoria::class, 'subcategoria_id');
    }
}
