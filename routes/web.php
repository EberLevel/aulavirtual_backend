<?php

use Illuminate\Support\Facades\Route;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->group(['prefix' => 'api/{domain}', 'middleware' => ['validate.domain']], function () use ($router) {

    $router->get('test', function () {
        dd(1);
    });
    $router->get('usuarios/{domain_id}', 'UsuarioController@index');
    $router->post('usuarios', 'UsuarioController@store');
    $router->delete('usuarios/{id}', 'UsuarioController@destroy');
    $router->post('login', 'LoginController@login');

    $router->get('maestros', 'MaestroController@index');
    $router->post('maestros', 'MaestroController@store');
    $router->get('maestros/{id}', 'MaestroController@show');
    $router->put('maestros/{id}', 'MaestroController@update');
    $router->delete('maestros/{id}', 'MaestroController@destroy');


    $router->get('parametros', 'ParametroController@index');
    $router->post('parametros', 'ParametroController@store');
    $router->get('parametros/{id}', 'ParametroController@show');
    $router->put('parametros/{id}', 'ParametroController@update');
    $router->delete('parametros/{id}', 'ParametroController@destroy');
    $router->get('parametrosAll/{domain_id}', 'ParametroController@indexAll');
    $router->get('parametrosRecursive/{domain_id}', 'ParametroController@indexRecursive');

    $router->get('instituciones', 'InstitucioneController@index');
    $router->post('instituciones', 'InstitucioneController@store');
    $router->get('instituciones/{id}', 'InstitucioneController@show');
    $router->put('instituciones/{id}', 'InstitucioneController@update');
    $router->delete('instituciones/{id}', 'InstitucioneController@destroy');
    $router->get('institutions-dropdown', 'InstitucioneController@dropdown');
    $router->get('carreras-list/{dominio_id}', 'CarreraController@index');
    $router->post('carreras', 'CarreraController@store');
    $router->get('carreras/{id}', 'CarreraController@show');
    $router->put('carreras/{id}', 'CarreraController@update');
    $router->delete('carreras/{id}', 'CarreraController@destroy');
    //common routes
    //get carreras dropdown
    $router->get('carreras-dropdown', 'CarreraController@dropdown');
    $router->get('carreras-dropdown/{domain_id}', 'CarreraController@dropdown');
    //get ciclos dropdown
    $router->get('ciclos-dropdown', 'ParametroController@dropdown');
    $router->get('ciclos-dropdown/{domain_id}', 'CicloController@dropDown');
    //docentes dropdown
    $router->get('docentes-dropdown/{domain_id}', 'DocenteController@dropdown');

    // DOCUMENTO GESTION RALVA
    $router->get('documento-gestion/{domain_id}', 'DocumentoGestionController@index');
    $router->post('documento-gestion', 'DocumentoGestionController@store');
    $router->get('documento-gestion/{domain_id}/{id}', 'DocumentoGestionController@show');
    $router->put('documento-gestion/{id}', 'DocumentoGestionController@update');
    $router->put('documento-gestion-eliminar/{id}', 'DocumentoGestionController@destroy');
    $router->get('documento-gestion-codigo', 'DocumentoGestionController@generateCode');
    $router->get('alumnos/{domain_id}/{user_id}/documentos', 'DocumentoGestionController@getAlumnoDocuments');

    $router->get('docentes/logged/{docente_id}/{dominio}', 'DocenteController@getLoggedDocente');
    $router->get('docentes/imagen', 'DocenteController@imagen');
    $router->get('docentes/listar/{domain_id}', 'DocenteController@index');
    $router->get('docentes/listar/{domain_id}/{id}', 'DocenteController@show');
    $router->post('docentes/registrar', 'DocenteController@store');
    $router->put('docentes/actualizar/{id}', 'DocenteController@update');
    $router->get('docentes/eliminar/{id}', 'DocenteController@destroy');


    $router->get('cursos', 'CursoController@index');
    $router->post('cursos', 'CursoController@store');
    $router->get('cursos/{id}', 'CursoController@show');
    $router->get('cursos/{id}/syllabus', 'CursoController@getSyllabus');
    $router->get('cursos/{id}/tema', 'CursoController@getTema');
    $router->put('cursos/{id}', 'CursoController@update');
    $router->delete('cursos/{id}', 'CursoController@destroy');

    $router->get('roles/{domain_id}', 'RolController@index');
    $router->post('rol/guardar', 'RolController@store');
    $router->get('rol/{id}', 'RolController@show');
    $router->put('rol/guardar/{id}', 'RolController@update');
    $router->delete('rol/eliminar/{id}', 'RolController@destroy');
    $router->get('roles-dropdown', 'RolController@getRolesDropDown');

    $router->post('rol/guardar-permiso', 'RolController@guardarPermiso');
    $router->get('rol/get-rol-permiso/{id}/{domain_id}', 'RolController@getRolPermisos');

    $router->get('empresas', 'EmpresaController@index');
    $router->post('empresa/guardar', 'EmpresaController@store');
    $router->get('empresa/{id}', 'EmpresaController@show');
    $router->put('empresa/guardar/{id}', 'EmpresaController@update');
    $router->delete('empresa/eliminar/{id}', 'EmpresaController@destroy');
    $router->get('empresas-dropdown', 'EmpresaController@dropdown');
    $router->get('permisos/{domain_id}', 'PermisoController@index');
    $router->post('permiso/guardar', 'PermisoController@store');
    $router->get('cursos/carrera/{id}', 'CursoController@index');

    // CAPACITACIONES GESTION RALVA
    $router->get('capacitaciones', 'CapacitacionController@index');
    $router->post('capacitaciones', 'CapacitacionController@store');
    $router->get('capacitaciones/{id}', 'CapacitacionController@show');
    $router->put('capacitaciones/{id}', 'CapacitacionController@update');
    $router->put('capacitaciones-eliminar/{id}', 'CapacitacionController@destroy');
    $router->get('capacitaciones-codigo', 'CapacitacionController@generateCode');
    $router->get('capacitaciones-docentes', 'CapacitacionController@listarDocentes');

    $router->get('grupo-de-evaluaciones/{curso_id}', 'GrupoDeEvaluacionesController@index');
    $router->post('grupo-de-evaluaciones', 'GrupoDeEvaluacionesController@store');
    $router->put('grupo-de-evaluaciones/{id}', 'GrupoDeEvaluacionesController@update');
    $router->delete('grupo-de-evaluaciones/{id}', 'GrupoDeEvaluacionesController@destroy');
    $router->get('evaluacion/{id}', 'EvaluacionesController@getEvaluacionById');
    $router->put('evaluacion/{id}', 'EvaluacionesController@updateEvaluacionById');


    $router->get('alumnos/logged/{alumno_id}/{dominio}', 'AlumnoController@getLoggedAlumno');
    $router->get('alumnos/{id}/{dominio}', 'AlumnoController@show');
    $router->get('alumnos/{dominio}', 'AlumnoController@index');
    $router->post('alumnos', 'AlumnoController@store');
    $router->delete('alumnos/{id}/{dominio}', 'AlumnoController@destroy');



    //horario routes
    $router->get('horario', 'HorarioController@index');
    $router->post('horario', 'HorarioController@store');
    $router->get('horario/{id}', 'HorarioController@show');
    //participantes routes
    $router->get('participantes/{domain_id}/{curso_id}', 'ParticipanteController@show');
    $router->post('participantes', 'ParticipanteController@store');
    //asistencia routes
    $router->post('asistencia-curso', 'AsistenciaCursoController@show');
    $router->post('asistencia-curso-marcar', 'AsistenciaCursoController@store');
    //evaluacion routes
    $router->post('evaluaciones', 'EvaluacionesController@store');
    $router->get('evaluaciones/{id}', 'EvaluacionesController@index');
    $router->put('evaluaciones/{id}', 'EvaluacionesController@update');
    $router->delete('evaluaciones/{id}', 'EvaluacionesController@destroy');
    //calendarios routes
    $router->post('calendario/alumno', 'CalendarioController@getAlumnoCalendario');
    $router->post('calendario/docente', 'CalendarioController@getDocenteCalendario');

    // Grado de Instrucción
    $router->get('grado-instrucciones/{domain_id}', 'GradoInstruccionController@index');
    $router->post('grado-instruccion', 'GradoInstruccionController@store');
    $router->get('grado-instruccion/{id}', 'GradoInstruccionController@show');
    $router->put('grado-instruccion/{id}', 'GradoInstruccionController@update');
    $router->delete('grado-instruccion/{id}', 'GradoInstruccionController@destroy');

    // Vinculo Laboral
    $router->get('vinculos-laborales/{domain_id}', 'VinculoLaboralController@index');
    $router->post('vinculo-laboral', 'VinculoLaboralController@store');
    $router->get('vinculo-laboral/{id}', 'VinculoLaboralController@show');
    $router->put('vinculo-laboral/{id}', 'VinculoLaboralController@update');
    $router->delete('vinculo-laboral/{id}', 'VinculoLaboralController@destroy');

    // Nivel de Cargo
    $router->get('niveles-cargo/{domain_id}', 'NivelCargoController@index');
    $router->post('nivel-cargo', 'NivelCargoController@store');
    $router->get('nivel-cargo/{id}', 'NivelCargoController@show');
    $router->put('nivel-cargo/{id}', 'NivelCargoController@update');
    $router->delete('nivel-cargo/{id}', 'NivelCargoController@destroy');

    // Modalidades de Puesto
    $router->get('modalidades-puesto/{domain_id}', 'ModalidadPuestoController@index');
    $router->post('modalidad-puesto', 'ModalidadPuestoController@store');
    $router->get('modalidad-puesto/{id}', 'ModalidadPuestoController@show');
    $router->put('modalidad-puesto/{id}', 'ModalidadPuestoController@update');
    $router->delete('modalidad-puesto/{id}', 'ModalidadPuestoController@destroy');

    //Profesiones
    $router->get('profesiones/{domain_id}', 'ProfesionController@index');
    $router->post('profesion', 'ProfesionController@store');
    $router->get('profesion/{id}', 'ProfesionController@show');
    $router->put('profesion/{id}', 'ProfesionController@update');
    $router->delete('profesion/{id}', 'ProfesionController@destroy');

    // Rutas para EstadoAvance
    $router->get('estados-avance/{domain_id}', 'EstadoAvanceController@index');
    $router->post('estado-avance', 'EstadoAvanceController@store');
    $router->get('estado-avance/{id}', 'EstadoAvanceController@show');
    $router->put('estado-avance/{id}', 'EstadoAvanceController@update');
    $router->delete('estado-avance/{id}', 'EstadoAvanceController@destroy');

    //Escala
    $router->get('escalas/{domain_id}', 'EscalaController@index');
    $router->post('escala', 'EscalaController@store');
    $router->get('escala/{id}', 'EscalaController@show');
    $router->put('escala/{id}', 'EscalaController@update');
    $router->delete('escala/{id}', 'EscalaController@destroy');

    //Año - Afiliado al Paritdo en Frontend
    $router->get('anos/{domain_id}', 'AnoController@index');
    $router->post('ano', 'AnoController@store');
    $router->get('ano/{id}', 'AnoController@show');
    $router->put('ano/{id}', 'AnoController@update');
    $router->delete('ano/{id}', 'AnoController@destroy');

    //preguntas routes
    $router->get('preguntas/{domain_id}/{evaluacion_id}', 'PreguntaController@index');
    $router->post('preguntas', 'PreguntaController@store');
    $router->get('preguntas/{id}', 'PreguntaController@show');
    $router->put('preguntas/{id}', 'PreguntaController@update');
    $router->delete('preguntas/{id}', 'PreguntaController@destroy');
    $router->post('foros', 'ForoController@store');
    $router->get('foros/{domain_id}/{alumno_id}/{docente_id}', 'ForoController@show');
    $router->post('foros/message', 'ForoController@storeMessage');
    //areas de formacion
    $router->get('areas-de-formacion/{domain_id}', 'AreaDeFormacionController@index');
    $router->post('areas-de-formacion/{domain_id}', 'AreaDeFormacionController@store');
    $router->put('areas-de-formacion/{domain_id}/{id}', 'AreaDeFormacionController@update');
    $router->delete('areas-de-formacion/{domain_id}/{id}', 'AreaDeFormacionController@destroy');


    //modulos formativos
    $router->get('modulos-formativos/{domain_id}', 'ModuloFormativoController@index');
    $router->post('modulos-formativos/{domain_id}', 'ModuloFormativoController@store');
    $router->put('modulos-formativos/{domain_id}/{id}', 'ModuloFormativoController@update');
    $router->delete('modulos-formativos/{domain_id}/{id}', 'ModuloFormativoController@destroy');

    //ciclos
    $router->get('ciclos/{domain_id}', 'CicloController@index');
    $router->post('ciclos/{domain_id}', 'CicloController@store');
    $router->put('ciclos/{domain_id}/{id}', 'CicloController@update');
    $router->delete('ciclos/{domain_id}/{id}', 'CicloController@destroy');
    $router->post('ciclos-orden', 'CicloController@orden');

    //Promociones
    $router->get('promociones/{domain_id}', 'PromocionController@index');
    $router->post('promociones', 'PromocionController@store');
    $router->get('promocion/{id}', 'PromocionController@show');
    $router->put('promociones/{id}', 'PromocionController@update');
    $router->delete('promociones/{id}', 'PromocionController@destroy');

    //estado
    $router->get('estados/{domain_id}', 'EstadoController@index');
    $router->post('estados/{domain_id}', 'EstadoController@store');
    $router->put('estados/{domain_id}/{id}', 'EstadoController@update');
    $router->delete('estados/{domain_id}/{id}', 'EstadoController@destroy');

    //estado de curso
    $router->get('estados-curso/{domain_id}', 'EstadoCursoController@index');
    $router->post('estados-curso/{domain_id}', 'EstadoCursoController@store');
    $router->put('estados-curso/{domain_id}/{id}', 'EstadoCursoController@update');
    $router->delete('estados-curso/{domain_id}/{id}', 'EstadoCursoController@destroy');
    //aulas
    $router->get('aulas/{dominio_id}', 'AulaController@index');
    $router->post('aulas', 'AulaController@store');
    $router->delete('aulas/{id}', 'AulaController@destroy');
    $router->post('aulas/disponibilidad', 'AulaController@saveDisponibilidad');
    $router->get('aulas/disponibilidad/{aula_id}', 'AulaController@getDisponibilidad');
    $router->delete('aulas/disponibilidad/{id}', 'AulaController@destroyDisponibilidad');

    //get institution data
    $router->get('company/{domain_id}', 'CompanyController@show');
    $router->post('company', 'CompanyController@store');



    //get cursos by docente
    $router->get('cursos-docente/{docente_id}', 'CursoDocenteController@index');

    //get cursos by alumno
    $router->get('cursos-alumno/{alumno_id}', 'CursoAlumnoController@index');

    //alumno preguntas
    $router->post('alumno-preguntas', 'PreguntaAlumnoController@guardarAlumnoPregunta');



    Route::get('cursos/{curso_id}/evaluaciones', 'PreguntaAlumnoController@obtenerCursosConEvaluaciones');

    Route::get('obtener-preguntas-corregidas/{pregunta_id}', 'PreguntaAlumnoController@obtenerPreguntasNoCorregidas');

    //Ceiber Conrago Garibay Choque - 2024-08-10 Subgrupo de rutas para las apis de organizacion institucional
    $router->group(['prefix' => 'organizacion-institucional'], function () use ($router) {
        //Mantenimientos
        $router->get('action/{domain_id}', 'AccionController@index');
        $router->get('action/get/{id}', 'AccionController@show');
        $router->post('action/{domain_id}', 'AccionController@store');
        $router->put('action/{domain_id}/{id}', 'AccionController@update');
        $router->delete('action/{domain_id}/{id}', 'AccionController@destroy');
    });
});
