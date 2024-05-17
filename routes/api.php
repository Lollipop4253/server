<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MeController;
use App\Http\Controllers\RoleController;

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

Route::prefix('ref')->group(function () {
	Route::prefix('user')->group(function () {
		Route::get('/', [MeController::class, 'getUsers']);
		Route::get('{id}/role', [MeController::class, 'getUserRoles']);
		Route::post('{id}/role', [MeController::class, 'giveUserRoles']);
		Route::delete('{id}/role/{role_id}', [MeController::class, 'hardDeleteRole']);
		Route::delete('{id}/role/{role_id}/soft', [MeController::class, 'softDeleteRole']);
		Route::post('{id}/role/{role_id}/restore', [MeController::class, 'restoreDeletedRole']);
	});

	Route::prefix('policy')->group(function () {
		Route::get('role', [RoleController::class, 'getRoles']);
		Route::get('role/{id}', [RoleController::class, 'getTargetRole']);
		Route::post('role/', [RoleController::class, 'createRole']);
		Route::put('role/{id}', [RoleController::class, 'updateRole']);
		Route::delete('role/{id}', [RoleController::class, 'hardDeleteRole']);
		Route::delete('role/{id}/soft', [RoleController::class, 'softDeleteRole']);
		Route::post('role/{id}/restore', [RoleController::class, 'restoreDeletedRole']);

		// Route::get('permission', [PolicyController::class, 'getPermissions']);
		// Route::get('permission/{id}', [PolicyController::class, 'getTargetPermission']);
		// Route::post('permission/', [PolicyController::class, 'createPermission']);
		// Route::put('permission/{id}', [PolicyController::class, 'updatePermission']);
		// Route::delete('permission/{id}', [PolicyController::class, 'hardDeletePermission']);
		// Route::delete('permission/{id}/soft', [PolicyController::class, 'softDeletePermission']);
		// Route::post('permission/{id}/restore', [PolicyController::class, 'restoreDeletedPermission']);
	});
});



Route::prefix('auth')->group(function () {

	Route::get('login', function () {
		return view('/api/auth/login');
	})->name('/login');

	Route::post('login', [LoginController::class, "login"])->name('login');


	Route::middleware('check')->group(function () {
		Route::get('register', function () {
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
