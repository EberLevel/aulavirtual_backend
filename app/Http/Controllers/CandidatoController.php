<?php

namespace App\Http\Controllers;

use App\Models\Candidato; // Importa el modelo 'Candidato'
use App\Models\User; // Importa el modelo 'User'
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

class CandidatoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $domain_id)
    {
        $candidatos = Candidato::with('marital_status', 'profession', 'estadoActual', 'education_degree', 'identification_document')
            ->where('domain_id', $domain_id) // Filtrar por domain_id
            ->byTerm($request->term)
            ->byProfessionId($request->profession_id)
            ->byEducationDegreeId($request->education_degree_id)
            ->byCurrentStateId($request->current_state_id)
            ->paginate(10);

        return response()->json($candidatos, 200);
    }
    public function getCiudadByCandidato($id)
    {
        $candidato = Candidato::findOrFail($id); // Buscar el candidato por su ID

        // Verificar si el candidato tiene un 'ciudad_id'
        if (!$candidato->ciudad_id) {
            return response()->json(['message' => 'El candidato no tiene una ciudad asociada.'], 404);
        }

        return response()->json(['ciudad_id' => $candidato->ciudad_id], 200);
    }

    public function getByCiudad($ciudad_id)
    {
        // Buscar candidatos solo por ciudad_id sin incluir relaciones
        $candidatos = Candidato::where('ciudad_id', $ciudad_id)->paginate(10);

        if ($candidatos->isEmpty()) {
            return response()->json(['message' => 'No se encontraron candidatos para la ciudad especificada.'], 404);
        }

        return response()->json($candidatos, 200);
    }

    public function filtersData()
    {
        $data = [
            'education_degrees' => \App\Models\GradoInstruccion::all(),
            'professions' => \App\Models\Profesion::all(),
            'current_states' => \App\Models\EstadoActual::all()
        ];

        return response()->json($data, 200);
    }

    public function dataCreate($domain_id)
    {
        $code = $this->generateCodigoCandidato($domain_id);
        $data = [
            'code' => $code,
            'identification_documents' => \App\Models\DocIdentidad::where('domain_id', $domain_id)->get(),
            'marital_statuses' => \App\Models\EstadoCivil::where('domain_id', $domain_id)->get(),
            'education_degrees' => \App\Models\GradoInstruccion::where('domain_id', $domain_id)->get(),
            'professions' => \App\Models\Profesion::where('domain_id', $domain_id)->get(),
            'current_states' => \App\Models\EstadoActual::where('domain_id', $domain_id)->get(),
            'position_levels' => \App\Models\NivelCargo::where('domain_id', $domain_id)->get(),
            'scales' => \App\Models\Escala::where('domain_id', $domain_id)->get(),
            'actions' => \App\Models\AccionOi::where('domain_id', $domain_id)->get(),
            'training_types' => \App\Models\TipoCapacitacion::where('domain_id', $domain_id)->get(),
            'ocupacion_actual' => \App\Models\OcupacionActual::where('domain_id', $domain_id)->get(),
        ];

        return response()->json($data, 200);
    }

    private function generateCodigoCandidato($domain_id)
    {
        $count = Candidato::where('domain_id', $domain_id)->count();
        return 'CND-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'identification_number' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'position_code' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:100',
            'identification_document_id' => 'nullable|integer',
            'nombre' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'marital_status_id' => 'nullable|integer',
            'number_children' => 'nullable|integer',
            'date_birth' => 'nullable|date',
            'age' => 'nullable|integer',
            'education_degree_id' => 'nullable|integer',
            'profesion' => 'nullable|string|max:191',  // Modificado a string
            'ocupacion_actual' => 'nullable|string|max:191',  // Modificado a string
            'email' => 'nullable|string|max:100',
            'sex' => 'nullable|string|max:1',
            'date_affiliation' => 'nullable|date',
            'estado_actual' => 'nullable|string|max:191',  // Modificado a string
            'domain_id' => 'required|integer|exists:domains,id',
            'ciudad_id' => 'required|integer|exists:ciudades,id',
            'imagen' => 'nullable|string',
        ]);

        // Crear un nuevo usuario asociado con el candidato
        $user = new \App\Models\User([
            'name' => $request->input('nombre'),
            'email' => $request->input('email'),
            'dni' => $request->input('identification_number'),
            'password' => Hash::make($request->input('password')),
            'domain_id' => $request->input('domain_id'),
            'rol_id' => 25,
            'type' => 'user',
            'status' => 'active',
        ]);

        // Guarda el usuario en la base de datos
        $user->save();

        // Ahora crea el registro en la tabla `av_candidatos`
        $candidato = Candidato::create([
            'position_code' => $request->input('position_code'),
            'code' => $request->input('code'),
            'identification_document_id' => $request->input('identification_document_id') ?: null,
            'identification_number' => $request->input('identification_number'),
            'nombre' => $request->input('nombre'),
            'phone' => $request->input('telefono'),
            'marital_status_id' => $request->input('marital_status_id'),
            'number_children' => $request->input('number_children'),
            'date_birth' => $request->input('fecha_nacimiento') ?: null,
            'age' => $request->input('age'),
            'education_degree_id' => $request->input('education_degree_id')?: null,
            'profesion' => $request->input('profesion'),  // Ahora es un string
            'ocupacion_actual' => $request->input('ocupacion_actual'),  // Ahora es un string
            'email' => $request->input('email'),
            'sex' => $request->input('genero'),
            'date_affiliation' => $request->input('fecha_afiliacion') ?: null,
            'estado_actual' => $request->input('estado_actual'),  // Ahora es un string
            'domain_id' => $request->input('domain_id'),
            'ciudad_id' => $request->input('ciudad_id'),
            'user_id' => $user->id,
            'image' => $request->input('imagen'),
        ]);

        // Verificar si el candidato fue creado correctamente antes de actualizar el usuario
        if ($candidato && $candidato->id) {
            $user->update(['candidato_id' => $candidato->id]);
        } else {
            return response()->json(['error' => 'No se pudo crear el candidato.'], 500);
        }

        return response()->json(['candidato' => $candidato], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $candidato = Candidato::findOrFail($id);
        return response()->json(['candidato' => $candidato]);
    }

    public function showByUser($id)
    {
        $candidato = Candidato::where('user_id', $id)->first();
        return response()->json(['candidato' => $candidato]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de los datos
        $data = $this->validate($request, [
            'identification_number' => 'required|string|max:100',
            'password' => 'required|string|min:6',
            'position_code' => 'nullable|string|max:100',
            'code' => 'nullable|string|max:100',
            'identification_document_id' => 'nullable|integer',
            'nombre' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'marital_status_id' => 'nullable|integer',
            'number_children' => 'nullable|integer',
            'fecha_nacimiento' => 'nullable|date',
            'age' => 'nullable|integer',
            'education_degree_id' => 'nullable|integer',
            'profesion' => 'nullable|string|max:191',  // Modificado a string
            'ocupacion_actual' => 'nullable|string|max:191',  // Modificado a string
            'email' => 'nullable|string|max:100',
            'genero' => 'nullable|string|max:1',
            'fecha_afiliacion' => 'nullable|date',
            'estado_actual' => 'nullable|string|max:191',  // Modificado a string
            'domain_id' => 'required|integer|exists:domains,id',
            'ciudad_id' => 'required|integer|exists:ciudades,id',
            'imagen' => 'nullable|string',
        ]);

        $candidato = Candidato::findOrFail($id);

        // Actualizar el candidato con los datos proporcionados
        $candidato->update([
            'position_code' => Arr::get($data, 'position_code', null),
            'code' => Arr::get($data, 'code', null),
            'identification_document_id' => Arr::get($data, 'identification_document_id', null),
            'identification_number' => Arr::get($data, 'identification_number', null),
            'nombre' => Arr::get($data, 'nombre', null),
            'phone' => Arr::get($data, 'telefono', null),
            'marital_status_id' => Arr::get($data, 'marital_status_id', null),
            'number_children' => Arr::get($data, 'number_children', null), // Cambio realizado aquí
            'date_birth' => Arr::get($data, 'fecha_nacimiento', null),
            'age' => Arr::get($data, 'age', null),
            'education_degree_id' => Arr::get($data, 'education_degree_id', null),
            'profesion' => Arr::get($data, 'profesion', null),
            'ocupacion_actual' => Arr::get($data, 'ocupacion_actual', null),
            'email' => Arr::get($data, 'email', null),
            'sex' => Arr::get($data, 'genero', null),
            'date_affiliation' => Arr::get($data, 'fecha_afiliacion', null),
            'estado_actual' => Arr::get($data, 'estado_actual', null),
            'domain_id' => Arr::get($data, 'domain_id'),
            'ciudad_id' => Arr::get($data, 'ciudad_id'),
            'image' => Arr::get($data, 'imagen', null),
        ]);

        return response()->json(['message' => 'Candidato actualizado correctamente', 'data' => $candidato], 200);
    }





    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $candidato = Candidato::find($id);
    
        if (!$candidato) {
            return response()->json(['message' => 'Candidato no encontrado'], 404);
        }
    
        // Buscar y eliminar el usuario asociado al candidato
        $user = \App\Models\User::find($candidato->user_id);
        if ($user) {
            $user->delete();
        }
    
        // Eliminar el candidato
        $candidato->delete();
    
        return response()->json(['message' => 'Candidato y usuario eliminados correctamente'], 204);
    }
    
}
