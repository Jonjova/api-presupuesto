<?php

namespace App\Imports;

use App\Models\ProvisionDistribuida;
use Maatwebsite\Excel\Concerns\ToModel;

class ProvisionImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new ProvisionDistribuida([
            //
        ]);
    }
}
