<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'tipo'];

    public static $rules = [
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|string|in:ingreso,gasto',
        'parent_id' => 'nullable|exists:categorias,id',
        'subcategorias' => 'sometimes|array',
        'subcategorias.*.nombre' => 'required_with:subcategorias|string|max:255',
        'subcategorias.*.tipo' => 'required_with:subcategorias|string|in:ingreso,gasto',
    ];

    // Mensajes de error personalizados estáticos
    public static $messages = [
        'nombre.required' => 'El nombre de la categoría es obligatorio',
        'tipo.in' => 'El tipo debe ser "ingreso" o "gasto"',
        'subcategorias.*.nombre.required_with' => 'Cada subcategoría debe tener un nombre',
        'subcategorias.*.tipo.in' => 'El tipo de cada subcategoría debe ser "ingreso" o "gasto"',
    ];

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

    // Relación para obtener subcategorías
    public function subcategorias()
    {
        return $this->hasMany(Categoria::class, 'parent_id');
    }

    // Relación para obtener la categoría padre
    public function parent()
    {
        return $this->belongsTo(Categoria::class, 'parent_id');
    }

    // Scope para obtener solo categorías principales
    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }

    // Método para verificar si una categoría es padre de otra
    public function isAncestorOf($categoryId)
    {
        return Categoria::where('id', $categoryId)->where('parent_id', $this->id)->exists();
    }
}
