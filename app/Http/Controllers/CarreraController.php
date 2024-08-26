<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarreraController extends Controller
{
    public function index($dominio_id){
     //   if(!$request){
     //       return response()->json('Not parameter domain');
     //   }
     //   $id = $request->query('dominio');
    // 
     //   $carrera = Carrera::where('domain_id', $id)->get();
      //  if($carrera){
       //     return response()->json($carrera);
       // }
       // return response()->json('Record not found', 404);
       $carreras = DB::table('carreras as c')
        ->leftJoin('cursos as c2', 'c.id', '=', 'c2.carrera_id')
        ->select('c.*', DB::raw('GROUP_CONCAT(c2.nombre) as cursos'), DB::raw('SUM(c2.cantidad_de_creditos) as total_creditos'))
        ->groupBy('c.id','c.codigo','c.nombres','c.domain_id','c.created_at','c.updated_at')
        ->where('c.domain_id', $dominio_id)
        ->get();

     return response()->json($carreras);
    }
    public function store(Request $request){
        
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'domain_id' => 'required',
        ]);
        $carrera = Carrera::create($request->all());
        return response()->json($carrera, 201);
    }
    public function update($id, Request $request){
        $this->validate($request, [
            'codigo' => 'required|string|max:255',
            'nombres' => 'required|string|max:255',
            'domain_id' => 'required',
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
    public function show($id, $dominio){
        $carrera = Carrera::where('id', $id)->where('dominio', $dominio)->first();
        if ($carrera) {
            return response()->json($carrera);
        }
        return response()->json('Record not found', 404);
    }
    public function dropDown($domain_id){
        $carreras = DB::table('carreras')->select('id', 'nombres')->where('domain_id', $domain_id)->get();
        return response()->json($carreras);;
    }
}
