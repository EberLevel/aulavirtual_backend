<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\Alumno;
use App\Models\CursoAlumno;
use Illuminate\Support\Facades\Validator;

class CursoAlumnoController extends Controller
{

    public function index($alumno_id)
    {
        try {
            $courses = Curso::leftJoin('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
                ->leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')  // Join con la tabla ciclos
                ->leftJoin('area_de_formacion', 'area_de_formacion.id', '=', 'cursos.area_de_formacion_id')  // Join con la tabla area_de_formacion
                ->leftJoin('modulos_formativos', 'modulos_formativos.id', '=', 'cursos.modulo_formativo_id')  // Join con la tabla modulos_formativos
                ->leftJoin('t_g_parametros as estado', 'estado.nu_id_parametro', '=', 'cursos.estado_id')
                ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
                ->leftJoin('alumnos', 'alumnos.id', '=', 'curso_alumno.alumno_id')
                // Agregar filtro para estadoAlumno
                ->where('curso_alumno.alumno_id', $alumno_id)
                ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
                ->select(
                    'cursos.*',
                    'ciclos.nombre as ciclo_nombre',  // Obtiene el nombre del ciclo
                    'area_de_formacion.nombre as area_de_formacion_nombre',  // Obtiene el nombre del área de formación
                    'modulos_formativos.nombre as modulo_formativo_nombre',  // Obtiene el nombre del módulo formativo
                    'carreras.nombres as carrera_nombre',
                    'estado.tx_item_description as estado_nombre',
                    'alumnos.id as alumno_id',
                    'curso_alumno.estado_id as estado_id'
                )
                ->get();
    
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    

    public function indexByAlumno($alumno_id)
    {
        try {
            $courses = Curso::leftJoin('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
                ->leftJoin('ciclos', 'ciclos.id', '=', 'cursos.ciclo_id')  // Join con la tabla ciclos
                ->leftJoin('t_g_parametros as modulo_formativo', 'modulo_formativo.nu_id_parametro', '=', 'cursos.modulo_formativo_id')
                ->leftJoin('t_g_parametros as area_de_formacion', 'area_de_formacion.nu_id_parametro', '=', 'cursos.area_de_formacion_id')
                ->leftJoin('t_g_parametros as estado', 'estado.nu_id_parametro', '=', 'cursos.estado_id')
                ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
                ->leftJoin('alumnos', 'alumnos.id', '=', 'curso_alumno.alumno_id')
                // Agregar filtro para estadoAlumno
                ->where('curso_alumno.alumno_id', $alumno_id)
                ->where('alumnos.estadoAlumno', '!=', 'RETIRADO')
                ->where('curso_alumno.estado_id', 2)
                ->select(
                    'cursos.*',
                    'ciclos.nombre as ciclo_nombre',  // Obtiene el nombre del ciclo desde la tabla ciclos
                    'modulo_formativo.tx_item_description as modulo_formativo_nombre',
                    'area_de_formacion.tx_item_description as area_de_formacion_nombre',
                    'carreras.nombres as carrera_nombre',
                    'estado.tx_item_description as estado_nombre',
                    'alumnos.id as alumno_id',
                    'curso_alumno.estado_id as estado_id'
                )
                ->get();
    
            return response()->json($courses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateCursoEstado(Request $request)
    {
        $rules = [
            'cursoId' => 'required|integer',
            'estadoId' => 'required|integer',
            'alumnoId' => 'required|integer',
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $validatedData = $validator->validated();
    
        // Encuentra el curso alumno que corresponde
        $cursoAlumno = CursoAlumno::where('curso_id', $validatedData['cursoId'])
            ->where('alumno_id', $validatedData['alumnoId'])
            ->first();
    
        if ($cursoAlumno) {
            $cursoAlumno->estado_id = $validatedData['estadoId'];
            $cursoAlumno->save();
    
            return response()->json(['message' => 'Estado actualizado con éxito.'], 200);
        } else {
            return response()->json(['message' => 'Curso o alumno no encontrados.'], 404);
        }
    }
    
    



    // Método para asignar un curso a un alumno
    public function assign(Request $request)
    {
        try {
            $this->validate($request, [
                'curso_id' => 'required|integer|exists:cursos,id',
                'alumno_id' => 'required|integer|exists:alumnos,id',
            ]);

            $curso = Curso::findOrFail($request->curso_id);
            $curso->alumno_id = $request->alumno_id;
            $curso->save();

            return response()->json($curso, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
