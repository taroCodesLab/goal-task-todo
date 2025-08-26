<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\GoalImportController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['web','auth:sanctum'])->group(function() {
    // ゲスト時に作成したデータをデータベースに送る
    Route::post('/goals/import', [GoalImportController::class, 'import']);

    //goal関係
    Route::post('/goals', [GoalController::class, 'store']);
    Route::put('/goals/{goal}', [GoalController::class, 'update']);
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy']);

    Route::post('/goals/reorder', [GoalController::class, 'updateOrder']);

    //task関係
    Route::post('/goals/{goal}/tasks', [TaskController::class, 'store']);
    Route::patch('/goals/{goal}/tasks/{task}/status', [TaskController::class, 'updateStatus'])->scopeBindings();
    Route::delete('/goals/{goal}/tasks/{task}', [TaskController::class, 'destroy']);

});
