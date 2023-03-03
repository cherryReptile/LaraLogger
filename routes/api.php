<?php

use App\Http\Controllers\AppAuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::prefix('app')->group(function () {
        Route::post('/register', [AppAuthController::class, 'register'])->name('app.register');
        Route::post('/login', [AppAuthController::class, 'login'])->name('app.login');
        Route::post('/add', [AppAuthController::class, 'addAccount'])->middleware(['auth:sanctum'])->name('app.add');
    });
    Route::prefix('oauth')->group(function () {
        Route::get('/{provider}/url', [OAuthController::class, 'getUrl'])->name('oauth.url');
        Route::post('/{provider}/token', [OAuthController::class, 'getToken'])->name('oauth.token');
        Route::post('/{provider}/login', [OAuthController::class, 'login'])->name('oauth.login');
        Route::post('/{provider}/add', [OAuthController::class, 'addAccount'])->name('oauth.add')->middleware('auth:sanctum');
    });
    Route::prefix('profile')->middleware(['auth:sanctum'])->group(function () {
        Route::get('/get', [ProfileController::class, 'get'])->name('profile.get');
        Route::patch('/update', [ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::prefix('log')->group(function () {
    Route::post('/{level}', [LogController::class, 'create'])->middleware('auth:sanctum')->name('log');
});
Route::get('/user/{id}', [UserController::class, 'find'])->name('user.get');
Route::post('/users/get', [UserController::class, 'getAllWithSortAndFilter'])->name('users.get');
Route::get('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum')->name('logout');