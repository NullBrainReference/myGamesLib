<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiProjectController;
use App\Http\Controllers\Api\ApiHirePositionController;
use App\Http\Controllers\Api\ApiFriendController;
use App\Http\Controllers\Api\ApiMessageController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ApiAuthController::class, 'me']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    // Messages API
    Route::get('/messages/conversations', [ApiMessageController::class, 'conversations']);
    Route::get('/messages/{user}', [ApiMessageController::class, 'messages']);
    Route::post('/messages/{user}', [ApiMessageController::class, 'send']);

    // Friends API
    Route::get('/friends', [ApiFriendController::class, 'index']);
    Route::post('/friends/{user}/request', [ApiFriendController::class, 'sendRequest']);
    Route::post('/friends/{user}/accept', [ApiFriendController::class, 'acceptRequest']);
    Route::delete('/friends/{user}', [ApiFriendController::class, 'removeFriend']);

    // Projects API
    Route::prefix('projects')->group(function () {
        Route::get('/', [ApiProjectController::class, 'index']);
        Route::get('/{id}', [ApiProjectController::class, 'show']);
    });

    Route::get('/projects/{project}/vacancies', [ApiHirePositionController::class, 'index']);
    Route::post('/projects/{project}/vacancies', [ApiHirePositionController::class, 'store']);
    Route::get('/projects/{project}/vacancies/applicants', [ApiHirePositionController::class, 'applicants']);
    Route::post('/projects/{project}/request-tickets', [ApiHirePositionController::class, 'requestMoreTickets']);
    
    Route::post('/positions/{position}/apply', [ApiHirePositionController::class, 'apply']);
    Route::post('/tickets/{ticket}/review', [ApiHirePositionController::class, 'reviewTicket']);

    Route::post('/projects/{project}/request-position-slots', [ApiHirePositionController::class, 'requestPositionSlots']);
    Route::post('/position-requests/{positionRequest}/review', [ApiHirePositionController::class, 'reviewPositionSlotRequest']);
});