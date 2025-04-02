<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('todo.index');
});

Route::get('/todo', [GoalController::class, 'index'])->name('todo.index');
Route::post('/todo', [GoalController::class, 'store'])->name('todo.store');
Route::delete('/todo/{goal}', [GoalController::class, 'destroy'])->name('todo.delete');

Route::post('/task', [TaskController::class, 'store'])->name('task.store');

Route::put('/task/{task}/status', [TaskController::class, 'updateStatus']);
Route::delete('/task/{task}', [TaskController::class, 'destroy'])->name('task.delete');

Route::post('/todo/update-order', [GoalController::class, 'updateOrder'])->name('todo.updateOrder');

Route::put('/todo/{goal}', [GoalController::class, 'update'])->name('todo.update');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
