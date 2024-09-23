<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipanteController extends Controller
{
    public function show($domain_id, $curso_id)
    {
        $participantes = DB::table('alumnos as a')
            ->select(
                'a.codigo',
                'a.estadoAlumno',
                'a.id',
                'ca.id as curso_alumno_id',
                DB::raw("concat(a.nombres, ' ', a.apellidos) as nombres"),
                'a.celular',
                'a.email',
                'a.dni'
            )
            ->leftJoin('curso_alumno as ca', function ($join) use ($curso_id, $domain_id) {
                $join->on('ca.alumno_id', '=', 'a.id')
                    ->where('ca.curso_id', $curso_id)
                    ->where('ca.domain_id', $domain_id);
            })
            ->where('a.domain_id', $domain_id)  // Alumnos del dominio actual
            ->get();
    
        return response()->json($participantes);
    }
    
    


    public function store(Request $request)
    {
        $domainId = $request->input('domain_id');
        $cursoId = $request->input('curso_id');
        $alumnoId = $request->input('alumno_id');
        //verificar si el alumno ya esta registrado en el curso
        $cursoAlumno = DB::table('curso_alumno')
            ->where('curso_id', $cursoId)
            ->where('alumno_id', $alumnoId)
            ->where('domain_id', $domainId)
            ->first();
        if ($cursoAlumno) {
            //remover el registro
            DB::table('curso_alumno')->where('id', $cursoAlumno->id)->delete();
        } else {
            //insertar el registro
            DB::table('curso_alumno')->insert([
                'curso_id' => $cursoId,
                'alumno_id' => $alumnoId,
                'domain_id' => $domainId
            ]);
        }
    }

        // Método para obtener la suma de las notas de un alumno en un curso
        public function getSumaNotas($curso_id, $alumno_id)
        {
            $sumaNotas = DB::table('evaluaciones_alumno as ea')
                ->join('evaluaciones as e', 'ea.evaluacion_id', '=', 'e.id')
                ->join('grupo_de_evaluaciones as ge', 'e.grupo_de_evaluaciones_id', '=', 'ge.id')
                ->join('cursos as c', 'ge.curso_id', '=', 'c.id')
                ->where('c.id', $curso_id)
                ->where('ea.alumno_id', $alumno_id)
                ->sum('ea.nota');
    
            return response()->json([
                'curso_id' => $curso_id,
                'alumno_id' => $alumno_id,
                'suma_notas' => $sumaNotas
            ]);
        }
}
