<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    
    public function index($dominio_id){
        $carreras = DB::table('carreras as c')
            ->leftJoin('cursos as c2', 'c.id', '=', 'c2.carrera_id')
            ->leftJoin('plan_de_estudios as p', 'c.plan_de_estudios_id', '=', 'p.id') // Unimos la tabla de plan de estudios
            ->select(
                'c.*', 
                DB::raw('GROUP_CONCAT(c2.nombre) as cursos'), 
                DB::raw('SUM(c2.cantidad_de_creditos) as total_creditos'),
                'p.nombre as plan_de_estudios_nombre'  // Seleccionamos el nombre del plan de estudios
            )
            ->groupBy('c.id', 'c.codigo', 'c.nombres', 'c.domain_id', 'c.created_at', 'c.updated_at', 'p.nombre')
            ->where('c.domain_id', $dominio_id)
            ->get();
    
        return response()->json($carreras);
    }
    

    public function store(Request $request)
    {

        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'domain_id' => 'required',
            'plan_de_estudios_id' => 'required',
        ]);
        $carrera = Carrera::create($request->all());
        return response()->json($carrera, 201);
    }
    public function update($id, Request $request)
    {
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'domain_id' => 'required',
            'plan_de_estudios_id' => 'required',
        ]);
        $carrera = Carrera::where('id', $id)->first();
        if ($carrera) {
            $carrera->update($request->all());
            return response()->json($carrera, 201);
        }
        return response()->json('Record not found', 404);
    }

    public function destroy($id)
    {
        $carrera = Carrera::where('id', $id)->first();
        if ($carrera) {
            $carrera->delete();
            return response()->json('Record deleted', 201);
        }
        return response()->json('Record not found', 404);
    }

    public function show($id, $dominio)
    {
        $carrera = Carrera::where('id', $id)->where('dominio', $dominio)->first();
        if ($carrera) {
            return response()->json($carrera);
        }
        return response()->json('Record not found', 404);
    }

    public function dropDown($plan_de_estudios_id)
    {
        $carreras = DB::table('carreras')
            ->select('id', 'nombres')
            ->where('plan_de_estudios_id', $plan_de_estudios_id)
            ->get();

        // Devuelve los resultados en formato JSON
        return response()->json($carreras);
    }
}
