<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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
    return redirect()->route('goal.index');
});

Route::get('/goal', [GoalController::class, 'index'])->name('goal.index');
Route::middleware(['auth'])->group(function () {
    // Route::post('/goal', [GoalController::class, 'store'])->name('goal.store');
    // Route::delete('/goal/{goal}', [GoalController::class, 'destroy'])->name('goal.delete');

    // Route::post('/task', [TaskController::class, 'store'])->name('task.store');
    // Route::put('/task/{task}/status', [TaskController::class, 'updateStatus']);
    // Route::delete('/task/{task}', [TaskController::class, 'destroy'])->name('task.delete');

    // Route::post('/goal/update-order', [GoalController::class, 'updateOrder'])->name('goal.updateOrder');
    // Route::put('/goal/{goal}', [GoalController::class, 'update'])->name('goal.update');

    Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
    Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

    // ユーザー情報編集用のルート
    Route::get('/users/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/update', [UserController::class, 'update'])->name('users.update');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
