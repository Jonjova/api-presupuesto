<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categoria;
use Illuminate\Support\Facades\DB;
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

            return response()->json(
                [
                    'success' => true,
                    'data' => $categorias,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al obtener las categorías: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), Categoria::$rules, Categoria::$messages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            // Crear categoría principal
            $categoria = Categoria::create([
                'nombre' => $request->nombre,
                'tipo' => $request->tipo,
                'parent_id' => $request->parent_id,
            ]);

            // Crear subcategorías si existen
            if ($request->has('subcategorias')) {
                $subcategoriasData = [];

                foreach ($request->subcategorias as $subcategoria) {
                    $subcategoriasData[] = new Categoria([
                        'nombre' => $subcategoria['nombre'],
                        'tipo' => $subcategoria['tipo'],
                        'parent_id' => $categoria->id,
                    ]);
                }

                // Guardar todas las subcategorías de una vez
                $categoria->subcategorias()->saveMany($subcategoriasData);
            }

            DB::commit();

            // FORZAR la recarga de la relación con las subcategorías
            $categoria->load('subcategorias');

            return response()->json(
                [
                    'success' => true,
                    'data' => $categoria,
                    'message' => $request->has('subcategorias') ? 'Categoría con subcategorías creadas exitosamente' : 'Categoría creada exitosamente',
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al crear la categoría: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $categoria = Categoria::with('subcategorias')->find($id);

            if (!$categoria) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Categoría no encontrada',
                    ],
                    404,
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $categoria,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al obtener la categoría: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'nombre' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|string|in:ingreso,gasto',
            'parent_id' => [
                'nullable',
                'exists:categorias,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value == $id) {
                        $fail('Una categoría no puede ser padre de sí misma');
                    }
                    // Verificar jerarquía circular
                    $potentialParent = Categoria::find($value);
                    while ($potentialParent) {
                        if ($potentialParent->id == $id) {
                            $fail('No puede asignar una categoría descendiente como padre');
                            break;
                        }
                        $potentialParent = $potentialParent->parent;
                    }
                },
            ],
            'subcategorias' => 'sometimes|array',
            'subcategorias.*.id' => 'sometimes|required_with:subcategorias|exists:categorias,id,parent_id,' . $id,
            'subcategorias.*.nombre' => 'required_with:subcategorias|string|max:255',
            'subcategorias.*.tipo' => 'required_with:subcategorias|string|in:ingreso,gasto',
        ];

        $messages = [
            'subcategorias.*.id.exists' => 'La subcategoría no pertenece a esta categoría padre',
            'subcategorias.*.nombre.required_with' => 'Cada subcategoría debe tener un nombre',
            'subcategorias.*.tipo.in' => 'El tipo de cada subcategoría debe ser "ingreso" o "gasto"',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        try {
            DB::beginTransaction();

            // 1. Actualizar la categoría principal
            $categoria = Categoria::findOrFail($id);
            $categoria->update($request->only(['nombre', 'tipo', 'parent_id']));

            // 2. Procesar subcategorías si vienen en el request
            if ($request->has('subcategorias')) {
                foreach ($request->subcategorias as $subcategoriaData) {
                    if (isset($subcategoriaData['id'])) {
                        // 2.1. Actualizar subcategoría existente
                        $subcategoria = Categoria::where('id', $subcategoriaData['id'])->where('parent_id', $id)->first();

                        if ($subcategoria) {
                            $subcategoria->update([
                                'nombre' => $subcategoriaData['nombre'],
                                'tipo' => $subcategoriaData['tipo'],
                            ]);
                        }
                    } else {
                        // 2.2. Crear nueva subcategoría
                        Categoria::create([
                            'nombre' => $subcategoriaData['nombre'],
                            'tipo' => $subcategoriaData['tipo'],
                            'parent_id' => $id,
                        ]);
                    }
                }

                // IMPORTANTE: No eliminamos subcategorías no incluidas
            }

            DB::commit();

            // 3. Cargar la categoría con sus relaciones actualizadas
            $categoria->refresh();
            $categoria->load('subcategorias');

            return response()->json(
                [
                    'success' => true,
                    'data' => $categoria,
                    'message' => 'Categoría y subcategorías actualizadas exitosamente',
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al actualizar: ' . $e->getMessage(),
                ],
                500,
            );
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
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Categoría no encontrada',
                    ],
                    404,
                );
            }

            // Verificar si tiene relaciones antes de eliminar
            if ($categoria->subcategorias_count > 0) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se puede eliminar la categoría porque tiene subcategorías asociadas',
                    ],
                    409,
                );
            }

            if ($categoria->detalles_presupuesto_count > 0 || $categoria->ejecuciones_count > 0 || $categoria->provisiones_count > 0) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se puede eliminar la categoría porque tiene registros asociados',
                    ],
                    409,
                );
            }

            $categoria->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Categoría eliminada exitosamente',
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al eliminar la categoría: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Obtener categorías por tipo
     */
    public function byType($type)
    {
        try {
            $validator = Validator::make(
                ['type' => $type],
                [
                    'type' => 'required|in:ingreso,gasto',
                ],
            );

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Tipo de categoría no válido',
                    ],
                    422,
                );
            }

            $categorias = Categoria::where('tipo', $type)->whereNull('parent_id')->with('subcategorias')->get();

            return response()->json(
                [
                    'success' => true,
                    'data' => $categorias,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al obtener las categorías: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function tree($depth = 1)
    {
        try {
            // Construir la consulta con eager loading dinámico
            $query = Categoria::with($this->buildDepthRelationships($depth))->whereNull('parent_id')->orderBy('nombre');

            $categorias = $query->get();

            // Si no hay categorías principales
            if ($categorias->isEmpty()) {
                return response()->json(
                    [
                        'success' => true,
                        'data' => [],
                        'message' => 'No se encontraron categorías principales',
                    ],
                    200,
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'data' => $categorias,
                ],
                200,
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Error al obtener el árbol de categorías: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // Método auxiliar para construir relaciones de profundidad dinámica
    protected function buildDepthRelationships($depth, $currentLevel = 1)
    {
        if ($currentLevel >= $depth) {
            return 'subcategorias';
        }

        return [
            'subcategorias' => function ($query) use ($depth, $currentLevel) {
                $query->with($this->buildDepthRelationships($depth, $currentLevel + 1))->orderBy('nombre');
            },
        ];
    }
}
