<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Alumno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    use UserTrait;

    // Obtener todos los alumnos por dominio
    public function index($dominio)
    {
        $alumnos = Alumno::leftJoin('ciclos', 'ciclos.id', '=', 'alumnos.ciclo_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'alumnos.carrera_id')
            ->leftJoin('domains', 'domains.id', '=', 'alumnos.domain_id')
            ->select(
                'alumnos.*',
                'ciclos.nombre as ciclo_nombre',
                'carreras.nombres as carrera_nombre',
                'domains.nombre as institucion'
            )
            ->whereNull('alumnos.deleted_at')
            ->where('alumnos.domain_id', $dominio)
            ->get();

        return response()->json($alumnos);
    }

    // Crear un nuevo alumno
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $this->validate($request, [
                'codigo' => 'required|string|max:255',
                'nombres' => 'required|string|max:255',
                'apellidos' => 'required|string|max:255',
                'cicloId' => 'required|integer',
                'carreraId' => 'required|integer',
                'promocion_id' => 'required|integer'
            ]);

            $promocion = Promocion::find($request->input('promocion_id'));

            if (!$promocion) {
                return response()->json(['message' => 'Promoción no encontrada'], 400);
            }

            $alumno = [
                "codigo" => $request->input('codigo'),
                "nombres" => $request->input('nombres'),
                "apellidos" => $request->input('apellidos'),
                "celular" => $request->input('nroCelular'),
                "email" => $request->input('email'),
                "carrera_id" => $request->input('carreraId'),
                "ciclo_id" => $request->input('cicloId'),
                "dni" => $request->input('numeroDocumento'),
                "genero" => "masculino",
                "fecha_nacimiento" => $request->input('fechaNacimiento') ?? date('Y-m-d'),
                "direccion" => $request->input('direccion'),
                "domain_id" => $request->input('domain_id'),
                "promocion_id" => $promocion->id,
                "foto_perfil" => $request->input('fotoPerfil') ?? 'default_profile_picture.jpg',
                "foto_carnet" => $request->input('fotoCarnet') ?? 'default_carnet_picture.jpg'
            ];

            $alumnoId = DB::table('alumnos')->insertGetId($alumno);

            DB::commit();

            return response()->json(['alumno_id' => $alumnoId, 'message' => 'Alumno creado correctamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Actualizar un alumno existente
    public function update(Request $request, $id, $domain_id)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'cicloId' => 'required|integer',
            'carreraId' => 'required|integer',
            'dni' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'nroCelular' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'fechaNacimiento' => 'required|date',
            'promocionId' => 'required|integer',
        ]);

        $alumno = Alumno::where('id', $id)
                        ->where('domain_id', $domain_id)
                        ->first();

        if ($alumno) {
            $alumno->update($request->all());
            return response()->json($alumno, 200);
        }

        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }

    // Eliminar un alumno
    public function destroy($id, $dominio)
    {
        $alumno = Alumno::where('id', $id)->where('domain_id', $dominio)->first();
        if ($alumno) {
            $alumno->delete();
            DB::table('users')->where('email', $alumno->email)->delete();
            return response()->json('Record deleted', 201);
        }
        return response()->json('Record not found', 404);
    }

    // Mostrar un alumno por ID y dominio
    public function show($id, $dominio)
    {
        $alumno = Alumno::where('id', $id)->where('domain_id', $dominio)->first();
    
        if ($alumno) {
            return response()->json($alumno, 200);
        }
    
        return response()->json(['message' => 'Alumno no encontrado'], 404);
    }

    // Obtener el alumno logueado
    public function getLoggedAlumno($alumno_id, $dominio)
    {
        $alumno = Alumno::leftJoin('ciclos', 'ciclos.id', '=', 'alumnos.ciclo_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'alumnos.carrera_id')
            ->leftJoin('domains', 'domains.id', '=', 'alumnos.domain_id')
            ->select(
                'alumnos.*',
                'ciclos.nombre as ciclo_nombre',
                'carreras.nombres as carrera_nombre',
                'domains.nombre as institucion'
            )
            ->where('alumnos.id', $alumno_id)
            ->where('alumnos.domain_id', $dominio)
            ->whereNull('alumnos.deleted_at')
            ->first();

        if ($alumno) {
            return response()->json($alumno);
        }

        return response()->json('Alumno no encontrado', 404);
    }
}
