<?php

namespace App\Models\Maintenance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradoInstruccion extends Model
{
    use HasFactory;
    protected $table = 'grado_instruccion';

    protected $fillable = [

        'nombre',
        'nivel',
        'porcentaje',
        'domain_id'

    ];

    public function domain() {
        return $this->belongsTo(Domain::class, 'domain_id');
    }
}
