<?php

use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallsController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\LoginLogsController;
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\MessageStatusController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Route عام بدون حماية JWT
    Route::get('/ping', function () {
        return response()->json(['message' => 'API V1 is working!']);
    });

    Route::get('/time-check', function () {
        return [
            'php_time' => date('Y-m-d H:i:s'),
            'carbon_now' => \Carbon\Carbon::now()->toDateTimeString(),
            'timezone' => date_default_timezone_get(),
            'config_app' => config('app.timezone'),
        ];
    });

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });

    // Group routes تحتاج JWT
    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });

        Route::apiResource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
        Route::patch('users/{user}/change-password', [UserController::class, 'changePassword']);

        Route::apiResource('departments', DepartmentController::class);
        Route::get('departments/{id}/users', [DepartmentController::class, 'users']);
        Route::get('departments/filter/active', [DepartmentController::class, 'activeDepartments']);

        Route::apiResource('contacts', ContactsController::class);
        Route::post('contacts/check', [ContactsController::class, 'checkContact']);
        Route::post('contacts/mutual', [ContactsController::class, 'mutualContacts']);

        Route::apiResource('chats', ChatsController::class);
        Route::post('chats/{id}/add-member', [ChatsController::class, 'addMember']);
        Route::post('chats/{id}/remove-member', [ChatsController::class, 'removeMember']);
        Route::post('chats/{id}/leave', [ChatsController::class, 'leaveGroup']);
        Route::get('chats/{id}/members', [ChatsController::class, 'members']);
        Route::get('chats/my/groups', [ChatsController::class, 'myGroups']);

        Route::prefix('messages')->group(function () {
            Route::get('/{chatId}', [MessagesController::class, 'index']);
            Route::post('/{chatId}/add-message', [MessagesController::class, 'store']);
            Route::get('/single/{id}', [MessagesController::class, 'show']);
            Route::delete('/single/{id}', [MessagesController::class, 'destroy']);
            Route::post('/mark-read/{id}', [MessagesController::class, 'markAsRead']);
            Route::post('/bulk-read/{chatId}', [MessagesController::class, 'bulkMarkAsRead']);
            Route::post('/search/{chatId}', [MessagesController::class, 'search']);
        });

<<<<<<< HEAD
        Route::apiResource('message-status', MessageStatusController::class);
        Route::patch('message-status/statuses/bulk-update', [MessageStatusController::class, 'bulkUpdate']);

        Route::get('files', [FilesController::class, 'index']);
        Route::post('files/upload-file', [FilesController::class, 'store']);
        Route::get('files/{id}', [FilesController::class, 'show']);
        Route::delete('files/{id}', [FilesController::class, 'destroy']);

        Route::apiResource('calls', CallsController::class);
        Route::patch('calls/{id}/status', [CallsController::class, 'updateStatus']);
        Route::get('calls/history/list', [CallsController::class, 'history']);
=======
        Route::prefix('message-status')->group(function () {
            // تحديث حالات متعددة
            Route::patch('/statuses/bulk-update', [MessageStatusController::class, 'bulkUpdate']);
        });

        Route::prefix('files')->group(function () {
            Route::get('', [FilesController::class, 'index']);
            Route::post('/upload-file', [FilesController::class, 'store']);
            Route::get('/{id}', [FilesController::class, 'show']);
            Route::delete('/{id}', [FilesController::class, 'destroy']);
        });

        Route::prefix('calls')->group(function () {
            Route::apiResource('', CallsController::class);
            Route::patch('/{id}/status', [CallsController::class, 'updateStatus']);
            Route::get('/history/list', [CallsController::class, 'history']);
        });
>>>>>>> e1ba519c8197441cbae35fb4f353df82f8acba96

        Route::prefix('login-logs')->group(function () {
            Route::get('', [LoginLogsController::class, 'index']);
            Route::post('/add-login-log', [LoginLogsController::class, 'store']);
            Route::patch('/logout', [LoginLogsController::class, 'logout']);
            Route::get('/history', [LoginLogsController::class, 'history']);
        });

        Route::prefix('audit-logs')->group(function () {
            Route::get('', [AuditLogsController::class, 'index']);
            Route::post('/add-audit-log', [AuditLogsController::class, 'store']);
            Route::get('/{id}', [AuditLogsController::class, 'show']);
        });

<<<<<<< HEAD
        Route::apiResource('settings', UserSettingsController::class);
        Route::post('settings/upsert', [UserSettingsController::class, 'upsertSetting']);
=======
        Route::prefix('settings')->group(function () {
            Route::apiResource('', UserSettingsController::class);
            Route::post('/upsert', [UserSettingsController::class, 'upsertSetting']);
        });
>>>>>>> e1ba519c8197441cbae35fb4f353df82f8acba96

    });

});
