<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/shop', [GameController::class, 'shop'])->name('shop');

Route::get('/game/{id}', [GameController::class, 'view'])
    ->where('id', '[0-9]+')
    ->name('game.view');

Route::get('/games/{id}/remove', [GameController::class, 'confirmRemoval'])
    ->where('id', '[0-9]+')
    ->middleware('auth')
    ->name('games.remove');

Route::delete('/games/{id}/delete', [GameController::class, 'delete'])
    ->middleware('auth')
    ->name('games.delete');

Route::get('/games/publish', [GameController::class, 'create'])
    ->name('games.create')
    ->middleware('auth');

Route::post('peliculas/publish', [GameController::class, 'store'])
    ->name('games.store')
    ->middleware('auth');

Route::post('/games/{id}/add', [GameController::class, 'addToLibrary'])
    ->where('id', '[0-9]+')
    ->name('games.add');

Route::get('/library', [GameController::class, 'library'])->name('library');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


require __DIR__.'/auth.php';
