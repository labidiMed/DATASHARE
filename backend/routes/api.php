<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DownloadController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::get('download/{token}', [DownloadController::class, 'show']);
Route::post('download/{token}', [DownloadController::class, 'download']);

Route::middleware('auth:api')->group(function () {
    Route::get('files', [FileController::class, 'index']);
    Route::post('files', [FileController::class, 'store']);
    Route::get('files/{file}', [FileController::class, 'show']);
    Route::delete('files/{file}', [FileController::class, 'destroy']);

    Route::get('tags', [TagController::class, 'index']);
});
