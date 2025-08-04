<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriasIngresosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $timestamp = now();
        $grupos = [
            // 1. Salario
            [
                'nombre' => 'Salario mensual',
                'subcategorias' => []
            ],

            // 2. Comisiones
            [
                'nombre' => 'Comisiones',
                'subcategorias' => [
                    'Aguinaldo',
                    'Vacaciones',
                    'Bonos',
                ]
            ],

            // 3. Servicios profesionales
            [
                'nombre' => 'Servicios profesionales',
                'subcategorias' => [
                    'Consultorías',
                    'Freelance',
                    'Honorarios profesionales'
                ]
            ],
            // 4. Inversiones
            [
                'nombre' => 'Inversiones',
                'subcategorias' => [
                    'Dividendos',
                    'Alquileres',
                    'Intereses mensuales',
                    'Intereses no mensuales'
                ]
            ],
            // 5. Otros ingresos
            [
                'nombre' => 'Otros ingresos',
                'subcategorias' => [
                    'Otros ingresos'
                ]
            ]
        ];

      foreach ($grupos as $grupo) {
            // Insertar categoría principal (grupo)
            $parentId = Categoria::insertGetId([
                'nombre' => $grupo['nombre'],
                'tipo' => 'ingreso',
                'parent_id' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);

            // Insertar subcategorías
            foreach ($grupo['subcategorias'] as $subcategoria) {
                Categoria::insert([
                    'nombre' => $subcategoria,
                    'tipo' => 'ingreso',
                    'parent_id' => $parentId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }

    }
}
