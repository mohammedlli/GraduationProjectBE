<?php

use App\Http\Controllers\AnswerController;
use App\Http\Controllers\CodeEditorController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');




Route::prefix('task')->middleware('auth:sanctum')->group(function () {

    Route::post('', [TaskController::class, 'store'])->middleware('checkUser');


    Route::get('', [TaskController::class, 'getAll'])->middleware('checkUser');

    Route::put('/{id}', [TaskController::class, 'update'])
        ->middleware('checkUser');

    Route::get('/{id}', [TaskController::class, 'show'])->middleware('checkUser');

    Route::delete('/{id}', [TaskController::class, 'delete'])
        ->middleware('checkUser');
});



Route::prefix('answer')->group(function () {
    Route::get('/getByTask/{id}', [AnswerController::class, 'getAllByTaskId']);
    Route::get('/analyzeFile/{id}', [AnswerController::class, 'analyzeUploadedFile']);
    Route::get('/file/{id}', [AnswerController::class, 'getFileContent']);
    Route::post('/ai-test', [AnswerController::class, 'testAI']);
    Route::post('', [AnswerController::class, 'store']);
    Route::get('', [AnswerController::class, 'getAll']);
    Route::put('/{id}', [AnswerController::class, 'update']);

    Route::delete('/{id}', [AnswerController::class, 'delete']);

});


Route::prefix('users')->group(function () {
    Route::post('', [UserController::class, 'store']);
    Route::get('', [UserController::class, 'getAll']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::delete('/{id}', [UserController::class, 'delete']);
});

Route::get('stage', [StageController::class, 'getAll']);


Route::post('/run-code', [CodeEditorController::class, 'run']);
