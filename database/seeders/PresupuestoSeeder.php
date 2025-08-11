<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Presupuesto;
use App\Models\PresupuestoDetalle;
use App\Models\Unidad;
use Illuminate\Database\Seeder;

class PresupuestoSeeder extends Seeder
{
    public function run()
    {
        // 1. Obtener la unidad y categorías (asumiendo que ya existen)
        $unidad = Unidad::first();
        $presupuesto = Presupuesto::firstOrCreate([
            'unidad_id' => $unidad->id,
            'anio' => 2025
        ]);

        // 2. Limpiar detalles existentes para evitar duplicados
        PresupuestoDetalle::where('presupuesto_id', $presupuesto->id)->delete();

        $ingresosJonathan = [
            'Salario mensual' => 741.97,
            'Aguinaldo' => 36.75,
            'Vacaciones' => 132.30,
            'bonos' => 11.025,
            'Intereses mensuales' => 11.3583333333333
        ];

        $gastos = [
            // 1. Casa
            'Celular' => 32,
            'Prov. Mantenimiento y reparaciones' => 10,
            'Prov. Compra de artículos del hogar' => 10.4166666666667,
            
            // 2. Alimentación
            'Alimentos perecederos' => 60.00,
            'Limpieza del hogar / Higiene personal' => 6.67,
            'Compra de alimentación en el trabajo' => 60.00,
            
            // 3. Vehículos / Transporte
            'Bus / Taxi / Uber / CarWash / Parqueo' => 24.00,
            'Prov. Mantenimiento' => 10.04,
            
            // 5. Entretenimiento
            'Comidas fuera' => 35.00,
            'Paseos de fin de semana' => 25.00,
            'Prov. Vacaciones y Viajes' => 16.25,
            
            // 6. Ahorros
            'Emergencias e imprevistos' => 50.00,
            
            // 7. Deudas
            'Extrafinanciamientos/Compras a plazos' => 56.50,
            
            // 8. Ropa
            'Prov. Gasto Anual de Ropa' => 7.08,
            
            // 9. Salud
            'Consultas Médicas' => 3.75,
            'Medicinas' => 5.00,
            'Prov. Gastos Médicos' => 20.00,
            
            // 10. Educación
            'Prov. Matrícula' => 75.00,
            
            // 11. Misceláneos
            'Peluquería' => 7.00,
            'Suscripciones (Periódicos, Netflix, etc.)' => 18.34,
            'Donaciones / Ayuda Familiar/ Diezmo' => 75.00,
            'Otros' => 9.17,
            'Prov. Cosméticos y Salón de Belleza' => 3.75,
            'Prov. Regalos y Eventos' => 12.50,
            'Prov. Rep. Doc. (DUI/Pasaporte /Visa/etc.)' => 3.18,
            
            // 12. Inversiones
            'Ahorro programado' => 250.00
        ];

        // 3. Insertar ingresos
        foreach ($ingresosJonathan as $nombre => $monto) {
            $categoria = Categoria::where('nombre', 'like', "%$nombre%")->first();
            if ($categoria) {
                PresupuestoDetalle::updateOrCreate(
                    [
                        'presupuesto_id' => $presupuesto->id,
                        'categoria_id' => $categoria->id
                    ],
                    [
                        'monto_mensual' => $monto,
                        'monto_anual' => $monto * 12
                    ]
                );
            }
        }

        // 4. Insertar gastos
        foreach ($gastos as $nombre => $monto) {
            $categoria = Categoria::where('nombre', 'like', "%$nombre%")->first();
            if ($categoria) {
                PresupuestoDetalle::updateOrCreate(
                    [       
                        'presupuesto_id' => $presupuesto->id,
                        'categoria_id' => $categoria->id
                    ],
                    [
                        'monto_mensual' => $monto,
                        'monto_anual' => $monto * 12
                    ]
                );
            }
        }

        // 5. Resumen
        $totalIngresos = array_sum($ingresosJonathan);
        $totalGastos = array_sum($gastos);
        
        $this->command->info('Presupuesto real '. $presupuesto->anio .' creado:');
        $this->command->info("- Ingresos mensuales: $" . number_format($totalIngresos, 2));
        $this->command->info("- Gastos mensuales: $" . number_format($totalGastos, 2));
        $this->command->info("- Balance mensual: $" . number_format(($totalIngresos - $totalGastos), 2));
        $this->command->info("- Ingresos anuales: $" . number_format($totalIngresos * 12, 2));
        $this->command->info("- Gastos anuales: $" . number_format($totalGastos * 12, 2));
        $this->command->info("- Balance anual: $" . number_format(($totalIngresos - $totalGastos) * 12, 2));
    }
}