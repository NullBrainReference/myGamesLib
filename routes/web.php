<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/shop', [GameController::class, 'shop'])->name('shop');

Route::get('/game/{id}', [GameController::class, 'view'])
    ->where('id', '[0-9]+')
    ->name('game.view');

