<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\Alumno;
use App\Models\PagoAlumno;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends Controller
{
    use UserTrait;

    // Obtener todos los alumnos por dominio
    public function index($dominio)
    {
        $alumnos = Alumno::leftJoin('ciclos', 'ciclos.id', '=', 'alumnos.ciclo_id')
            ->leftJoin('carreras', 'carreras.id', '=', 'alumnos.carrera_id')
            ->leftJoin('domains', 'domains.id', '=', 'alumnos.domain_id')
            ->leftJoin('plan_de_estudios', 'plan_de_estudios.id', '=', 'alumnos.estado_id')
            ->leftJoin('promociones', 'promociones.id', '=', 'alumnos.promocion_id')
            ->select(
                'alumnos.*',
                'ciclos.nombre as ciclo_nombre',
                'carreras.nombres as carrera_nombre',
                'domains.nombre as institucion',
                'plan_de_estudios.nombre as estado_nombre',
                'promociones.nombre_promocion as promocion_nombre'
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
                'domain_id' => 'required|integer',
                'estadoId' => 'required|integer',
                'email' => 'required|email|max:255',
                'contraseña' => 'required|string|min:6'
            ]);
    
            $promocion = Promocion::find($request->input('promocion_id'));
    
            if (!$promocion) {
                return response()->json(['message' => 'Promoción no encontrada'], 400);
            }
    
            // Procesar imágenes en base64 si están presentes
            $fotoPerfil = $request->input('fotoPerfil');
            $fotoCarnet = $request->input('fotoCarnet');
    
            // Crear el alumno
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
                "estado_id" => $request->input('estadoId'),
                "foto_perfil" => $fotoPerfil ?? null, // Maneja null si no se envía
                "foto_carnet" => $fotoCarnet ?? null  // Maneja null si no se envía
            ];
    
            // Insertar el alumno en la base de datos y obtener el ID del alumno
            $alumnoId = DB::table('alumnos')->insertGetId($alumno);
    
            // Crear el usuario correspondiente en la tabla 'users'
            DB::table('users')->insert([
                'alumno_id' => $alumnoId,
                'name' => $request->input('nombres'),
                'lastname' => $request->input('apellidos'),
                'email' => $request->input('email'),
                'dni' => $request->input('numeroDocumento'),
                'domain_id' => $request->input('domain_id'),
                'password' => Hash::make($request->input('contraseña')),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'rol_id' => 12
            ]);
    
            // Obtener cursos de la carrera seleccionada
            $carreraId = $request->input('carreraId');
            $domainId = $request->input('domain_id');
    
            $cursos = DB::table('cursos')->where('carrera_id', $carreraId)->get();
    
            // Insertar los cursos en la tabla `curso_alumno`
            foreach ($cursos as $curso) {
                DB::table('curso_alumno')->insert([
                    'curso_id' => $curso->id,
                    'alumno_id' => $alumnoId,
                    'domain_id' => $domainId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
    
            DB::commit();
    
            return response()->json(['alumno_id' => $alumnoId, 'message' => 'Alumno y usuario creados correctamente, y asignado a los cursos de la carrera.'], 201);
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
            'estadoId' => 'required|integer',
            'promocion_id' => 'required|integer',
            'estadoAlumno' => 'required|string|in:EN PROCESO,RETIRADO' // Validación del estadoAlumno
        ]);

        $alumno = Alumno::where('id', $id)
            ->where('domain_id', $domain_id)
            ->first();

        if ($alumno) {
            // Procesar las imágenes si están presentes
            $fotoPerfil = $request->input('fotoPerfil') ? $request->input('fotoPerfil') : $alumno->foto_perfil;
            $fotoCarnet = $request->input('fotoCarnet') ? $request->input('fotoCarnet') : $alumno->foto_carnet;

            // Actualizar los datos del alumno, incluyendo estadoAlumno
            $alumno->update([
                "codigo" => $request->input('codigo'),
                "nombres" => $request->input('nombres'),
                "apellidos" => $request->input('apellidos'),
                "celular" => $request->input('nroCelular'),
                "email" => $request->input('email'),
                "carrera_id" => $request->input('carreraId'),
                "ciclo_id" => $request->input('cicloId'),
                "dni" => $request->input('dni'),
                "fecha_nacimiento" => $request->input('fechaNacimiento'),
                "direccion" => $request->input('direccion'),
                "estado_id" => $request->input('estadoId'),
                "promocion_id" => $request->input('promocion_id'),
                "foto_perfil" => $fotoPerfil,
                "foto_carnet" => $fotoCarnet,
                "estadoAlumno" => $request->input('estadoAlumno'),  // Asegúrate de tener esta línea
            ]);

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
        ->leftJoin('plan_de_estudios', 'plan_de_estudios.id', '=', 'alumnos.estado_id') // Join para obtener el nombre del estado
        ->leftJoin('promociones', 'promociones.id', '=', 'alumnos.promocion_id') // Join para obtener el nombre de la promoción
        ->select(
            'alumnos.*',
            'ciclos.nombre as ciclo_nombre',
            'carreras.nombres as carrera_nombre',
            'domains.nombre as institucion',
            'plan_de_estudios.nombre as estado_nombre', // Selecciona el nombre del estado
            'promociones.nombre_promocion as promocion_nombre' // Selecciona el nombre de la promoción
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

public function paymentByStudent(Request $request, $id, $dominio)
{
    $available = $request->input('available', false);

    // Construir la consulta base para los pagos del alumno
    $query = PagoAlumno::with(['pago', 'estado'])
        ->where('alumno_id', $id)
        ->whereHas('pago', function ($q) use ($dominio) {
            $q->where('domain_id', $dominio);
        });

    // Aplicar filtro según el valor de "available"
    if ($available) {
        // Solo incluir pagos pendientes (estado_id = 21)
        $query->where('estado_id', 21);
    }

    // Ejecutar la consulta
    $pagosAlumno = $query->get();

    // Formatear la respuesta
    $pagos = $pagosAlumno->map(function ($pagoAlumno) {
        return [
            'pago_alumno_id' => $pagoAlumno->pago->id,
            'nombre' => $pagoAlumno->pago->nombre ?? 'Sin nombre',
            'monto' => $pagoAlumno->pago->monto ?? 0,
            'fecha_vencimiento' => $pagoAlumno->pago->fecha_vencimiento ?? null,
            'estado' => $pagoAlumno->estado->nombre ?? 'Desconocido',
            'estado_id' => $pagoAlumno->estado->id
        ];
    });

    // Retornar la respuesta en formato JSON
    return response()->json([
        'alumno_id' => $id,
        'pagos' => $pagos,
    ]);
}

public function subirComprobante(Request $request)
{

    $validator = Validator::make(
        $request->all(),
        [
            'pago_id' => 'required|integer',
            'alumno_id' => 'required|integer',
            'domain_id' => 'required|integer',
            'voucher_pago' => 'required|file|mimes:jpg,jpeg,png',
        ]
    );

    if ($validator->fails()) {
        return response()->json([
            'responseCode' => 422,
            'message' => 'Datos inválidos.',
            'errors' => $validator->errors(),
        ], 422);
    }

    $pagoAlumno = PagoAlumno::where('pago_id', $request->pago_id)
        ->where('alumno_id', $request->alumno_id)
        ->first();

    if (!$pagoAlumno) {
        return response()->json(['error' => 'Pago no encontrado'], 404);
    }

    if (!empty($pagoAlumno->voucher_pago)) {
        return response()->json([
            'responseCode' => 409,
            'message' => 'El voucher ya fue subido anteriormente.',
        ], 409);
    }

    $file = $request->file('voucher_pago');
    $base64Image = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));
    // Guardar el comprobante y cambiar el estado a '2' (validado)
    $pagoAlumno->voucher_pago = $base64Image;
    $pagoAlumno->estado_id = 2; // Cambiar estado a 'validado'
    $pagoAlumno->save();

    return response()->json(['message' => 'Comprobante subido exitosamente'], 200);
}

}
