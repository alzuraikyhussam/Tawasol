<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Route عام بدون حماية JWT
    Route::get('/ping', function () {
        return response()->json(['message' => 'API V1 is working!']);
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Group routes تحتاج JWT
    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
        });

        Route::prefix('users')->group(function () {
            Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus']);
            Route::patch('/{id}/change-password', [UserController::class, 'changePassword']);
        });

        Route::prefix('departments')->group(function () {
            Route::get('/{id}/users', [DepartmentController::class, 'users']);
            Route::get('/filter/active', [DepartmentController::class, 'activeDepartments']);
        });

        Route::prefix('contacts')->group(function () {
            Route::post('/check', [ContactsController::class, 'checkContact']);
            Route::post('/mutual', [ContactsController::class, 'mutualContacts']);
        });

        Route::prefix('chats')->group(function () {
            Route::post('/{id}/add-member', [ChatsController::class, 'addMember']);
            Route::post('/{id}/remove-member', [ChatsController::class, 'removeMember']);
            Route::post('/{id}/leave', [ChatsController::class, 'leaveGroup']);
            Route::get('/{id}/members', [ChatsController::class, 'members']);
            Route::get('/my/groups', [ChatsController::class, 'myGroups']);
        });

        Route::prefix('chats')->group(function () {
            Route::post('/mark-read/{id}', [MessagesController::class, 'markAsRead']);
            Route::post('/bulk-read/{chatId}', [MessagesController::class, 'bulkMarkAsRead']);
            Route::post('/search/{chatId}', [MessagesController::class, 'search']);
        });

    });

});
