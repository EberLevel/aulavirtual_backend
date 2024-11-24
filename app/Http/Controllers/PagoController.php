<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; 

class PagoController extends Controller
{
    public function index()
    {
        $pagos = Pago::with('estados')->get();
        if ($pagos) {
            return response()->json([
                'responseCode' => 200,
                'response' => $pagos
            ], 200);
        }
        return response()->json('Record not found', 404);
    }

    public function store(Request $request)
    {
        // Validar los datos entrantes usando Validator
        
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha_pago',
            'estado_id' => 'required|exists:estados,id', // Verifica que el estado exista
        ]);
    
        // Si la validación falla
        if ($validator->fails()) {
            return response()->json([
                'responseCode' => 422,
                'message' => 'Datos inválidos.',
                'errors' => $validator->errors(),
            ], 422);
        }
    
        try {
            // Crear un nuevo registro en la base de datos
            $pago = Pago::create($validator->validated());
    
            // Retornar la respuesta con el registro creado
            return response()->json([
                'responseCode' => 201,
                'message' => 'Pago creado con éxito.',
                'data' => $pago,
            ], 201);
        } catch (\Exception $e) {
            // Manejar errores
            return response()->json([
                'responseCode' => 500,
                'message' => 'Error al crear el pago.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
