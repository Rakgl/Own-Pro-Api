<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Mobile\AuthController;
use App\Http\Controllers\Api\V1\Mobile\RegisterController;
use App\Http\Controllers\Api\V1\Mobile\ForgetPasswordController;
use App\Http\Controllers\Api\V1\Mobile\TranslationController;
use App\Http\Controllers\Api\V1\Mobile\AppVersionController;
use App\Http\Controllers\Api\V1\Mobile\StaticContentController;
use App\Http\Controllers\Api\V1\Mobile\FcmTokenController;
use App\Http\Controllers\Api\V1\Mobile\NotificationController;
use App\Http\Controllers\Api\V1\Mobile\SettingController;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Register
Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/verify-phone-number', [RegisterController::class, 'verifyPhoneNumber']);
Route::post('/auth/verify-otp', [RegisterController::class, 'verifyOTP']);
Route::post('/auth/resend-otp', [RegisterController::class, 'resendOTP']);

// Forget Password
Route::post('/forget-password/verify-phone-number', [ForgetPasswordController::class, 'verifyPhoneNumber']);
Route::post('/forget-password/verify-otp', [ForgetPasswordController::class, 'verifyOTP']);
Route::post('/forget-password/reset-password', [ForgetPasswordController::class, 'resetPassword']);
Route::post('/forget-password/resend-otp', [ForgetPasswordController::class, 'resendOTP']);

// Translations & Version
Route::get('/get-translations', [TranslationController::class, 'getTranslations']);
Route::get('/version', [AppVersionController::class, 'version']);

Route::post('/auth/refresh-token', [AuthController::class, 'refreshToken']);

// Static contents
Route::get('/static-contents/privacy-policies', [StaticContentController::class, 'privacyPolicies']);
Route::get('/static-contents/about-us', [StaticContentController::class, 'aboutUs']);

// Authenticated customer routes
Route::middleware(['auth:sanctum', 'ability:customer'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/auth/get-user-info', [AuthController::class, 'getUserInfo']);
    Route::get('/auth/get-user-vehicle', [AuthController::class, 'getUserVehicleInfo']);

    Route::post('/fcm-token', [FcmTokenController::class, 'store']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);

    Route::get('/settings', [SettingController::class, 'index']);
});
