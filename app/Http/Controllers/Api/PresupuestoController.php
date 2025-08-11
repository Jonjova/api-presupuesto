<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presupuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PresupuestoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $presupuestos = Presupuesto::with('unidad', 'detalles.subcategorias')->get();

        return response()->json(['success' => true, 'data' => $presupuestos]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'unidad_id' => 'required|exists:unidades,id',
                'anio' => [
                    'required',
                    'digits:4',
                    'integer',
                    'min:1900',
                    'max:' . (date('Y') + 5),
                    Rule::unique('presupuestos')->where(function ($query) use ($request) {
                        return $query->where('unidad_id', $request->unidad_id);
                    }),
                ],
                'detalles' => 'required|array|min:1',
                'detalles.*.categoria_id' => 'required|exists:categorias,id',
                'detalles.*.monto_anual' => 'required|numeric|min:0',
            ],
            [
                'anio.unique' => 'Ya existe un presupuesto para esta unidad y año.',
            ],
        );

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            \DB::beginTransaction();
            $presupuesto = Presupuesto::firstOrCreate(['unidad_id' => $request->unidad_id, 'anio' => $request->anio]);

            foreach ($request->detalles as $detalle) {
                $presupuesto->detalles()->create($detalle);
            }

            \DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'data' => $presupuesto->load('unidad', 'detalles.categoria'),
                ],
                201,
            );
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al crear el presupuesto: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $presupuesto = Presupuesto::with('unidad', 'detalles.categoria')->find($id);

        if (!$presupuesto) {
            return response()->json(['success' => false, 'message' => 'Presupuesto no encontrado'], 404);
        }

        return response()->json(['success' => true, 'data' => $presupuesto]);
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
    {
        $presupuesto = Presupuesto::find($id);

        if (!$presupuesto) {
            return response()->json(['success' => false, 'message' => 'Presupuesto no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'unidad_id' => 'sometimes|required|exists:unidades,id',
            'anio' => [
                'sometimes',
                'required',
                'digits:4',
                'integer',
                'min:1900',
                'max:' . (date('Y') + 5),
                Rule::unique('presupuestos')
                    ->where(function ($query) use ($request) {
                        return $query->where('unidad_id', $request->unidad_id ?? $presupuesto->unidad_id);
                    })
                    ->ignore($presupuesto->id),
            ],
            'detalles' => 'sometimes|required|array|min:1',
            'detalles.*.categoria_id' => 'required_with:detalles|exists:categorias,id',
            'detalles.*.monto_anual' => 'required_with:detalles|numeric|min:0',
        ], [
            'anio.unique' => 'Ya existe un presupuesto para esta unidad y año.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            \DB::beginTransaction();

            $presupuesto->update($request->only('unidad_id', 'anio'));

            if ($request->has('detalles')) {
                $existingDetails = $presupuesto->detalles->keyBy('categoria_id');
                
                foreach ($request->detalles as $detalle) {
                    if ($existingDetails->has($detalle['categoria_id'])) {
                        $existingDetails->get($detalle['categoria_id'])->update(['monto_anual' => $detalle['monto_anual']]);
                    } else {
                        $presupuesto->detalles()->create($detalle);
                    }
                }
                
                $requestCategoriaIds = collect($request->detalles)->pluck('categoria_id');
                $presupuesto->detalles()->whereNotIn('categoria_id', $requestCategoriaIds)->delete();
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'data' => $presupuesto->load('unidad', 'detalles.categoria')
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el presupuesto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $presupuesto = Presupuesto::find($id);

        if (!$presupuesto) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Presupuesto no encontrado',
                ],
                404,
            );
        }

        try {
            $presupuesto->delete();
            return response()->json([
                'success' => true,
                'message' => 'Presupuesto eliminado correctamente',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al eliminar el presupuesto: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
