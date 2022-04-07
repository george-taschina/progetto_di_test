<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\v1\RegisterController as RegisterControllerV1;
use App\Http\Controllers\API\v1\ToDoListController as ToDoListControllerV1;
use App\Http\Controllers\API\v1\TagController as TagControllerV1;
use App\Http\Controllers\API\v1\TaskController as TaskControllerV1;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('1.0')->group(function () {
    Route::controller(RegisterControllerV1::class)->group(function(){
        Route::post('register', 'register');
        Route::post('login', 'login');
    });

    Route::middleware('auth:sanctum')->group( function () {

        Route::apiResources([
            'lists' => ToDoListControllerV1::class,
            'tags' => TagControllerV1::class,
        ]);

        Route::apiResource('lists.tasks', TaskControllerV1::class)->except([
            'update','destroy'
        ]);

        Route::put('tasks/{id}', [TaskControllerV1::class, 'update']);

        Route::post('tasks/{idTask}/tags/{idTag}', [TagControllerV1::class, 'attach']);

        Route::delete('tasks/{idTask}/tags/{idTag}', [TagControllerV1::class, 'detach']);

        Route::post('logout', [RegisterControllerV1::class,'logout']);
    });

});