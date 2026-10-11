<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BootstrapController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('helpdesk.index');
})->name('helpdesk.index');

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/api/me', [AuthController::class, 'me']);
    Route::get('/api/bootstrap', [BootstrapController::class, 'index']);

    Route::get('/api/notifications', [NotificationController::class, 'index']);
    Route::post('/api/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllRead']);

    Route::apiResource('api/tickets', TicketController::class)->parameters(['tickets' => 'ticket']);
    Route::post('/api/tickets/{ticket}/reply', [TicketController::class, 'reply']);
    Route::patch('/api/tickets/{ticket}/close', [TicketController::class, 'close']);

    Route::apiResource('api/roles', RoleController::class)->parameters(['roles' => 'role']);
    Route::put('/api/roles/{role}/permissions', [RoleController::class, 'updatePermissions']);

    Route::apiResource('api/users', UserController::class)->parameters(['users' => 'user']);

    Route::get('/api/settings', [SettingController::class, 'show']);
    Route::put('/api/settings/{section}', [SettingController::class, 'update']);

    // Read-only, sumber datanya db_indukk (lihat DivisiController/KaryawanController).
    Route::get('/api/divisi', [DivisiController::class, 'index']);
    Route::get('/api/karyawan', [KaryawanController::class, 'index']);
});
