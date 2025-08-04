<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriasEgresosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();
        
        $grupos = [
            // 1. Casa
            [
                'nombre' => 'Casa',
                'subcategorias' => [
                    'Cuota: Préstamo',
                    'Cuota: Alquiler',
                    'Vigilancia',
                    'Alcaldía',
                    'Electricidad',
                    'Agua potable',
                    'Agua - Embotellada o Filtro',
                    'Gas propano',
                    'Celular',
                    'Internet / Fijo / Cable',
                    'Servicio doméstico',
                    'Jardinero',
                    'Otros',
                    'Prov. Mantenimiento y reparaciones',
                    'Prov. Compra de artículos del hogar',
                    'Prov. Aguinaldos de empleados'
                ]
            ],
            
            // 2. Alimentación
            [
                'nombre' => 'Alimentación',
                'subcategorias' => [
                    'Alimentos perecederos',
                    'Frutas / Verduras',
                    'Abarrotes',
                    'Limpieza del hogar / Higiene personal',
                    'Compra de alimentación en el trabajo',
                    'Otros'
                ]
            ],
            
            // 3. Vehículos / Transporte
            [
                'nombre' => 'Vehículos / Transporte',
                'subcategorias' => [
                    'Cuota(s) de préstamo de vehículo',
                    'Combustible',
                    'Bus / Taxi / Uber / CarWash / Parqueo',
                    'Transporte escolar',
                    'Prov. Mantenimiento',
                    'Prov. Tarjeta de Circulación / Placas'
                ]
            ],
            
            // 4. Seguros
            [
                'nombre' => 'Seguros',
                'subcategorias' => [
                    'Gastos médicos',
                    'Vida - Protección de Ingresos',
                    'Vida - Deuda',
                    'Daños residenciales',
                    'Vehículos',
                    'Prov. Prima'
                ]
            ],
            
            // 5. Entretenimiento
            [
                'nombre' => 'Entretenimiento',
                'subcategorias' => [
                    'Comidas fuera',
                    'Paseos de fin de semana',
                    'Cenas o reuniones en casa',
                    'Prov. Vacaciones y Viajes'
                ]
            ],
            
            // 6. Ahorros
            [
                'nombre' => 'Ahorros',
                'subcategorias' => [
                    'Emergencias e imprevistos'
                ]
            ],
            
            // 7. Deudas
            [
                'nombre' => 'Deudas',
                'subcategorias' => [
                    'Préstamos personales',
                    'Tarjetas de crédito',
                    'Extrafinanciamientos/Compras a plazos',
                    'Otros',
                    'Pagos anticipados a deudas'
                ]
            ],
            
            // 8. Ropa
            [
                'nombre' => 'Ropa',
                'subcategorias' => [
                    'Compra y reparación de ropa',
                    'Prov. Gasto Anual de Ropa'
                ]
            ],
            
            // 9. Salud
            [
                'nombre' => 'Salud',
                'subcategorias' => [
                    'Consultas Médicas',
                    'Dentistas / Ortodoncista',
                    'Medicinas',
                    'Exámenes/Vacunas',
                    'Prov. Gastos Médicos'
                ]
            ],
            
            // 10. Educación
            [
                'nombre' => 'Educación',
                'subcategorias' => [
                    'Colegiatura mensual',
                    'Materiales (Tareas)',
                    'Prov. Matrícula',
                    'Prov. Útiles (Libros, cuadernos, etc.)',
                    'Prov. Uniformes'
                ]
            ],
            
            // 11. Misceláneos
            [
                'nombre' => 'Misceláneos',
                'subcategorias' => [
                    'Dry cleaning',
                    'Salón de belleza',
                    'Peluquería',
                    'Suscripciones (Periódicos, Netflix, etc.)',
                    'Mascotas',
                    'Membresías: Club social / Gimnasio / Otros',
                    'Extracurriculares hijos',
                    'Donaciones / Ayuda Familiar/ Diezmo',
                    'Otros',
                    'Prov. Cosméticos y Salón de Belleza',
                    'Prov. Regalos y Eventos',
                    'Prov. Rep. Doc. (DUI/Pasaporte /Visa/etc.)',
                    'Prov. Ajuste de Impuesto sobre la Renta (ISR)'
                ]
            ],
            
            // 12. Inversiones
            [
                'nombre' => 'Inversiones',
                'subcategorias' => [
                    'Retiro',
                    'Estudios',
                    'Ahorro programado',
                    'Otros'
                ]
            ]
        ];

        foreach ($grupos as $grupo) {
            // Insertar categoría principal (grupo)
            $parentId = Categoria::insertGetId([
                'nombre' => $grupo['nombre'],
                'tipo' => 'gasto',
                'parent_id' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ]);

            // Insertar subcategorías
            foreach ($grupo['subcategorias'] as $subcategoria) {
                Categoria::insert([
                    'nombre' => $subcategoria,
                    'tipo' => 'gasto',
                    'parent_id' => $parentId,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp
                ]);
            }
        }
    }
}