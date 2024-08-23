<?php

namespace App\Http\Controllers\Api\Maintenance;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Maintenance\GradoInstruccion;
use Illuminate\Support\Facades\Log;

class EducationalDegreeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($domain_id)
    {
        if(!$domain_id){
            $data=DB::table('grado_instruccion')->get();
        }else{
            $data=DB::table('grado_instruccion')->where('domain_id',$domain_id)->get();
        }
        if($domain_id == 0){
            $data=DB::table('grado_instruccion')->get();
        }
        $data=DB::table('grado_instruccion')->get();
        return $data;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $domain_id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'nivel' => 'required|numeric',
            'porcentaje' => 'required|numeric',
        ]);

        $data = $request->all();
        $data['domain_id'] = $domain_id;
        
        $grado = DB::table('grado_instruccion')->insert($data);
        return response()->json($grado, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($domain_id, $id)
    {
        $data=DB::table('grado_instruccion')->where('id',$id)->get();
        return $data;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EducationalDegree $educationalDegree)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'nombre' => 'required|string|max:255',
            'nivel' => 'required|numeric',
            'porcentaje' => 'required|numeric',
        ]);


        $grado = GradoInstruccion::find($id);

        if (!$grado) {
            return response()->json(['mensaje' => 'Grado no encontrada', 'status' => 404], 404);
        }

        $grado->update($request->all());
        return response()->json(true);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $grado = GradoInstruccion::find($id);

        if (!$grado) {
            return response()->json(['mensaje' => 'Grado no encontrada', 'status' => 404], 404);
        }

        $grado->delete();
        return response()->json($grado);
    }
}
