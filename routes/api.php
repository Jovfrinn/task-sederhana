<?php

use App\Http\Controllers\api\ProjectController;
use App\Http\Controllers\api\TaskController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);

    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/joined', [ProjectController::class, 'getProjectJoined']);
    Route::post('/projects/{id}/join', [ProjectController::class, 'join']);
    Route::get('/projects/{id}/joined-users', [ProjectController::class, 'joinedUsers']);
    
    
    Route::get('/projects/{id}/tasks', [TaskController::class, 'index']);
    Route::post('/projects/{id}/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{id}', [TaskController::class, 'update']);
    Route::put('/tasks/{task}/assign', [TaskController::class, 'assignUser']);
});

Route::middleware(['auth:api', 'role:admin'])->group(function () {
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);

    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']);
});
