<?php

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
/*
$router->post('login','LoginController@login');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->get('user', function () use ($router) {
        return auth()->user();
    });
    $router->get('test', function(){
        dd(1);
    });
    $router->get('users','UsuarioController@index');
    $router->post('users','UsuarioController@store');

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
    $router->get('ciclos-dropdown/{domain_id}', 'ParametroController@dropdown');
    //docentes dropdown
    $router->get('docentes-dropdown/{domain_id}', 'DocenteController@dropdown');

    // DOCUMENTO GESTION RALVA
    $router->get('documento-gestion/{domain_id}', 'DocumentoGestionController@index');
    $router->post('documento-gestion', 'DocumentoGestionController@store');
    $router->get('documento-gestion/{domain_id}/{id}', 'DocumentoGestionController@show');
    $router->put('documento-gestion/{id}', 'DocumentoGestionController@update');
    $router->put('documento-gestion-eliminar/{id}', 'DocumentoGestionController@destroy');
    $router->get('documento-gestion-codigo', 'DocumentoGestionController@generateCode');

    $router->get('docentes/imagen','DocenteController@imagen');
    $router->get('docentes/listar/{domain_id}','DocenteController@index');
    $router->get('docentes/listar/{domain_id}/{id}','DocenteController@show');
    $router->post('docentes/registrar','DocenteController@store');
    $router->put('docentes/actualizar/{id}','DocenteController@update');
    $router->get('docentes/eliminar/{id}','DocenteController@destroy');

    $router->get('cursos', 'CursoController@index');
    $router->post('cursos', 'CursoController@store');
    $router->get('cursos/{id}', 'CursoController@show');
    $router->get('cursos/{id}/syllabus', 'CursoController@getSyllabus');
    $router->put('cursos/{id}', 'CursoController@update');
    $router->delete('cursos/{id}', 'CursoController@destroy');

    $router->get('roles', 'RolController@index');
    $router->post('rol/guardar', 'RolController@store');
    $router->get('rol/{id}', 'RolController@show');
    $router->put('rol/guardar/{id}', 'RolController@update');
    $router->delete('rol/eliminar/{id}', 'RolController@destroy');
    $router->get('roles-dropdown', 'RolController@getRolesDropDown');

    $router->post('rol/guardar-permiso', 'RolController@guardarPermiso');
    $router->get('rol/get-rol-permiso/{id}', 'RolController@getRolPermisos');

    $router->get('empresas', 'EmpresaController@index');
    $router->post('empresa/guardar', 'EmpresaController@store');
    $router->get('empresa/{id}', 'EmpresaController@show');
    $router->put('empresa/guardar/{id}', 'EmpresaController@update');
    $router->delete('empresa/eliminar/{id}', 'EmpresaController@destroy');

    $router->get('permisos', 'PermisoController@index');
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

    $router->get('alumnos/{dominio}', 'AlumnoController@index');
    $router->post('alumnos', 'AlumnoController@store');
    $router->get('alumnos/{id}/{dominio}', 'AlumnoController@show');
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
    $router->post('evaluaciones', 'EvaluacionesController@store');
    $router->get('evaluaciones/{id}', 'EvaluacionesController@index');
    $router->put('evaluaciones/{id}', 'EvaluacionesController@update');
    $router->delete('evaluaciones/{id}', 'EvaluacionesController@destroy');
    //calendarios routes
    $router->post('calendario/alumno', 'CalendarioController@getAlumnoCalendario');
    $router->post('calendario/docente', 'CalendarioController@getDocenteCalendario');


    //preguntas routes
    $router->get('preguntas/{domain_id}/{evaluacion_id}', 'PreguntaController@index');
    $router->post('preguntas', 'PreguntaController@store');
    $router->get('preguntas/{id}', 'PreguntaController@show');
    $router->put('preguntas/{id}', 'PreguntaController@update');
    $router->delete('preguntas/{id}', 'PreguntaController@destroy');
    $router->post('foros', 'ForoController@store');
});*/

