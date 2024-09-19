<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Curso;

class CursoController extends Controller
{
    public function index($id)
    {
        $courses = Curso::leftJoin('t_g_parametros as ciclo', 'ciclo.nu_id_parametro', '=', 'cursos.ciclo_id')
            ->leftJoin('t_g_parametros as modulo_formativo', 'modulo_formativo.nu_id_parametro', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('t_g_parametros as area_de_formacion', 'area_de_formacion.nu_id_parametro', '=', 'cursos.area_de_formacion_id')
            ->leftJoin('t_g_parametros as estado', 'estado.nu_id_parametro', '=', 'cursos.estado_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->where('cursos.carrera_id', $id)
            ->select(
                'cursos.*',
                'ciclo.tx_item_description as ciclo_nombre',
                'modulo_formativo.tx_item_description as modulo_formativo_nombre',
                'area_de_formacion.tx_item_description as area_de_formacion_nombre',
                'carreras.nombres as carrera_nombre',
                'estado.tx_item_description as estado_nombre',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre'
            )
            ->get();

        return response()->json($courses);
    }
    public function getCursosByAlumno($alumnoId)
    {
        $courses = Curso::leftJoin('curso_alumno', 'curso_alumno.curso_id', '=', 'cursos.id')
            ->leftJoin('t_g_parametros as ciclo', 'ciclo.nu_id_parametro', '=', 'cursos.ciclo_id')
            ->leftJoin('t_g_parametros as modulo_formativo', 'modulo_formativo.nu_id_parametro', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('t_g_parametros as area_de_formacion', 'area_de_formacion.nu_id_parametro', '=', 'cursos.area_de_formacion_id')
            ->leftJoin('t_g_parametros as estado', 'estado.nu_id_parametro', '=', 'cursos.estado_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->where('curso_alumno.alumno_id', $alumnoId)
            ->select(
                'cursos.*',
                'ciclo.tx_item_description as ciclo_nombre',
                'modulo_formativo.tx_item_description as modulo_formativo_nombre',
                'area_de_formacion.tx_item_description as area_de_formacion_nombre',
                'carreras.nombres as carrera_nombre',
                'estado.tx_item_description as estado_nombre',
                'docentes.id as docente_id'
            )
            ->get();

        return response()->json($courses);
    }

    public function getCursosByDomain($domainId)
    {
        $courses = Curso::leftJoin('t_g_parametros as ciclo', 'ciclo.nu_id_parametro', '=', 'cursos.ciclo_id')
            ->leftJoin('t_g_parametros as modulo_formativo', 'modulo_formativo.nu_id_parametro', '=', 'cursos.modulo_formativo_id')
            ->leftJoin('t_g_parametros as area_de_formacion', 'area_de_formacion.nu_id_parametro', '=', 'cursos.area_de_formacion_id')
            ->leftJoin('t_g_parametros as estado', 'estado.nu_id_parametro', '=', 'cursos.estado_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'cursos.carrera_id')
            ->leftJoin('docentes', 'docentes.id', '=', 'cursos.docente_id')
            ->where('cursos.domain_id', $domainId)
            ->select(
                'cursos.*',
                'ciclo.tx_item_description as ciclo_nombre',
                'modulo_formativo.tx_item_description as modulo_formativo_nombre',
                'area_de_formacion.tx_item_description as area_de_formacion_nombre',
                'carreras.nombres as carrera_nombre',
                'estado.tx_item_description as estado_nombre',
                'docentes.id as docente_id',
                'docentes.nombres as docente_nombre'
            )
            ->get();

        return response()->json($courses);
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombreCurso' => 'required|string|max:255',
            'cicloId' => 'required|integer',
            'areaFormacionId' => 'required|integer',
            'moduloFormativoId' => 'required|integer',
            'cantidadCreditos' => 'required|integer',
            'porcentajeCreditos' => 'required|integer',
            'cantidadHoras' => 'required|integer',
            'carreraId' => 'required|integer',
            'syllabus' => 'required|string',
            'tema' => 'required|string',
            'estadoId' => 'required|integer',
            'domain_id' => 'required',
            // 'asignacionDocentesId' => 'required', // Elimina esta línea para que no sea requerido
        ]);

        $curso = Curso::create([
            'codigo' => $request->codigo,
            'nombre' => $request->nombreCurso,
            'ciclo_id' => $request->cicloId,
            'area_de_formacion_id' => $request->areaFormacionId,
            'modulo_formativo_id' => $request->moduloFormativoId,
            'cantidad_de_creditos' => $request->cantidadCreditos,
            'porcentaje_de_creditos' => $request->porcentajeCreditos,
            'cantidad_de_horas' => $request->cantidadHoras,
            'carrera_id' => $request->carreraId,
            'syllabus' => $request->syllabus,
            'tema' => $request->tema,
            'estado_id' => $request->estadoId,
            'domain_id' => $request->domain_id,
            'docente_id' => is_array($request->asignacionDocentesId) ? null : $request->asignacionDocentesId,
        ]);

        return response()->json($curso, 201);
    }


    public function show($id)
    {
        $course = Curso::find($id);
        if (!$course) {
            return response()->json(['Error' => 'Curso no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $course], 200);
    }

    public function getSyllabus($id)
    {
        $course = Curso::find($id, ['id', 'syllabus']);
        if (!$course) {
            return response()->json(['Error' => 'Curso no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $course], 200);
    }

    public function getTema($id)
    {
        $course = Curso::find($id, ['id', 'tema']);
        if (!$course) {
            return response()->json(['Error' => 'Curso no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $course], 200);
    }

    public function update(Request $request, $id)
    {

        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombreCurso' => 'required|string|max:255',
            'cicloId' => 'required|integer',
            'areaFormacionId' => 'required|integer',
            'moduloFormativoId' => 'required|integer',
            'cantidadCreditos' => 'required|integer',
            'porcentajeCreditos' => 'required|integer',
            'cantidadHoras' => 'required|integer',
            'carreraId' => 'required|integer',
            'syllabus' => 'required|string',
            'tema' => 'required|string',
            'estadoId' => 'required|integer',
            'domain_id' => 'required',
        ]);

        $curso = Curso::findOrFail($id);
        $curso->update([
            'codigo' => $request->codigo,
            'nombre' => $request->nombreCurso,
            'ciclo_id' => $request->cicloId,
            'area_de_formacion_id' => $request->areaFormacionId,
            'modulo_formativo_id' => $request->moduloFormativoId,
            'cantidad_de_creditos' => $request->cantidadCreditos,
            'porcentaje_de_creditos' => $request->porcentajeCreditos,
            'cantidad_de_horas' => $request->cantidadHoras,
            'carrera_id' => $request->carreraId,
            'syllabus' => $request->syllabus,
            'tema' => $request->tema,
            'estado_id' => $request->estadoId,
            'domain_id' => $request->domain_id,
        ]);

        return response()->json($curso, 200);
    }

    public function destroy($id)
    {
        // Busca el curso por su ID
        $curso = Curso::find($id);

        // Verifica si el curso existe
        if (!$curso) {
            return response()->json([
                'message' => 'Curso no encontrado'
            ], 404);
        }

        // Elimina el curso
        $curso->delete();

        // Devuelve una respuesta de éxito
        return response()->json([
            'message' => 'Curso eliminado exitosamente'
        ], 200);
    }
}
