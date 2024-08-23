<?php

namespace App\Http\Controllers;
use App\Models\Docente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Traits\FileTrait;
use App\Traits\UserTrait;
class DocenteController extends Controller
{
    use FileTrait, UserTrait;
    public function index($domain_id)
    {
        $docente = DB::table('docentes as d')->select('d.*')->where('d.domain_id', $domain_id)->get();
        foreach ($docente as $item) {
            if (!empty($item->foto) && file_exists($item->foto) && is_readable($item->foto)) {
                // Obtén el contenido del archivo y el tipo MIME
                $fileContent = file_get_contents($item->foto);
                $mimeType = mime_content_type($item->foto);
    
                // Convierte el contenido a Base64
                $base64Content = base64_encode($fileContent);
    
                // Agrega el tipo MIME al principio de la cadena Base64
                $item->foto = 'data:' . $mimeType . ';base64,' . $base64Content;
            } else {
                // Maneja el caso donde la foto no existe
                // Por ejemplo, asigna una cadena vacía, un mensaje de error o una imagen predeterminada
                $item->foto = null; // O 'foto no disponible' o la URL de una imagen predeterminada
            }
        }
        return response()->json(['Exito' => true, 'Datos' => $docente], 200);
    }

    public function getLoggedDocente($docente_id, $dominio) {
        $docente = Docente::leftJoin('domains', 'domains.id', '=', 'docentes.domain_id')
            ->select(
                'docentes.*',
                'domains.nombre as institucion'
            )
            ->where('docentes.id', $docente_id)
            ->where('docentes.domain_id', $dominio)
            ->first();
    
        if ($docente) {
            return response()->json($docente);
        }
    
        return response()->json('Docente no encontrado', 404);
    }

    
    public function imagen(Request $request)
    {
        $base64Image = $request->input('base64');
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            return response()->json(['Error' => true, 'Mensaje' => 'Imagen inválida']);
        }
        $imageType = $matches[1];
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $image = base64_decode($base64Image);
        
        // Generar un nombre único para el archivo
        $imageName = uniqid() . '.' . $imageType;

        // Definir la ruta donde se guardará la imagen
        $imagePath = storage_path('app\\public\\docentes\\' . $imageName);

        // Crear el directorio si no existe
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }

        // Guardar la imagen en el disco
        file_put_contents($imagePath, $image);
        return response()->json($imagePath);
    }

    public function show($id)
    {
        $docente = Docente::find($id);
        if(!$docente){
            return response()->json(['Error' => 'Docente no encontrado'], 404);
        }

        return response()->json(['Exito' => true, 'Datos' => $docente], 200);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:20',
            'nombres' => 'required|string|max:20',
            'constraseña' => 'required|string|max:30',
            'celular' => 'required|string|max:20',
            'profesion' => 'required|string|max:30',
            'tipo_documento' => 'required|string|max:20',
            'doc_identidad' => 'required|string|max:20',
            'fecha_nacimiento' => 'required|date|before:today',
            'genero' => 'required|string|max:100',
            // 'foto' => 'required|string|max:100'
        ]);
            $isValidEmail=$this->checkIsValidEmail($request->email);
            if(!$isValidEmail){
                DB::rollBack();
                return response()->json(['message' => 'Email inválido'], 400);
            }
            
            $isValidImage = $this->checkIsValidImage($request->foto);
            if (!$isValidImage) {
                return response()->json(['Error' => true, 'Mensaje' => 'Imagen inválida']);
            }
            $imagePath = $this->uploadFile($request->foto, 'docentes');
            $docenteRol=DB::table('rol')->where('nombre', 'Docente')->first();
            $docente = [
                "codigo" => $request->codigo,
                "nombres"=> $request->nombres,
                // "usuario"=> $request->usuario,
                // "clave"=> $request->clave,
                "celular"=> $request->celular,
                "profesion"=> $request->profesion,
                // "vinculo_laboral"=> $request->vinculo_laboral,
                "tipo_documento"=> $request->tipo_documento,
                "doc_identidad"=> $request->doc_identidad,
                "fecha_nacimiento"=> $request->fecha_nacimiento,
                "genero"=> $request->genero,
                "foto"=> $imagePath,
                "roles"=> $request->roles,
                'domain_id' => $request->domain_id,
                'email' => $request->email,
            ];  
            $docenteId=DB::table('docentes')->insertGetId($docente);
            $userData = [
                'name' => $request->nombres,
                'email' => $request->email,
                'password' => Hash::make($request->clave),
                'rol_id' => $docenteRol->id,
                'domain_id' => $request->domain_id,
                'docente_id' => $docenteId
            ];
            DB::table('users')->insert($userData);
            DB::commit();
            return response()->json(['Exito' => true, 'Mensaje' => 'Registro exitoso'], 201);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json(['Error' => true, 'Mensaje' => $e->getMessage()], 500);
        }    
    }

    public function update(Request $request, $id){
        $docente = Docente::find($id);
        if(!$docente){
            return response()->json(['Error' => 'Docente no encontrado'], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:20',
            'nombres' => 'required|string|max:20',
            'usuario' => 'required|string|max:20|unique:docentes,usuario,'.$id,
            'clave' => 'required|string|max:30',
            'celular' => 'required|string|max:20',
            'profesion' => 'required|string|max:30',
            'vinculo_laboral' => 'required|string|max:20',
            'tipo_documento' => 'required|string|max:20',
            'doc_identidad' => 'required|string|max:20|unique:docentes,doc_identidad,' .$id,
            'fecha_nacimiento' => 'required|date|before:today',
            'edad' => 'required|integer|min:18',
            'genero' => 'required|string|max:100',
            // 'foto' => 'nullable|string|max:100'
            'roles' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['Error' => $validator->errors()], 422);
        }

        $base64Image = $request->input('foto');
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            return response()->json(['Error' => true, 'Mensaje' => 'Imagen inválida']);
        }

        $imageType = $matches[1];
        $base64Image = preg_replace('/^data:image\/\w+;base64,/', '', $base64Image);
        $image = base64_decode($base64Image);
        
        // Generar un nombre único para el archivo
        $imageName = uniqid() . '.' . $imageType;

        // Definir la ruta donde se guardará la imagen
        $imagePath = storage_path('app\\public\\docentes\\' . $imageName);

        // Crear el directorio si no existe
        if (!file_exists(dirname($imagePath))) {
            mkdir(dirname($imagePath), 0777, true);
        }

        // Guardar la imagen en el disco
        file_put_contents($imagePath, $image);


        $docente->update([
            "codigo" => $request->codigo,
            "nombres"=> $request->nombres,
            "usuario"=> $request->usuario,
            "clave"=> $request->clave,
            "celular"=> $request->celular,
            "profesion"=> $request->profesion,
            "vinculo_laboral"=> $request->vinculo_laboral,
            "tipo_documento"=> $request->tipo_documento,
            "doc_identidad"=> $request->doc_identidad,
            "fecha_nacimiento"=> $request->fecha_nacimiento,
            "edad"=> $request->edad,
            "genero"=> $request->genero,
            "foto"=> $imagePath,
            "roles" => $request->roles
        ]);

        return response()->json(['Exito' => true, 'Mensaje' => 'Docente actualizado correctamente'], 200);
    }

    public function destroy($id){
        $docente = Docente::find($id);
        if(!$docente){
            return response()->json(['Error' => 'Docente no encontrado'], 404);
        }
        $docente->delete();
        return response()->json(['Mensaje' => 'Docente Eliminado'], 200);
    }
    public function dropDown($domain_id){
        $docentes = Docente::select('id', 'nombres')->where('domain_id', $domain_id)->get();
        return response()->json($docentes);
    }
}
