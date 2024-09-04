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

        // Convertir fotos a base64
        foreach ($alumnos as $alumno) {
            if ($alumno->foto_perfil) {
                $alumno->foto_perfil = 'data:image/jpeg;base64,' . $alumno->foto_perfil;
            }
            if ($alumno->foto_carnet) {
                $alumno->foto_carnet = 'data:image/jpeg;base64,' . $alumno->foto_carnet;
            }
        }

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
                'promocion_id' => 'required|integer',
                'email' => 'required|email|max:255',
                'contraseña' => 'required|string|min:6' // Aseguramos que se envíe la contraseña
            ]);

            $promocion = Promocion::find($request->input('promocion_id'));

            if (!$promocion) {
                return response()->json(['message' => 'Promoción no encontrada'], 400);
            }

            // Procesar imágenes en base64 si están presentes
            $fotoPerfil = $request->input('fotoPerfil') ? $request->input('fotoPerfil') : null;
            $fotoCarnet = $request->input('fotoCarnet') ? $request->input('fotoCarnet') : null;

            $alumno = [
                "codigo" => $request->input('codigo'),
                "nombres" => $request->input('nombres'),
                "apellidos" => $request->input('apellidos'),
                "celular" => $request->input('nroCelular'),
                "email" => $request->input('email'),
                "carrera_id" => $request->input('carreraId'),
                "ciclo_id" => $request->input('cicloId'),
                "dni" => $request->input('numeroDocumento'),
                "fecha_nacimiento" => $request->input('fechaNacimiento') ?? date('Y-m-d'),
                "direccion" => $request->input('direccion'),
                "domain_id" => $request->input('domain_id'),
                "promocion_id" => $promocion->id,
                "foto_perfil" => $fotoPerfil,
                "foto_carnet" => $fotoCarnet
            ];

            // Insertar el alumno en la base de datos y obtener el ID del alumno
            $alumnoId = DB::table('alumnos')->insertGetId($alumno);

            // Crear el usuario correspondiente en la tabla 'users'
            DB::table('users')->insert([
                'alumno_id' => $alumnoId, // Relacionamos el usuario con el ID del alumno
                'name' => $request->input('nombres'),
                'lastname' => $request->input('apellidos'),
                'email' => $request->input('email'),
                'dni' => $request->input('numeroDocumento'),
                'password' => Hash::make($request->input('contraseña')), // Hasheamos la contraseña
                'created_at' => Carbon::now(), // Usamos Carbon::now()
                'updated_at' => Carbon::now(), // Usamos Carbon::now()
            ]);

            DB::commit();

            return response()->json(['alumno_id' => $alumnoId, 'message' => 'Alumno y usuario creados correctamente'], 201);
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
            'domain_id' => 'required|integer',
        ]);

        $alumno = Alumno::where('id', $id)
            ->where('domain_id', $domain_id)
            ->first();

        if ($alumno) {
            // Procesar las imágenes si están presentes
            $fotoPerfil = $request->input('fotoPerfil') ? $request->input('fotoPerfil') : $alumno->foto_perfil;
            $fotoCarnet = $request->input('fotoCarnet') ? $request->input('fotoCarnet') : $alumno->foto_carnet;

            // Actualizar los datos del alumno
            $alumno->update([
                "codigo" => $request->input('codigo'),
                "nombres" => $request->input('nombres'),
                "apellidos" => $request->input('apellidos'),
                "celular" => $request->input('nroCelular'),
                "email" => $request->input('email'),
                "carrera_id" => $request->input('carreraId'),
                "ciclo_id" => $request->input('cicloId'),
                "dni" => $request->input('dni'),
                "genero" => $request->input('genero'),
                "fecha_nacimiento" => $request->input('fechaNacimiento'),
                "direccion" => $request->input('direccion'),
                "foto_perfil" => $fotoPerfil,
                "foto_carnet" => $fotoCarnet,
            ]);

            // Actualizar el usuario correspondiente en la tabla users
            $user = DB::table('users')->where('alumno_id', $id)->first();

            if ($user) {
                DB::table('users')->where('id', $user->id)->update([
                    'name' => $request->input('nombres'),
                    'lastname' => $request->input('apellidos'),
                    'email' => $request->input('email'),
                    'dni' => $request->input('dni'),
                    'password' => Hash::make($request->input('contraseña')),  // Hashear la contraseña
                ]);
            }

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
            // Convertir imágenes a base64 si existen
            if ($alumno->foto_perfil) {
                $alumno->foto_perfil = 'data:image/jpeg;base64,' . $alumno->foto_perfil;
            }
            if ($alumno->foto_carnet) {
                $alumno->foto_carnet = 'data:image/jpeg;base64,' . $alumno->foto_carnet;
            }

            return response()->json($alumno);
        }

        return response()->json('Alumno no encontrado', 404);
    }
}
