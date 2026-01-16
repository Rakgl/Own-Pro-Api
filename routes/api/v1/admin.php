<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\Security\AuthController;
use App\Http\Controllers\Api\V1\Admin\Security\RegisterController;
use App\Http\Controllers\Api\V1\Admin\Security\UserController;
use App\Http\Controllers\Api\V1\Admin\Security\RoleController;
use App\Http\Controllers\Api\V1\Admin\Security\RolePermissionController;
use App\Http\Controllers\Api\V1\Admin\Configuration\SettingController;
use App\Http\Controllers\Api\V1\Admin\Configuration\TranslationController;


// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/account-recovery', [AuthController::class, 'submitAccountRecovery']);
Route::post('/auth/forgot-password', [AuthController::class, 'initiateForgotPassword']);
Route::get('/settings', [SettingController::class, 'index']);
Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken']);

Route::get('/debug-ip', function (Request $request) {
    return response()->json([
        'laravel_request_ip' => $request->ip(),
        'server_remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'not set',
        'header_x_forwarded_for' => $request->header('X-Forwarded-For'),
        'all_headers' => $request->headers->all(),
    ]);
});


Route::get('/translations/{locale}', [TranslationController::class, 'getTranslationsByLocale']);

// Authenticated routes
Route::middleware(['auth:sanctum'])->group(function () {
	Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Security
    Route::get('/auth/info', [AuthController::class, 'info']);

    // Admin-only routes
    Route::middleware(['ability:admin'])->group(function () {

        // Auth
        Route::post('/auth/register', [RegisterController::class, 'register']);
		Route::get('/auth/get-user', [AuthController::class, 'getUser']);

        // Users
        Route::get('/users/edit/{id}', [UserController::class, 'edit']);
        Route::get('/users/audit/{id}', [UserController::class, 'audit']);
        Route::post('/users/upload-profile/{userId}', [UserController::class, 'uploadProfile']);
        Route::delete('/users/remove-profile/{userId}', [UserController::class, 'removeProfile']);
        Route::put('/users/change-password/{userId}', [UserController::class, 'changePassword']);
        Route::post('/users/update-user/{id}', [UserController::class, 'update']);
        Route::resource('/users', UserController::class);

		Route::post('/users/update-profile/{userId}', [UserController::class, 'updateProfile']);

        // Roles
        Route::get('roles/audit/{id}', [RoleController::class, 'audit']);
        Route::get('roles/active', [RoleController::class, 'active']);
        Route::resource('/roles', RoleController::class);

        // Role Permissions
        Route::get('/role-permissions/role/{id}', [RolePermissionController::class, 'permissionsByRole']);
        Route::get('/role-permissions', [RolePermissionController::class, 'index']);
        Route::post('/role-permissions/update', [RolePermissionController::class, 'updateRolePermission']);

        // Settings
        Route::get('/general-settings', [SettingController::class, 'show']);
        Route::post('/general-settings/update', [SettingController::class, 'update']);

        // Translation
         Route::apiResource('translations',TranslationController::class);
    });
});
