<?php

use App\Http\Controllers\AppAuthController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\OAuthController;
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
//        Route::post('/{provider}/add', [OAuthController::class, 'addAccount'])->name('oauth.add');
    });
    Route::get('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum')->name('logout');
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
