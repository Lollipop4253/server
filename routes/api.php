<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('auth')->group(function() {

	Route::get('login', function () {
	    return view('/api/auth/login');
	})->name('/login');

	Route::post('login', [LoginController::class, "login"])->name('login');


	Route::middleware('check')->group(function() {
		Route::get('register', function(){
			return view('/api/auth/register');
		});

		Route::post('register', [RegisterController::class, "register"]);
	});
	
	Route::middleware('auth:api')->group(function () {

	    Route::get('me', [MeController::class, "check"])->name('me');

		Route::post('out', [LoginController::class, "out"]);

		Route::post('out_all', [LoginController::class, "outAll"]);

		Route::get('tokens', [MeController::class, "getTokens"]);
	});
});