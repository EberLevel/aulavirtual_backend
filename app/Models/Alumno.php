<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $table = 'alumnos';
    
    protected $fillable = [
        'codigo', 
        'nombres', 
        'apellidos', 
        'celular', 
        'email', 
        'carrera_id', 
        'ciclo_id', 
        'dni', 
        'fecha_nacimiento', 
        'direccion', 
        'estado_id', 
        'promocion_id', 
        'foto_perfil', 
        'foto_carnet', 
        'domain_id',
        'estadoAlumno',
    ];
}

