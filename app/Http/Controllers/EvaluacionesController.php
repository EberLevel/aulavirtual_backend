<?php

namespace App\Http\Controllers;

use App\Models\Evaluaciones;
use Illuminate\Http\Request;
use Carbon\Carbon;
class EvaluacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $evaluaciones = Evaluaciones::leftJoin('estados', 'estados.id', '=', 'evaluaciones.estado_id')
            ->leftJoin('t_g_parametros as tipo_evaluacion', 'tipo_evaluacion.nu_id_parametro', '=', 'evaluaciones.tipo_evaluacion_id')
            ->where('evaluaciones.grupo_de_evaluaciones_id', $id)
            ->whereNull('evaluaciones.deleted_at')
            ->select(
                'evaluaciones.*',
                'estados.nombre as estado_nombre', // Obteniendo el nombre del estado desde la tabla `estados`
                'tipo_evaluacion.tx_item_description as tipo_evaluacion_nombre'
            )
            ->get();
    
        return response()->json($evaluaciones);
    }
    
    

    
    public function getEvaluacionById($id)
    {
        // Buscar la evaluación por su ID
        $evaluacion = Evaluaciones::select(
            'id',
            'nombre',
            'tipo_evaluacion_id',
            'fecha_y_hora_programo',
            'observaciones',
            'estado_id',
            'domain_id',
            'grupo_de_evaluaciones_id'
        )
        ->where('id', $id)
        ->first();
    
        // Verificar si se encontró la evaluación
        if (!$evaluacion) {
            return response()->json(['message' => 'Evaluación no encontrada'], 404);
        }
    
        // Devolver los datos de la evaluación
        return response()->json($evaluacion);
    }
    public function updateEvaluacionById(Request $request, $id)
    {
        // Validar los datos recibidos
        $validatedData = $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'required|exists:t_g_parametros,nu_id_parametro', // Si aún usas t_g_parametros para el tipo de evaluación
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required|exists:estados,id', // Cambiado para validar contra la tabla `estados`
            'domain_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
        ]);
    
        // Buscar la evaluación por su ID
        $evaluacion = Evaluaciones::find($id);
    
        // Verificar si la evaluación existe
        if (!$evaluacion) {
            return response()->json(['message' => 'Evaluación no encontrada'], 404);
        }
    
        // Actualizar la evaluación con los datos validados
        $evaluacion->update($validatedData);
    
        // Devolver la evaluación actualizada
        return response()->json($evaluacion);
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData =$this->validate($request, [
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'required|exists:t_g_parametros,nu_id_parametro',
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required',
            'domain_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
        ]);
        $validatedData['fecha_y_hora_programo'] = Carbon::parse($validatedData['fecha_y_hora_programo'])->format('Y-m-d H:i:s');

        $evaluacion = Evaluaciones::create($validatedData);
        return response()->json($evaluacion, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Evaluaciones  $Evaluaciones
     * @return \Illuminate\Http\Response
     */
    public function show(Evaluaciones $Evaluaciones)
    {
        return response()->json($Evaluaciones);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Evaluaciones  $Evaluaciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Evaluaciones $Evaluaciones)
    {
        $validatedData = $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'required|exists:t_g_parametros,nu_id_parametro',
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required',
            'domain_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
        ]);

        $Evaluaciones->update($validatedData);
        return response()->json($Evaluaciones);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Evaluaciones  $Evaluaciones
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $grupo = Evaluaciones::withTrashed()->findOrFail($id);
        $grupo->delete();
    
        return response()->json(['message' => 'Curso eliminado exitosamente'], 201);
    }
}