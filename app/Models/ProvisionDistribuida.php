<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvisionDistribuida extends Model
{
    use HasFactory;
    protected $table = 'provision_distribuidas';
    protected $fillable = ['provision_id', 'anio', 'mes', 'monto'];

    public function provision()
    {
        return $this->belongsTo(Provision::class);
    }
}
