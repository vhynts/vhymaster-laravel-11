<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\rolesPermissionsController;

use App\Http\Controllers\AuthController;

use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/auth/login', [AuthController::class, 'login'])->name('login')->middleware('auth.user');
Route::post('/auth/login/authenticate', [AuthController::class, 'loginAuthenticate'])->name('loginAuthenticate');
Route::get('/auth/logout', [AuthController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth.login']], function () {

    Route::get('/', function () {
        return redirect('/dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboardIndex');

    Route::post('/users/update-password', [UsersController::class, 'updatePassword'])->name('updatePassword');


    // PENGGUNA
    Route::get('/users', [UsersController::class, 'index'])->name('usersIndex');
    Route::post('/users-data-ajax', [UsersController::class, 'usersDataAjax'])->name('usersDataAjax');
    Route::post('/users/store', [UsersController::class, 'userStore'])->name('userStore');
    Route::delete('/users/{id}/delete', [UsersController::class, 'userDestroy'])->name('userDestroy');
    Route::post('/users/password-reset', [UsersController::class, 'resetPassword'])->name('resetPassword');
    Route::get('/users/{id}/edit', [UsersController::class, 'userEdit'])->name('userEdit');
    Route::put('/users/{id}/update', [UsersController::class, 'userUpdate'])->name('userUpdate');

    // roles & permissions
    Route::get('/others/roles', [rolesPermissionsController::class, 'rolesIndex'])->name('rolesIndex');
    Route::get('/others/roles-data-ajax', [rolesPermissionsController::class, 'rolesDataAjax'])->name('rolesDataAjax');
    Route::post('/others/roles/store', [rolesPermissionsController::class, 'rolesStore'])->name('rolesStore');
    Route::put('/others/roles/{id}/update', [rolesPermissionsController::class, 'rolesUpdate'])->name('rolesUpdate');
    Route::delete('/others/roles/{id}/delete', [rolesPermissionsController::class, 'rolesHapus'])->name('rolesHapus');


    Route::get('others/permissions', [rolesPermissionsController::class, 'permissionsIndex'])->name('permissionsIndex');
    Route::get('/others/permissions-data-ajax', [rolesPermissionsController::class, 'permissionsDataAjax'])->name('permissionsDataAjax');
    Route::post('/others/permissions/store', [rolesPermissionsController::class, 'permissionsStore'])->name('permissionsStore');
    Route::put('/others/permissions/{id}/update', [rolesPermissionsController::class, 'permissionsUpdate'])->name('permissionsUpdate');
    Route::delete('/others/permissions/{id}/delete', [rolesPermissionsController::class, 'permissionsHapus'])->name('permissionsHapus');

    Route::Get('/others/roles/kelola', [rolesPermissionsController::class, 'kelolarolesIndex'])->name('kelolaRolesIndex');
    Route::put('/others/roles/kelola-roles-update', [rolesPermissionsController::class, 'kelolaRolesUpdate'])->name('kelolaRolesUpdate');
    Route::get('/others/roles/{id}/permissions', [rolesPermissionsController::class, 'getPermissionsByRoles'])->name('getPermissionsByroles');

});

