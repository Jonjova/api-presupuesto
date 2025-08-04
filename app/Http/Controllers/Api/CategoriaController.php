<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Obtener parámetros de consulta
            $onlyParents = $request->query('onlyParents', false);
            $withChildren = $request->query('withChildren', false);

            $query = Categoria::query();

            if ($onlyParents) {
                $query->whereNull('parent_id');
            }

            if ($withChildren) {
                $query->with('subcategorias');
            }

            $categorias = $query->get();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Categoria::$rules, Categoria::$messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoria = Categoria::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $categoria,
                'message' => 'Categoría creada exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $categoria = Categoria::with(['parent', 'subcategorias'])->find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $categoria
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'nombre' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|string|in:ingreso,gasto',
            'parent_id' => 'nullable|exists:categorias,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $categoria = Categoria::find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            // Validar que no se asigne como padre a sí misma
            if ($request->has('parent_id') && $request->parent_id == $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Una categoría no puede ser padre de sí misma'
                ], 422);
            }

            // Validar que no se convierta en padre de su propio padre
            if ($request->has('parent_id') && $categoria->isAncestorOf($request->parent_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No puede asignar una categoría descendiente como padre'
                ], 422);
            }

            $categoria->update($request->all());

            return response()->json([
                'success' => true,
                'data' => $categoria,
                'message' => 'Categoría actualizada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $categoria = Categoria::withCount(['subcategorias', 'detallesPresupuesto', 'ejecuciones', 'provisiones'])->find($id);

            if (!$categoria) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categoría no encontrada'
                ], 404);
            }

            // Verificar si tiene relaciones antes de eliminar
            if ($categoria->subcategorias_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene subcategorías asociadas'
                ], 409);
            }

            if (
                $categoria->detalles_presupuesto_count > 0 ||
                $categoria->ejecuciones_count > 0 ||
                $categoria->provisiones_count > 0
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene registros asociados'
                ], 409);
            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener categorías por tipo
     */
    public function byType($type)
    {
        try {
            $validator = Validator::make(['type' => $type], [
                'type' => 'required|in:ingreso,gasto'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de categoría no válido'
                ], 422);
            }

            $categorias = Categoria::where('tipo', $type)
                ->whereNull('parent_id')
                ->with('subcategorias')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $categorias
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías: ' . $e->getMessage()
            ], 500);
        }
    }

  public function tree($depth = 2)
{
    try {
        $query = Categoria::with('subcategorias')->whereNull('parent_id');

        if ($depth > 1) {
            $query->with(['subcategorias' => function ($q) use ($depth) {
                if ($depth > 2) {
                    $q->with(['subcategorias' => function ($q2) use ($depth) {
                        // Puedes seguir anidando según la profundidad necesaria
                    }]);
                }
            }]);
        }

        $categorias = $query->get();

        return response()->json([
            'success' => true,
            'data' => $categorias
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener el árbol de categorías: ' . $e->getMessage()
        ], 500);
    }
}
}
