<?php

use App\Http\Controllers\AppSettings\PermissionController;
use App\Http\Controllers\AppSettings\RoleController;
use App\Http\Controllers\AppSettings\UserRoleController;
use Illuminate\Support\Facades\Route;



Route::prefix('app-settings')->name('app-settings.')->group(function () {
    Route::resource('roles', RoleController::class)->middleware(['auth', 'verified', 'permission:Manage Roles']);
    Route::resource('permissions', PermissionController::class)->middleware(['auth', 'verified', 'permission:Manage Permissions']);
    Route::prefix('users')->name('users.')->middleware(['auth', 'permission:Manage roles'])->group(function () {
        Route::get('/', [UserRoleController::class, 'index'])->name('index');
        Route::get('/{user}/edit', [UserRoleController::class, 'edit'])->name('edit');
        Route::put('/{user}/update', [UserRoleController::class, 'update'])->name('update');
    });
});
