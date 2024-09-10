<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoTarea extends Model
{
    use HasFactory;
    
    protected $table = 'proyecto_tarea';

    protected $fillable = [
        'nombre', 
        'prioridad',
        'estado',
        'grupo',
        'responsable'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'proyecto_id');
    }
}
