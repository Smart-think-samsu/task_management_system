<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\api\BoxOperationController;
use App\Http\Controllers\api\BoxStockController;
use App\Http\Controllers\api\DlStockController;
use App\Http\Controllers\api\PermissionController;
use App\Http\Controllers\api\RoleController;
use App\Http\Controllers\api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/user/login', [AuthController::class, 'login'])->name('user.login');

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    //=== User Api Route ===//
    Route::get('/user', [UserController::class, 'index'])->name('user.index')->middleware('permission:user-list');
    Route::post('/user', [UserController::class, 'store'])->name('user.store')->middleware('permission:user-create');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create')->middleware('permission:user-create');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('user.edit')->middleware('permission:user-edit');
    Route::get('/user/{user}', [UserController::class, 'show'])->name('user.show')->middleware('permission:user-show');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('user.update')->middleware('permission:user-edit');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('permission:user-delete');
    Route::post('/user/status/{id}', [UserController::class, 'status'])->name('user.status')->middleware('permission:user-status-update');
    Route::post('/user/workingstatus/{id}', [UserController::class, 'workingstatus'])->name('user.workingstatus')->middleware('permission:user-working-status');
    //=== Role Api Route ===//

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:role-list');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:role-create');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:role-create');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:role-edit');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:role-show');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:role-edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:role-delete');

    //=== Permission Api Route ===//

    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:permission-list');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:permission-create');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:permission-create');
    Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:permission-edit');
    Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:permission-edit');
    Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:permission-delete');

});





