<?php

use App\Http\Controllers\AppAuthController;
use Illuminate\Http\Request;
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
    });
});

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
