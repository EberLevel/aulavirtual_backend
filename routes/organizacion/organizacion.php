<?php

use Illuminate\Support\Facades\Route;

$router->group(['prefix' => 'maintendance'], function () use ($router) {
    // Route::resource('action', \App\Http\Controllers\Api\Maintenance\ActionController::class);
    $router->get('action','\App\Http\Controllers\Api\Maintenance\ActionController@index');
    $router->post('action', '\App\Http\Controllers\Api\Maintenance\ActionController@store');
    $router->put('action/{id}', '\App\Http\Controllers\Api\Maintenance\ActionController@update');
    $router->delete('action/{id}', '\App\Http\Controllers\Api\Maintenance\ActionController@destroy');

    // Route::resource('current-state', \App\Http\Controllers\Api\Maintenance\CurrentStateController::class);
    $router->get('current-state','\App\Http\Controllers\Api\Maintenance\CurrentStateController@index');
    $router->post('current-state', '\App\Http\Controllers\Api\Maintenance\CurrentStateController@store');
    $router->put('current-state/{id}', '\App\Http\Controllers\Api\Maintenance\CurrentStateController@update');
    $router->delete('current-state/{id}', '\App\Http\Controllers\Api\Maintenance\CurrentStateController@destroy');

    
    // Route::resource('educational-degree', \App\Http\Controllers\Api\Maintenance\EducationalDegreeController::class);
    $router->get('educational-degree/{domain_id}/{id}','\App\Http\Controllers\Api\Maintenance\EducationalDegreeController@show');
    $router->get('educational-degree/{domain_id}','\App\Http\Controllers\Api\Maintenance\EducationalDegreeController@index');
    $router->post('educational-degree/{domain_id}', '\App\Http\Controllers\Api\Maintenance\EducationalDegreeController@store');
    $router->put('educational-degree/{id}', '\App\Http\Controllers\Api\Maintenance\EducationalDegreeController@update');
    $router->delete('educational-degree/{id}', '\App\Http\Controllers\Api\Maintenance\EducationalDegreeController@destroy');

    // Route::resource('employment-link', \App\Http\Controllers\Api\Maintenance\EmploymentLinkController::class);
    $router->get('employment-link','\App\Http\Controllers\Api\Maintenance\EmploymentLinkController@index');
    $router->post('employment-link', '\App\Http\Controllers\Api\Maintenance\EmploymentLinkController@store');
    $router->put('employment-link/{id}', '\App\Http\Controllers\Api\Maintenance\EmploymentLinkController@update');
    $router->delete('employment-link/{id}', '\App\Http\Controllers\Api\Maintenance\EmploymentLinkController@destroy');

    // Route::resource('identification-document', \App\Http\Controllers\Api\Maintenance\IdentificationDocumentController::class);
    $router->get('identification-document','\App\Http\Controllers\Api\Maintenance\IdentificationDocumentController@index');
    $router->post('identification-document', '\App\Http\Controllers\Api\Maintenance\IdentificationDocumentController@store');
    $router->put('identification-document/{id}', '\App\Http\Controllers\Api\Maintenance\IdentificationDocumentController@update');
    $router->delete('identification-document/{id}', '\App\Http\Controllers\Api\Maintenance\IdentificationDocumentController@destroy');

    // Route::resource('management-document-type', \App\Http\Controllers\Api\Maintenance\ManagementDocumentTypeController::class);
    $router->get('management-document-type','\App\Http\Controllers\Api\Maintenance\ManagementDocumentTypeController@index');
    $router->post('management-document-type', '\App\Http\Controllers\Api\Maintenance\ManagementDocumentTypeController@store');
    $router->put('management-document-type/{id}', '\App\Http\Controllers\Api\Maintenance\ManagementDocumentTypeController@update');
    $router->delete('management-document-type/{id}', '\App\Http\Controllers\Api\Maintenance\ManagementDocumentTypeController@destroy');

    // Route::resource('marital-status', \App\Http\Controllers\Api\Maintenance\MaritalStatusController::class);
    $router->get('marital-status','\App\Http\Controllers\Api\Maintenance\MaritalStatusController@index');
    $router->post('marital-status', '\App\Http\Controllers\Api\Maintenance\MaritalStatusController@store');
    $router->put('marital-status/{id}', '\App\Http\Controllers\Api\Maintenance\MaritalStatusController@update');
    $router->delete('marital-status/{id}', '\App\Http\Controllers\Api\Maintenance\MaritalStatusController@destroy');

    // Route::resource('position-level', \App\Http\Controllers\Api\Maintenance\PositionLevelController::class);
    $router->get('position-level','\App\Http\Controllers\Api\Maintenance\PositionLevelController@index');
    $router->post('position-level', '\App\Http\Controllers\Api\Maintenance\PositionLevelController@store');
    $router->put('position-level/{id}', '\App\Http\Controllers\Api\Maintenance\PositionLevelController@update');
    $router->delete('position-level/{id}', '\App\Http\Controllers\Api\Maintenance\PositionLevelController@destroy');

    // Route::resource('position-modality', \App\Http\Controllers\Api\Maintenance\PositionModalityController::class);
    $router->get('position-modality','\App\Http\Controllers\Api\Maintenance\PositionModalityController@index');
    $router->post('position-modality', '\App\Http\Controllers\Api\Maintenance\PositionModalityController@store');
    $router->put('position-modality/{id}', '\App\Http\Controllers\Api\Maintenance\PositionModalityController@update');
    $router->delete('position-modality/{id}', '\App\Http\Controllers\Api\Maintenance\PositionModalityController@destroy');

    // Route::resource('profession', \App\Http\Controllers\Api\Maintenance\ProfessionController::class);
    $router->get('profession','\App\Http\Controllers\Api\Maintenance\ProfessionController@index');
    $router->post('profession', '\App\Http\Controllers\Api\Maintenance\ProfessionController@store');
    $router->put('profession/{id}', '\App\Http\Controllers\Api\Maintenance\ProfessionController@update');
    $router->delete('profession/{id}', '\App\Http\Controllers\Api\Maintenance\ProfessionController@destroy');

    // Route::resource('progress-status', \App\Http\Controllers\Api\Maintenance\ProgressStatusController::class);
    $router->get('progress-status','\App\Http\Controllers\Api\Maintenance\ProgressStatusController@index');
    $router->post('progress-status', '\App\Http\Controllers\Api\Maintenance\ProgressStatusController@store');
    $router->put('progress-status/{id}', '\App\Http\Controllers\Api\Maintenance\ProgressStatusController@update');
    $router->delete('progress-status/{id}', '\App\Http\Controllers\Api\Maintenance\ProgressStatusController@destroy');

    // Route::resource('scale', \App\Http\Controllers\Api\Maintenance\ScaleController::class);
    $router->get('scale','\App\Http\Controllers\Api\Maintenance\ScaleController@index');
    $router->post('scale', '\App\Http\Controllers\Api\Maintenance\ScaleController@store');
    $router->put('scale/{id}', '\App\Http\Controllers\Api\Maintenance\ScaleController@update');
    $router->delete('scale/{id}', '\App\Http\Controllers\Api\Maintenance\ScaleController@destroy');

    // Route::resource('training-type', \App\Http\Controllers\Api\Maintenance\TrainingTypeController::class);
    $router->get('training-type','\App\Http\Controllers\Api\Maintenance\TrainingTypeController@index');
    $router->post('training-type', '\App\Http\Controllers\Api\Maintenance\TrainingTypeController@store');
    $router->put('training-type/{id}', '\App\Http\Controllers\Api\Maintenance\TrainingTypeController@update');
    $router->delete('training-type/{id}', '\App\Http\Controllers\Api\Maintenance\TrainingTypeController@destroy');
});

// $router->group(['prefix' => 'cvbank'], function () use ($router) {
//     Route::get('filters-data', [\App\Http\Controllers\CvBankController::class, 'filtersData']);
//     Route::get('show-by-user/{id}', [\App\Http\Controllers\CvBankController::class, 'showByUser']);
//     Route::get('data-create', [\App\Http\Controllers\CvBankController::class, 'dataCreate']);
//     Route::resource('cvbank', \App\Http\Controllers\CvBankController::class);
//     Route::get('academic-formation/by-bank-cv/{id}', [AcademicFormationController::class, 'byBankCv']);
//     Route::get('academic-formation/data-create', [AcademicFormationController::class, 'getDataCreate']);
//     Route::post('academic-formation/validate-register/{id}', [AcademicFormationController::class, 'validateRegister']);
//     Route::resource('academic-formation', AcademicFormationController::class);
//     Route::get('capacitations/by-bank-cv/{id}', [CapacitacionController::class, 'byBankCv']);
//     Route::get('capacitations/data-create', [CapacitacionController::class, 'getDataCreate']);
//     Route::post('capacitations/validate-register/{id}', [CapacitacionController::class, 'validateRegister']);
//     Route::resource('capacitations', CapacitacionController::class);
//     Route::get('work-experience/data-create', [WorkExperienceController::class, 'getDataCreate']);
//     Route::get('work-experience/by-bank-cv/{id}', [\App\Http\Controllers\Api\WorkExperienceController::class, 'byBankCv']);
//     Route::post('work-experience/validate-register/{id}', [WorkExperienceController::class, 'validateRegister']);
//     Route::post('work-experience/update-data/{id}', [\App\Http\Controllers\Api\WorkExperienceController::class, 'updateData']);
//     Route::apiResource('work-experience', \App\Http\Controllers\Api\WorkExperienceController::class)->parameters([
//         'work-experience' => 'id'
//     ]);
//     Route::resource('comportamientos', \App\Http\Controllers\BehaviorController::class);
//     Route::get('references/by-bank-cv/{id}', [\App\Http\Controllers\ReferenceController::class, 'byBankCv']);
//     Route::get('references/data-create', [\App\Http\Controllers\ReferenceController::class, 'getDataCreate']);
//     Route::resource('references', \App\Http\Controllers\ReferenceController::class);
//     Route::get('evaluation/by-bank-cv/{id}', [\App\Http\Controllers\EvaluationController::class, 'byBankCv']);
//     Route::apiResource('evaluation', \App\Http\Controllers\EvaluationController::class)->parameters([
//         'evaluation' => 'id'
//     ])->names('api.evaluation');

//     Route::get('evaluation-final/by-bank-cv/{id}', [\App\Http\Controllers\EvaluationFinalController::class, 'byBankCv']);
//     Route::apiResource('evaluation-final', \App\Http\Controllers\EvaluationFinalController::class)->parameters([
//         'evaluation-final' => 'id'
//     ])->names('api.evaluation-final');
    
//     Route::resource('aprobacion-gestion', \App\Http\Controllers\ManagementController::class);
//     Route::resource('evaluacion-encuesta', \App\Http\Controllers\EvaluationController::class);


//     Route::prefix('service-orders')->group(function () {
//         Route::get('list/{cv_bank_id}', [\App\Http\Controllers\ServiceOrderController::class, 'index']);
//         Route::post('/', [\App\Http\Controllers\ServiceOrderController::class, 'store']);
//         Route::put('{id}', [\App\Http\Controllers\ServiceOrderController::class, 'update']);
//         Route::delete('{id}', [\App\Http\Controllers\ServiceOrderController::class, 'destroy']);
//     });


//     Route::prefix('personal-documentary-files')->group(function () {
//         Route::get('list/{cv_bank_id}', [\App\Http\Controllers\PersonalDocumentaryFileController::class, 'index']);
//         Route::get('data-create', [\App\Http\Controllers\PersonalDocumentaryFileController::class, 'dataCreate']);
//         Route::post('/', [\App\Http\Controllers\PersonalDocumentaryFileController::class, 'store']);
//         Route::post('/update', [\App\Http\Controllers\PersonalDocumentaryFileController::class, 'update']);
//         Route::delete('{id}', [\App\Http\Controllers\PersonalDocumentaryFileController::class, 'destroy']);
//     });
// });