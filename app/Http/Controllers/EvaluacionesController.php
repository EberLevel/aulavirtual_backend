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
        $evaluaciones = Evaluaciones::leftJoin('t_g_parametros as estado', 'estado.nu_id_parametro', '=', 'evaluaciones.estado_id')
            ->leftJoin('t_g_parametros as tipo_evaluacion', 'tipo_evaluacion.nu_id_parametro', '=', 'evaluaciones.tipo_evaluacion_id')
            ->where('evaluaciones.grupo_de_evaluaciones_id', $id)
            ->whereNull('evaluaciones.deleted_at')
            ->select(
                'evaluaciones.*',
                'estado.tx_item_description as estado_nombre',
                'tipo_evaluacion.tx_item_description as tipo_evaluacion_nombre'
            )
            ->get();

        return response()->json($evaluaciones);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());

        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_evaluacion_id' => 'required|exists:tipo_evaluaciones,id',
            'porcentaje_evaluacion' => 'required|numeric',
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required|integer',
            'grupo_de_evaluaciones_id' => 'required|integer',
            'domain_id' => 'required|integer',
        ]);
    
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
            'porcentaje_evaluacion' => 'required|numeric',
            'fecha_y_hora_programo' => 'required|date',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required',
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
