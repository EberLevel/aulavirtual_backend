<?php

namespace App\Http\Controllers;
use App\Models\Promocion;
use App\Models\Alumno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\UserTrait;
//use hash;
use Illuminate\Support\Facades\Hash;

class AlumnoController extends Controller
{
    use UserTrait;
    public function index($dominio)
    {
        $alumnos = Alumno::leftJoin('t_g_parametros as ciclo', 'ciclo.nu_id_parametro', '=', 'alumnos.ciclo_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'alumnos.carrera_id')
            ->leftJoin('domains', 'domains.id', '=', 'alumnos.domain_id')
            ->select(
                'alumnos.*',
                'ciclo.tx_abreviatura as ciclo_nombre',
                'carreras.nombres as carrera_nombre',
                'domains.nombre as institucion'
            )->whereNull('alumnos.deleted_at')->where('alumnos.domain_id', $dominio)->get();
        return response()->json($alumnos);
    }
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
                'tipoDocumento' => 'required|integer|max:255',
            ]);

            // Obtener la promoción dependiendo de la fecha actual
            $fechaActual = Carbon::now();
            $promocion = null;

            if ($fechaActual->month <= 6) {
                // Primer semestre del año, asignar la promoción "2024-I"
                $promocion = Promocion::where('nombre_promocion', '2024-I')->first();
            } else {
                // Segundo semestre del año, asignar la promoción "2024-II"
                $promocion = Promocion::where('nombre_promocion', '2024-II')->first();
            }

            if (!$promocion) {
                // Si no existe la promoción, podrías crearla o devolver un error
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
                "promocion_id" => $promocion->id  ,
                "foto_perfil" => $request->input('fotoPerfil') ?? 'default_profile_picture.jpg',
                "foto_carnet" => $request->input('fotoCarnet') ?? 'default_carnet_picture.jpg' 
            ];

            // Guardar el alumno
            $alumnoId = DB::table('alumnos')->insertGetId($alumno);

            DB::commit();

            return response()->json(['alumno_id' => $alumnoId, 'message' => 'Alumno creado correctamente'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'cicloId' => 'required|integer',
            'carreraId' => 'required|integer',
            'dni' => 'required|string|max:255',
        ]);
        $dominio = $request->input('dominio');
        $alumno = Alumno::where('id', $id)->where('dominio', $dominio)->first();
        if ($alumno) {
            $alumno->update($request->all());
            return response()->json($alumno, 201);
        }
        return response()->json('Record not found', 404);
    }
    public function destroy($id, $dominio)
    {
        $alumno = Alumno::where('id', $id)->where('domain_id', $dominio)->first();
        if ($alumno) {
            $alumno->delete();
            $user = DB::table('users')->where('email', $alumno->email)->delete();
            return response()->json('Record deleted', 201);
        }
        return response()->json('Record not found', 404);
    }
    public function show($id, $dominio)
    {
        $carrera = Alumno::where('id', $id)->where('domain_id', $dominio)->first();
        if ($carrera) {
            return response()->json($carrera);
        }
        return response()->json('Record not found', 404);
    }
    public function getLoggedAlumno($alumno_id, $dominio) {
        $alumno = Alumno::leftJoin('t_g_parametros as ciclo', 'ciclo.nu_id_parametro', '=', 'alumnos.ciclo_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'alumnos.carrera_id')
            ->leftJoin('domains', 'domains.id', '=', 'alumnos.domain_id')
            ->select(
                'alumnos.*',
                'ciclo.tx_abreviatura as ciclo_nombre',
                'carreras.nombres as carrera_nombre',
                'domains.nombre as institucion'
            )
            ->where('alumnos.id', $alumno_id) // Solo el alumno logueado
            ->where('alumnos.domain_id', $dominio) // Asegúrate de que pertenezca al dominio correcto
            ->whereNull('alumnos.deleted_at') // Excluye registros eliminados
            ->first(); // Solo un registro
                
        if ($alumno) {
            return response()->json($alumno);
        }
    
        return response()->json('Alumno no encontrado', 404);
    }
}
