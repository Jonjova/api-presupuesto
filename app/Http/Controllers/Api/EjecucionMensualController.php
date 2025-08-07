<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EjecucionMensual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EjecucionMensualController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Puedes agregar filtros por unidad, categoría, año o mes
        $query = EjecucionMensual::with(['unidad', 'categoria']);

        if ($request->has('unidad_id')) {
            $query->where('unidad_id', $request->unidad_id);
        }

        if ($request->has('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        if ($request->has('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->has('mes')) {
            $query->where('mes', $request->mes);
        }

        $ejecuciones = $query->get();

        return response()->json($ejecuciones);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unidad_id' => 'required|exists:unidades,id',
            'categoria_id' => 'required|exists:categories,id',
            'anio' => 'required|integer|min:2000|max:2100',
            'mes' => 'required|integer|min:1|max:12',
            'monto' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Verificar si ya existe un registro para esa unidad, categoría, año y mes
        $existente = EjecucionMensual::where('unidad_id', $request->unidad_id)
            ->where('categoria_id', $request->categoria_id)
            ->where('anio', $request->anio)
            ->where('mes', $request->mes)
            ->first();

        if ($existente) {
            return response()->json(['message' => 'Ya existe un registro para esta unidad, categoría, año y mes'], 400);
        }

        $ejecucion = EjecucionMensual::create($request->all());

        return response()->json($ejecucion, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $ejecucion = EjecucionMensual::with(['unidad', 'categoria'])->find($id);

        if (!$ejecucion) {
            return response()->json(['message' => 'Ejecución mensual no encontrada'], 404);
        }

        return response()->json($ejecucion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ejecucion = EjecucionMensual::find($id);

        if (!$ejecucion) {
            return response()->json(['message' => 'Ejecución mensual no encontrada'], 404);
        }

        $validator = Validator::make($request->all(), [
            'unidad_id' => 'sometimes|required|exists:unidades,id',
            'categoria_id' => 'sometimes|required|exists:categories,id',
            'anio' => 'sometimes|required|integer|min:2000|max:2100',
            'mes' => 'sometimes|required|integer|min:1|max:12',
            'monto' => 'sometimes|required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // Verificar si los cambios crearían un duplicado
        if ($request->has(['unidad_id', 'categoria_id', 'anio', 'mes'])) {
            $existente = EjecucionMensual::where('unidad_id', $request->unidad_id ?? $ejecucion->unidad_id)
                ->where('categoria_id', $request->categoria_id ?? $ejecucion->categoria_id)
                ->where('anio', $request->anio ?? $ejecucion->anio)
                ->where('mes', $request->mes ?? $ejecucion->mes)
                ->where('id', '!=', $id)
                ->first();

            if ($existente) {
                return response()->json(['message' => 'Ya existe otro registro para esta unidad, categoría, año y mes'], 400);
            }
        }

        $ejecucion->update($request->all());

        return response()->json($ejecucion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ejecucion = EjecucionMensual::find($id);

        if (!$ejecucion) {
            return response()->json(['message' => 'Ejecución mensual no encontrada'], 404);
        }

        $ejecucion->delete();

        return response()->json(null, 204);
    }

    /**
     * Obtener resumen por año y unidad
     */
    public function resumenAnual(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'anio' => 'required|integer|min:2000|max:2100',
            'unidad_id' => 'required|exists:unidades,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $resumen = EjecucionMensual::where('anio', $request->anio)
            ->where('unidad_id', $request->unidad_id)
            ->selectRaw('categoria_id, sum(monto) as total, avg(monto) as promedio')
            ->groupBy('categoria_id')
            ->with('categoria')
            ->get();

        return response()->json($resumen);
    }
     /**
     * Ejecuciones por unidad
     */
    public function porUnidad($unidadId)
    {
        $ejecuciones = EjecucionMensual::with(['categoria'])
            ->where('unidad_id', $unidadId)
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();
            
        return response()->json($ejecuciones);
    }

    /**
     * Ejecuciones por categoría
     */
    public function porCategoria($categoriaId)
    {
        $ejecuciones = EjecucionMensual::with(['unidad'])
            ->where('categoria_id', $categoriaId)
            ->orderBy('anio')
            ->orderBy('mes')
            ->get();
            
        return response()->json($ejecuciones);
    }
}
