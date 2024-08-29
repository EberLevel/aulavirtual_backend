<?php

namespace App\Http\Controllers;

use App\Models\CvBank\CvBank;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class CvBankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $domainId = $request->user()->domain_id;
        $cvBanks = CvBank::with('marital_status','profession','estadoActual','education_degree','identification_document')
                          ->byTerm($request->term)
                          ->byProfessionId($request->profession_id)
                          ->byEducationDegreeId($request->education_degree_id)
                          ->byCurrentStateId($request->current_state_id)
                          ->paginate(10);

        return response()->json($cvBanks, 200);
    }


    public function filtersData(){
        $data = [
            'education_degrees' => \App\Models\GradoInstruccion::all(),
            'professions' => \App\Models\Profesion::all(),
            'current_states' => \App\Models\EstadoActual::all()
        ];

        return response()->json($data, 200);
    }

    public function dataCreate()
    {
        $data = [
            'identification_documents' => \App\Models\DocIdentidad::all(),
            'marital_statuses' => \App\Models\EstadoCivil::all(),
            'education_degrees' => \App\Models\GradoInstruccion::all(),
            'professions' => \App\Models\Profesion::all(),
            'current_states' => \App\Models\EstadoActual::all(),
            'position_levels' => \App\Models\NivelCargo::all(),
            'scales' => \App\Models\Escala::all(),
            'actions' => \App\Models\AccionOi::all(),
            'training_types' => \App\Models\TipoCapacitacion::all()
        ];

        return response()->json($data, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de la entrada
        $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id',
            'position_code' => 'required|string|max:100',
            'identification_document_id' => 'required|integer',
            'identification_number' => 'string|max:100',
            'names' => 'string|max:100',
            'phone' => 'nullable|string|max:20',
            'marital_status_id' => 'required|integer',
            'number_children' => 'nullable|integer',
            'date_birth' => 'date',
            'age' => 'required|integer',
            'education_degree_id' => 'required|integer',
            'profession_id' => 'nullable|integer',
            'email' => 'nullable|string|max:100',
            'image' => 'nullable|string|regex:/^data:image\/(jpeg|png|gif|bmp);base64,/',  // Validación para asegurarse de que es una cadena Base64 válida
            'sex' => 'nullable|string|max:1',
            'date_affiliation' => 'nullable|date',
            'estado_actual_id' => 'nullable|integer',
            'domain_id' => 'required|integer|exists:domains,id',
        ]);
    
        $cvBank = CvBank::create([
            'user_id' => $request->user_id,
            'position_code' => $request->position_code,
            'code' => $request->code,
            'identification_document_id' => $request->identification_document_id,
            'identification_number' => $request->identification_number,
            'names' => $request->names,
            'phone' => $request->phone,
            'marital_status_id' => $request->marital_status_id,
            'number_children' => $request->number_children,
            'date_birth' => $request->date_birth,
            'age' => $request->age,
            'education_degree_id' => $request->education_degree_id,
            'profession_id' => $request->profession_id,
            'email' => $request->email,
            'image' => $request->image,  // Guardar la cadena Base64 completa en el campo `image`
            'sex' => $request->sex ?? null,
            'date_affiliation' => $request->date_affiliation ?? null,
            'estado_actual_id' => $request->estado_actual_id ?? null,
            'domain_id' => $request->domain_id,
        ]);
    
        return response()->json(['cvBank' => $cvBank], 201);
    }
    
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $cvBank = CvBank::findOrFail($id);
        return response()->json(['cvBank' => $cvBank]);
    }

    public function showByUser($id)
    {
        $cvBank = CvBank::where('user_id', $id)->first();
        return response()->json(['cvBank' => $cvBank]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CvBank $cvBank)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {

        $data =  $this->validate($request, [
            'position_code' => 'required|integer|exists:positions,id',
            'code' => 'required|string|max:100',
            'identification_document_id' => 'required|integer',
            'identification_number' => 'string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'names' => 'string|max:100',
            'phone' => 'nullable|string|max:20',
            'marital_status_id' => 'required|integer',
            'number_children' => 'nullable|integer',
            'date_birth' => 'date',
            'age' => 'required|integer',
            'education_degree_id' => 'required|integer',
            'profession_id' => 'nullable|integer',
            'email' => 'nullable|string|max:100'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images', $imageName, 'public');
        }

        $cvBank = CvBank::findOrFail($id);
        if (!$cvBank) {
            return response()->json(['message' => 'Banco de CV no encontrado'], 404);
        }
        $cvBank->update([
            'position_code' => $request->position_code,
            'code' => $request->code,
            'identification_document_id' => $request->identification_document_id,
            'identification_number' => $request->identification_number,
            'names' => $request->names,
            'phone' => $request->phone,
            'marital_status_id' => $request->marital_status_id,
            'number_children' => $request->number_children,
            'date_birth' => $request->date_birth,
            'age' => $request->age,
            'education_degree_id' => $request->education_degree_id,
            'profession_id' => $request->profession_id,
            'email' => $request->email,
            'urls' => null,
            'sex'               => $request->sex ?? null,
            'date_affiliation'  => $request->date_affiliation ?? null,
            'estado_actual_id'  => $request->estado_actual_id ?? null,
        ]);

        if ($imagePath) {
            $cvBank->update(['image' => $imagePath]);
        }

        return response()->json(['message' => 'banco CV actualizado correctamente', 'data' => $cvBank], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $cvBank = CvBank::find($id);

        if (!$cvBank) {
            return response()->json(['message' => 'Banco de CV no encontrado'], 404);
        }

        $cvBank->delete();

        return response()->json(['message' => 'Banco de CV eliminado correctamente'], 204);
    }
}
