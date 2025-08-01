<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ImageController;

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/profile/{id?}', function ($id = null) {
//     $id = $id ?? Auth::id();
//     return app(\App\Http\Controllers\ProfileController::class)->view($id);
// })->middleware('auth')->name('profile.view');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

Route::get('/profile', function () {
    return app(\App\Http\Controllers\ProfileController::class)->view(Auth::id());
})->middleware('auth')->name('profile');

Route::get('/profile/{id}', [ProfileController::class, 'view'])
    ->where('id', '[0-9]+')
    ->name('profile.view');

Route::middleware('auth')->prefix('profile')->group(function () {
    Route::get('/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/profile/{id}/comments', [ProfileController::class, 'comments'])->name('profile.comments');

Route::middleware('role.guard:admin')->group(function () {
    Route::post('/profile/{id}/ban', [ProfileController::class, 'ban'])->name('profile.ban');
    Route::post('/profile/{id}/unban', [ProfileController::class, 'unban'])->name('profile.unban');
});

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/shop', [GameController::class, 'shop'])->name('shop');

Route::get('/game/{id}', [GameController::class, 'view'])
    ->where('id', '[0-9]+')
    ->name('game.view');


Route::middleware('role.guard:admin')->group(function () {
    Route::get('/games/{id}/remove', [GameController::class, 'confirmRemoval'])
        ->where('id', '[0-9]+')
        ->name('games.remove');

    Route::delete('/games/{id}/delete', [GameController::class, 'delete'])
        ->name('games.delete');

    Route::get('/games/publish', [GameController::class, 'create'])->name('games.create');

    Route::post('/peliculas/publish', [GameController::class, 'store'])->name('games.store');

    Route::get('/games/{id}/edit', [GameController::class, 'edit'])
        ->where('id', '[0-9]+')
        ->name('games.edit');

    Route::post('/games/{id}/update', [GameController::class, 'update'])
        ->where('id', '[0-9]+')
        ->name('games.update');
});

Route::middleware('role.guard:admin')->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'users'])->name('dashboard');

    Route::get('/users', [DashboardController::class, 'users'])->name('dashboard.users');
    Route::patch('/users/{id}/role', [DashboardController::class, 'updateRole'])->name('dashboard.users.updateRole');
    
    Route::get('/games', [GameController::class, 'dashboard'])->name('dashboard.games');

    Route::get('/comments', [CommentController::class, 'dashboard'])->name('dashboard.comments');
});


Route::middleware(['auth', 'logout.banned'])->group(function () {
    Route::post('/games/{id}/comments', [CommentController::class, 'store'])
        ->where('id', '[0-9]+')
        ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});

Route::middleware(['auth', 'logout.banned'])->prefix('blog')->group(function () {
    Route::get('/create', [BlogController::class, 'create'])->name('blog.create');
    Route::post('/store', [BlogController::class, 'store'])->name('blog.store');
});

Route::get('/blog/{id}', [BlogController::class, 'show'])
    ->where('id', '[0-9]+')
    ->name('blog.view');


Route::middleware(['auth', 'logout.banned'])->group(function () {
    Route::post('/image/upload-temp', [ImageController::class, 'uploadTempImage'])
        ->name('image.upload-temp');
});

Route::get('/library/user/{id}', [LibraryController::class, 'userLibrary'])->name('library.user');
Route::get('/library', [LibraryController::class, 'index'])->name('library');

Route::middleware(['auth', 'logout.banned'])->group(function () {
    Route::post('/library/{id}/add', [LibraryController::class, 'add'])
        ->where('id', '[0-9]+')
        ->name('library.add');
    Route::delete('/library/remove/{game}', [LibraryController::class, 'remove'])->name('library.remove');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


require __DIR__.'/auth.php';
