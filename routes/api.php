<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::put('/{secure_id}', [TaskController::class, 'update']);
    Route::delete('/{secure_id}', [TaskController::class, 'destroy']);
    Route::post('/{secure_id}/restore', [TaskController::class, 'restore']);
});
