<?php

use Illuminate\Support\Facades\Route;

$router->group(['prefix' => 'maintendance'], function () use ($router) {
    // Route::resource('action', \App\Http\Controllers\Api\Maintenance\ActionController::class);
    $router->get('action','\App\Http\Controllers\Api\Maintenance\ActionController@index');
    $router->post('action', '\App\Http\Controllers\Api\Maintenance\ActionController@store');
    $router->put('action/{id}', '\App\Http\Controllers\Api\Maintenance\ActionController@update');
    $router->delete('action/{id}', '\App\Http\Controllers\Api\Maintenance\ActionController@destroy');

    // Route::resource('current-state', \App\Http\Controllers\Api\Maintenance\CurrentStateController::class);
    // Route::resource('educational-degree', \App\Http\Controllers\Api\Maintenance\EducationalDegreeController::class);
    // Route::resource('employment-link', \App\Http\Controllers\Api\Maintenance\EmploymentLinkController::class);
    // Route::resource('identification-document', \App\Http\Controllers\Api\Maintenance\IdentificationDocumentController::class);
    // Route::resource('management-document-type', \App\Http\Controllers\Api\Maintenance\ManagementDocumentTypeController::class);
    // Route::resource('marital-status', \App\Http\Controllers\Api\Maintenance\MaritalStatusController::class);
    // Route::resource('position-level', \App\Http\Controllers\Api\Maintenance\PositionLevelController::class);
    // Route::resource('position-modality', \App\Http\Controllers\Api\Maintenance\PositionModalityController::class);
    // Route::resource('profession', \App\Http\Controllers\Api\Maintenance\ProfessionController::class);
    // Route::resource('progress-status', \App\Http\Controllers\Api\Maintenance\ProgressStatusController::class);
    // Route::resource('scale', \App\Http\Controllers\Api\Maintenance\ScaleController::class);
    // Route::resource('training-type', \App\Http\Controllers\Api\Maintenance\TrainingTypeController::class);
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