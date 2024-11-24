<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PagoAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PagoController extends Controller
{
    public function index($domain_id)
    {
        $validator = Validator::make(
            ['domain_id' => $domain_id],
            ['domain_id' => 'required|numeric']
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pagos = Pago::with('estados')
            ->where("domain_id", "=", "$domain_id")
            ->get();

        if ($pagos->isEmpty()) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'No se encontraron pagos para este dominio.'
            ], 404);
        }

        return response()->json([
            'responseCode' => 200,
            'response' => $pagos
        ], 200);
    }

    public function getPaymentByStudent($domain_id, $pago_id)
{
    // Validar que el pago pertenece al dominio
    $pago = Pago::where('id', $pago_id)
        ->where('domain_id', $domain_id)
        ->first();

    if (!$pago) {
        return response()->json([
            'responseCode' => 404,
            'message' => 'El pago no existe o no pertenece a esta sede.'
        ], 404);
    }

    // Obtener alumnos vinculados al pago
    $alumnos = PagoAlumno::with('alumno') // Relación con el modelo Alumno
        ->where('pago_id', $pago_id)
        ->get();

    return response()->json([
        'responseCode' => 200,
        'pago' => $pago,
        'alumnos' => $alumnos,
    ]);
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_pago',
            'estado_id' => 'required|exists:estados,id',
            'domain_id' => 'required|exists:domains,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $pago = Pago::create($validator->validated());

            return response()->json([
                'responseCode' => 201,
                'message' => 'Pago creado con éxito.',
                'data' => $pago,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'responseCode' => 500,
                'message' => 'Error al crear el pago.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function assignPayment(Request $request)
    {
        $data = $request->all();

        $pagoId = $data['pago_id'];
        $alumnos  = $data['alumnos'];
        $estado = $data['estado'];

        foreach ($alumnos as $alumnoId) {
            PagoAlumno::updateOrCreate(
                ['pago_id' => $pagoId, 'alumno_id' => $alumnoId],
                ['estado_id' => $estado]
            );
        }

        return response()->json([
            'responseCode' => 200,
            'message' => 'Pago asignado con éxito a los alumnos.',
        ]);
    }

    public function uploadPaymentByStudent(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'pago_id' => 'required|exists:pago_alumno,pago_id',
                'alumno_id' => 'required|exists:pago_alumno,alumno_id',
                'voucher' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $pagoAlumno = PagoAlumno::where('pago_id', $request->get('pago_id'))
            ->where('alumno_id', $request->get('alumno_id'))
            ->first();

        if (!$pagoAlumno) {
            return response()->json([
                'responseCode' => 404,
                'message' => 'La relación entre el pago y el alumno no existe.',
            ], 404);
        }

        if (!empty($pagoAlumno->voucher_pago)) {
            return response()->json([
                'responseCode' => 409,
                'message' => 'El voucher ya fue subido anteriormente.',
            ], 409);
        }

        $file = $request->file('voucher');
        $base64Image = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file));

        $pagoAlumno->voucher_pago = $base64Image;
        $pagoAlumno->estado_id = 3;
        $pagoAlumno->save();

        return response()->json([
            'responseCode' => 200,
            'message' => 'Voucher subido con éxito.',
        ]);
    }
}
