<?php

namespace App\Imports;

use App\Models\PresupuestoDetalle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PresupuestoImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // asume columnas: unidad, categoria, monto_anual
        return new PresupuestoDetalle([
            'presupuesto_id' => $this->getPresupuestoId($row['unidad'], $row['año']),
            'categoria_id'   => $this->getCategoriaId($row['categoria']),
            'monto_anual'    => $row['monto_anual'],
        ]);
    }
    // métodos auxiliares para resolver IDs...
    protected function getPresupuestoId($unidad, $año)
    {
        // lógica para obtener el ID del presupuesto basado en unidad y año
        return 1; // placeholder
    }
    protected function getCategoriaId($categoria)
    {
        // lógica para obtener el ID de la categoría
        return 1; // placeholder
    }
}

