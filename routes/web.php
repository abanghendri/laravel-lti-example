<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaunchController;
use App\Http\Controllers\OidcController;
use App\Http\Controllers\Platform;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

Route::prefix('oidc')->group(function(){
    Route::post('/auth',[OidcController::class, 'init']);
    Route::get('/auth',[OidcController::class, 'init'])->name('oidc');
});
Route::get('/.well-known/jwks.json',[OidcController::class,'jwks'])->name('jwks');

//launch
Route::prefix('launch')->group(function(){
    Route::post('/', [LaunchController::class, 'launch'])->name('launch');
    // Route::post('/deep-link', 'LaunchController@deepLink')->name('deep-linking');
    Route::post('/deeplink', [LaunchController::class, 'deepLink'])->name('deeplinking');
});

Route::post('content-selected', [LaunchController::class, 'selectedContent'])->name('content-selected');
Route::post('register-platform', [Platform::class, 'register'])->name('register-platform');
